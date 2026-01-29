<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\Subject;
use App\Services\AuditService;
use App\Services\EvaluationService;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    protected EvaluationService $evaluationService;
    protected AuditService $auditService;

    public function __construct(EvaluationService $evaluationService, AuditService $auditService)
    {
        $this->evaluationService = $evaluationService;
        $this->auditService = $auditService;
    }

    /**
     * Display a listing of evaluations.
     */
    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;
        $week = $request->week ?? now()->weekOfYear;

        $evaluations = Evaluation::with(['enrollment.user', 'enrollment.subject', 'evaluator'])
            ->when(!$request->user()->isSuperadmin(), function ($query) use ($request) {
                $query->whereHas('enrollment.subject.pembinas', function ($q) use ($request) {
                    $q->where('users.id', $request->user()->id);
                });
            })
            ->when($request->subject_id, fn($q, $id) => $q->whereHas('enrollment', fn($e) => $e->where('subject_id', $id)))
            ->when($request->year, fn($q, $y) => $q->where('year', $y))
            ->when($request->week, fn($q, $w) => $q->where('week_number', $w))
            ->orderByDesc('created_at')
            ->paginate(15);

        $subjects = Subject::active()
            ->when(!$request->user()->isSuperadmin(), function ($query) use ($request) {
                $query->whereHas('pembinas', function ($q) use ($request) {
                    $q->where('users.id', $request->user()->id);
                });
            })
            ->orderBy('name')
            ->get();

        // Get pending evaluations
        $pending = $this->evaluationService->getPendingEvaluations($request->user(), $year);

        return view('pembina.evaluations.index', compact('evaluations', 'subjects', 'pending', 'year', 'week'));
    }

    /**
     * Show the form for creating a new evaluation.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'week' => 'required|integer|min:1|max:53',
            'year' => 'required|integer|min:2020',
        ]);

        $enrollment = Enrollment::with(['user', 'subject.activeComponents', 'program'])
            ->findOrFail($validated['enrollment_id']);

        if (!$request->user()->isSuperadmin()) {
            $isAssigned = $enrollment->subject->pembinas()
                ->where('users.id', $request->user()->id)
                ->exists();
            if (!$isAssigned) {
                abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
            }
        }

        // Check if evaluation already exists
        $exists = Evaluation::where('enrollment_id', $enrollment->id)
            ->where('week_number', $validated['week'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return redirect()
                ->route('pembina.evaluations.index')
                ->with('error', 'Evaluasi untuk minggu ini sudah ada.');
        }

        $components = $enrollment->subject->activeComponents;
        $week = $validated['week'];
        $year = $validated['year'];

        return view('pembina.evaluations.create', compact('enrollment', 'components', 'week', 'year'));
    }

    /**
     * Store a newly created evaluation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'week_number' => 'required|integer|min:1|max:53',
            'year' => 'required|integer|min:2020',
            'notes' => 'nullable|string',
            'components' => 'required|array',
            'components.*.component_id' => 'required|exists:components,id',
            'components.*.score' => 'nullable|numeric|min:0|max:100',
            'components.*.time_spent_minutes' => 'nullable|integer|min:0',
            'components.*.total_questions' => 'nullable|integer|min:0',
            'components.*.attempted_questions' => 'nullable|integer|min:0',
            'components.*.correct_questions' => 'nullable|integer|min:0',
            'components.*.notes' => 'nullable|string',
        ]);

        $enrollment = Enrollment::findOrFail($validated['enrollment_id']);

        if (!$request->user()->isSuperadmin()) {
            $isAssigned = $enrollment->subject->pembinas()
                ->where('users.id', $request->user()->id)
                ->exists();
            if (!$isAssigned) {
                return back()->withErrors(['error' => 'Anda tidak memiliki akses ke mata pelajaran ini.']);
            }
        }

        // Format component scores
        $componentScores = [];
        foreach ($validated['components'] as $comp) {
            $componentScores[$comp['component_id']] = [
                'score' => $comp['score'] ?? null,
                'time_spent_minutes' => $comp['time_spent_minutes'] ?? null,
                'total_questions' => $comp['total_questions'] ?? null,
                'attempted_questions' => $comp['attempted_questions'] ?? null,
                'correct_questions' => $comp['correct_questions'] ?? null,
                'notes' => $comp['notes'] ?? null,
            ];
        }

        try {
            $evaluation = $this->evaluationService->createEvaluation(
                $enrollment,
                $request->user(),
                $validated['week_number'],
                $validated['year'],
                $componentScores,
                $validated['notes'] ?? null
            );

            return redirect()
                ->route('pembina.evaluations.show', $evaluation)
                ->with('success', 'Evaluasi berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified evaluation.
     */
    public function show(Evaluation $evaluation)
    {
        if (!request()->user()->isSuperadmin()) {
            $isAssigned = $evaluation->enrollment->subject->pembinas()
                ->where('users.id', request()->user()->id)
                ->exists();
            if (!$isAssigned) {
                abort(403, 'Anda tidak memiliki akses ke evaluasi ini.');
            }
        }

        $evaluation->load(['enrollment.user', 'enrollment.subject', 'enrollment.program', 'details.component', 'evaluator']);

        return view('pembina.evaluations.show', compact('evaluation'));
    }

    /**
     * Show the form for editing the specified evaluation.
     */
    public function edit(Evaluation $evaluation)
    {
        if (!request()->user()->isSuperadmin()) {
            $isAssigned = $evaluation->enrollment->subject->pembinas()
                ->where('users.id', request()->user()->id)
                ->exists();
            if (!$isAssigned) {
                abort(403, 'Anda tidak memiliki akses ke evaluasi ini.');
            }
        }

        if (!$evaluation->canBeEditedBy(request()->user())) {
            return redirect()
                ->route('pembina.evaluations.show', $evaluation)
                ->with('error', 'Evaluasi hanya dapat diedit pada hari yang sama.');
        }

        $evaluation->load(['enrollment.user', 'enrollment.subject.activeComponents', 'details']);
        $components = $evaluation->enrollment->subject->activeComponents;

        // Map existing details
        $existingDetails = $evaluation->details->keyBy('component_id');

        return view('pembina.evaluations.edit', compact('evaluation', 'components', 'existingDetails'));
    }

    /**
     * Update the specified evaluation.
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        if (!request()->user()->isSuperadmin()) {
            $isAssigned = $evaluation->enrollment->subject->pembinas()
                ->where('users.id', request()->user()->id)
                ->exists();
            if (!$isAssigned) {
                abort(403, 'Anda tidak memiliki akses ke evaluasi ini.');
            }
        }

        if (!$evaluation->canBeEditedBy($request->user())) {
            return redirect()
                ->route('pembina.evaluations.show', $evaluation)
                ->with('error', 'Evaluasi hanya dapat diedit pada hari yang sama.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
            'components' => 'required|array',
            'components.*.component_id' => 'required|exists:components,id',
            'components.*.score' => 'nullable|numeric|min:0|max:100',
            'components.*.time_spent_minutes' => 'nullable|integer|min:0',
            'components.*.total_questions' => 'nullable|integer|min:0',
            'components.*.attempted_questions' => 'nullable|integer|min:0',
            'components.*.correct_questions' => 'nullable|integer|min:0',
            'components.*.notes' => 'nullable|string',
        ]);

        // Format component scores
        $componentScores = [];
        foreach ($validated['components'] as $comp) {
            $componentScores[$comp['component_id']] = [
                'score' => $comp['score'] ?? null,
                'time_spent_minutes' => $comp['time_spent_minutes'] ?? null,
                'total_questions' => $comp['total_questions'] ?? null,
                'attempted_questions' => $comp['attempted_questions'] ?? null,
                'correct_questions' => $comp['correct_questions'] ?? null,
                'notes' => $comp['notes'] ?? null,
            ];
        }

        try {
            $evaluation = $this->evaluationService->updateEvaluation(
                $evaluation,
                $request->user(),
                $componentScores,
                $validated['notes'] ?? null
            );

            return redirect()
                ->route('pembina.evaluations.show', $evaluation)
                ->with('success', 'Evaluasi berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Lock evaluasi (pembina/superadmin).
     */
    public function lock(Request $request, Evaluation $evaluation)
    {
        if (!$request->user()->isSuperadmin()) {
            $isAssigned = $evaluation->enrollment->subject->pembinas()
                ->where('users.id', $request->user()->id)
                ->exists();
            if (!$isAssigned) {
                return redirect()
                    ->route('pembina.evaluations.show', $evaluation)
                    ->with('error', 'Anda tidak memiliki akses ke evaluasi ini.');
            }
        }

        if ($evaluation->is_locked) {
            return redirect()
                ->route('pembina.evaluations.show', $evaluation)
                ->with('error', 'Evaluasi ini sudah dikunci.');
        }

        try {
            $this->evaluationService->lockEvaluation(
                $evaluation,
                $request->user(),
                $request->input('reason')
            );

            return redirect()
                ->route('pembina.evaluations.show', $evaluation)
                ->with('success', 'Evaluasi berhasil dikunci.');
        } catch (\Exception $e) {
            return redirect()
                ->route('pembina.evaluations.show', $evaluation)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Select student and week for new evaluation.
     */
    public function selectStudent()
    {
        $user = request()->user();

        $subjects = Subject::active()
            ->when(!$user->isSuperadmin(), function ($query) use ($user) {
                $query->whereHas('pembinas', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            })
            ->with(['enrollments' => fn($q) => $q->active()->whereHas('user', fn($u) => $u->active())->with('user')])
            ->orderBy('name')
            ->get();

        $currentWeek = now()->weekOfYear;
        $currentYear = now()->year;

        return view('pembina.evaluations.select-student', compact('subjects', 'currentWeek', 'currentYear'));
    }
}
