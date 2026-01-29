<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Subject;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Display a listing of subjects.
     */
    public function index(Request $request)
    {
        $subjectIds = $this->scopedSubjectIds($request->user());

        $subjects = Subject::with('program')
            ->withCount(['components', 'enrollments'])
            ->when($subjectIds !== null, fn($q) => $q->whereIn('id', $subjectIds))
            ->when($request->program_id, fn($q, $id) => $q->where('program_id', $id))
            ->orderBy('name')
            ->paginate(10);

        $programs = Program::active()
            ->when($subjectIds !== null, function ($query) use ($subjectIds) {
                $query->whereHas('subjects', fn($q) => $q->whereIn('subjects.id', $subjectIds));
            })
            ->orderBy('name')
            ->get();

        return view('admin.subjects.index', compact('subjects', 'programs'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        $programs = Program::active()->orderBy('name')->get();

        return view('admin.subjects.create', compact('programs'));
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Check unique code within program
        $exists = Subject::where('program_id', $validated['program_id'])
            ->where('code', $validated['code'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['code' => 'Kode mata pelajaran sudah digunakan dalam program ini.']);
        }

        $subject = Subject::create($validated);
        $this->auditService->logCreated($subject);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil dibuat.');
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        $this->ensureSubjectInScope($subject, request()->user());
        $subject->load(['program', 'components', 'enrollments.user']);

        return view('admin.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject)
    {
        $this->ensureSubjectInScope($subject, request()->user());
        $subjectIds = $this->scopedSubjectIds(request()->user());
        $programs = Program::active()
            ->when($subjectIds !== null, fn($q) => $q->where('id', $subject->program_id))
            ->orderBy('name')
            ->get();

        return view('admin.subjects.edit', compact('subject', 'programs'));
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, Subject $subject)
    {
        $this->ensureSubjectInScope($subject, $request->user());
        $subjectIds = $this->scopedSubjectIds($request->user());
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Restricted pembina cannot move subjects across programs.
        if ($subjectIds !== null) {
            $validated['program_id'] = $subject->program_id;
        }

        // Check unique code within program (excluding self)
        $exists = Subject::where('program_id', $validated['program_id'])
            ->where('code', $validated['code'])
            ->where('id', '!=', $subject->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['code' => 'Kode mata pelajaran sudah digunakan dalam program ini.']);
        }

        $oldValues = $subject->toArray();
        $subject->update($validated);
        $this->auditService->logUpdated($subject, $oldValues);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Toggle subject active status.
     */
    public function toggleStatus(Subject $subject)
    {
        $this->ensureSubjectInScope($subject, request()->user());
        $oldValues = $subject->toArray();
        $subject->update(['is_active' => !$subject->is_active]);
        $this->auditService->logUpdated($subject, $oldValues);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Status mata pelajaran berhasil diubah.');
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject)
    {
        $this->ensureSubjectInScope($subject, request()->user());
        if ($subject->hasHistoricalData()) {
            return redirect()
                ->route('admin.subjects.index')
                ->with('error', 'Mata pelajaran dengan data historis tidak dapat dihapus.');
        }

        $this->auditService->logDeleted($subject);
        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
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
