<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Display a listing of enrollments.
     */
    public function index(Request $request)
    {
        $subjectIds = $this->scopedSubjectIds($request->user());

        $enrollments = Enrollment::with(['user', 'program', 'subject'])
            ->when($subjectIds !== null, fn($q) => $q->whereIn('subject_id', $subjectIds))
            ->when($request->program_id, fn($q, $id) => $q->where('program_id', $id))
            ->when($request->subject_id, fn($q, $id) => $q->where('subject_id', $id))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderByDesc('enrolled_at')
            ->paginate(15);

        $programs = Program::active()
            ->when($subjectIds !== null, function ($query) use ($subjectIds) {
                $query->whereHas('subjects', fn($q) => $q->whereIn('subjects.id', $subjectIds));
            })
            ->orderBy('name')
            ->get();
        $subjects = Subject::active()
            ->when($subjectIds !== null, fn($q) => $q->whereIn('id', $subjectIds))
            ->orderBy('name')
            ->get();

        return view('admin.enrollments.index', compact('enrollments', 'programs', 'subjects'));
    }

    /**
     * Show the form for creating a new enrollment.
     */
    public function create(Request $request)
    {
        $studentRole = Role::where('name', 'student')->first();
        $students = User::whereHas('roles', fn($q) => $q->where('role_id', $studentRole->id))
            ->active()
            ->orderBy('name')
            ->get();

        $subjectIds = $this->scopedSubjectIds($request->user());
        $subjects = Subject::active()
            ->with('program')
            ->when($subjectIds !== null, fn($q) => $q->whereIn('id', $subjectIds))
            ->orderBy('name')
            ->get();

        $programIds = $subjects->pluck('program_id')->unique()->values()->all();
        $programs = Program::active()
            ->when($subjectIds !== null, fn($q) => $q->whereIn('id', $programIds))
            ->orderBy('name')
            ->get();

        return view('admin.enrollments.create', compact('students', 'programs', 'subjects'));
    }

    /**
     * Store a newly created enrollment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'program_id' => 'required|exists:programs,id',
            'subject_id' => 'required|exists:subjects,id',
            'enrolled_at' => 'required|date',
        ]);

        $subject = Subject::active()->findOrFail($validated['subject_id']);
        $this->ensureSubjectInScope($subject, $request->user());

        // Ensure subject belongs to the chosen program.
        if ((int) $validated['program_id'] !== (int) $subject->program_id) {
            return back()->withErrors(['program_id' => 'Program tidak sesuai dengan mata pelajaran yang dipilih.']);
        }

        // Check if enrollment already exists
        $exists = Enrollment::where('user_id', $validated['user_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['user_id' => 'Siswa sudah terdaftar pada mata pelajaran ini.']);
        }

        $validated['status'] = 'active';
        $validated['program_id'] = $subject->program_id;
        $enrollment = Enrollment::create($validated);
        $this->auditService->logCreated($enrollment);

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Pendaftaran berhasil dibuat.');
    }

    /**
     * Display the specified enrollment.
     */
    public function show(Enrollment $enrollment)
    {
        $this->ensureSubjectInScope($enrollment->subject, request()->user());
        $enrollment->load(['user', 'program', 'subject', 'evaluations.details']);

        return view('admin.enrollments.show', compact('enrollment'));
    }

    /**
     * Toggle enrollment status.
     */
    public function toggleStatus(Enrollment $enrollment)
    {
        $this->ensureSubjectInScope($enrollment->subject, request()->user());
        $oldValues = $enrollment->toArray();
        
        if ($enrollment->isActive()) {
            $enrollment->deactivate();
        } else {
            $enrollment->activate();
        }

        $this->auditService->logUpdated($enrollment, $oldValues);

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Status pendaftaran berhasil diubah.');
    }

    /**
     * Bulk enroll students to a subject.
     */
    public function bulkEnroll(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'program_id' => 'required|exists:programs,id',
            'subject_id' => 'required|exists:subjects,id',
            'enrolled_at' => 'required|date',
        ]);

        $subject = Subject::active()->findOrFail($validated['subject_id']);
        $this->ensureSubjectInScope($subject, $request->user());

        if ((int) $validated['program_id'] !== (int) $subject->program_id) {
            return back()->withErrors(['program_id' => 'Program tidak sesuai dengan mata pelajaran yang dipilih.']);
        }

        $enrolled = 0;
        $skipped = 0;

        foreach ($validated['user_ids'] as $userId) {
            $exists = Enrollment::where('user_id', $userId)
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if (!$exists) {
                $enrollment = Enrollment::create([
                    'user_id' => $userId,
                    'program_id' => $subject->program_id,
                    'subject_id' => $subject->id,
                    'enrolled_at' => $validated['enrolled_at'],
                    'status' => 'active',
                ]);
                $this->auditService->logCreated($enrollment);
                $enrolled++;
            } else {
                $skipped++;
            }
        }

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', "{$enrolled} siswa berhasil didaftarkan. {$skipped} dilewati (sudah terdaftar).");
    }

    /**
     * Get scoped subject IDs for restricted pembina users.
     */
    private function scopedSubjectIds(User $user): ?array
    {
        if ($user->isPembina() && !$user->isAdmin() && !$user->isSuperadmin()) {
            return $user->pembinaSubjects()->pluck('subjects.id')->toArray();
        }

        return null;
    }

    /**
     * Ensure the given subject is within the user's scope.
     */
    private function ensureSubjectInScope(Subject $subject, User $user): void
    {
        $subjectIds = $this->scopedSubjectIds($user);
        if ($subjectIds !== null && !in_array($subject->id, $subjectIds, true)) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
        }
    }
}
