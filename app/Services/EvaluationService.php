<?php

namespace App\Services;

use App\Models\Component;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class EvaluationService
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Create a new evaluation with details.
     */
    public function createEvaluation(
        Enrollment $enrollment,
        User $evaluator,
        int $weekNumber,
        int $year,
        array $componentScores,
        ?string $notes = null
    ): Evaluation {
        return DB::transaction(function () use ($enrollment, $evaluator, $weekNumber, $year, $componentScores, $notes) {
            // Create the evaluation
            $evaluation = Evaluation::create([
                'enrollment_id' => $enrollment->id,
                'evaluator_id' => $evaluator->id,
                'week_number' => $weekNumber,
                'year' => $year,
                'evaluation_date' => now(),
                'notes' => $notes,
                'is_locked' => false,
            ]);

            // Create evaluation details for each component
            foreach ($componentScores as $componentId => $data) {
                EvaluationDetail::create([
                    'evaluation_id' => $evaluation->id,
                    'component_id' => $componentId,
                    'score' => $data['score'] ?? null,
                    'time_spent_minutes' => $data['time_spent_minutes'] ?? null,
                    'total_questions' => $data['total_questions'] ?? null,
                    'attempted_questions' => $data['attempted_questions'] ?? null,
                    'correct_questions' => $data['correct_questions'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);
            }

            // Calculate total weighted score
            $evaluation->calculateTotalScore();

            // Log the creation
            $this->auditService->logCreated($evaluation);

            $this->clearAnalyticsCache($evaluation);

            return $evaluation->fresh(['details']);
        });
    }

    /**
     * Update an existing evaluation.
     */
    public function updateEvaluation(
        Evaluation $evaluation,
        User $user,
        array $componentScores,
        ?string $notes = null
    ): Evaluation {
        // Check if evaluation can be edited
        if (!$evaluation->canBeEditedBy($user)) {
            throw new \Exception('Evaluasi hanya dapat diedit pada hari yang sama atau oleh superadmin (jika belum dikunci).');
        }

        return DB::transaction(function () use ($evaluation, $componentScores, $notes) {
            $oldValues = $evaluation->toArray();

            // Update notes if provided
            if ($notes !== null) {
                $evaluation->update(['notes' => $notes]);
            }

            // Update evaluation details
            foreach ($componentScores as $componentId => $data) {
                $detail = $evaluation->details()->where('component_id', $componentId)->first();
                
                if ($detail) {
                    $detail->update([
                        'score' => $data['score'] ?? $detail->score,
                        'time_spent_minutes' => $data['time_spent_minutes'] ?? $detail->time_spent_minutes,
                        'total_questions' => $data['total_questions'] ?? $detail->total_questions,
                        'attempted_questions' => $data['attempted_questions'] ?? $detail->attempted_questions,
                        'correct_questions' => $data['correct_questions'] ?? $detail->correct_questions,
                        'notes' => $data['notes'] ?? $detail->notes,
                    ]);
                } else {
                    // Create new detail if component didn't exist before
                    EvaluationDetail::create([
                        'evaluation_id' => $evaluation->id,
                        'component_id' => $componentId,
                        'score' => $data['score'] ?? null,
                        'time_spent_minutes' => $data['time_spent_minutes'] ?? null,
                        'total_questions' => $data['total_questions'] ?? null,
                        'attempted_questions' => $data['attempted_questions'] ?? null,
                        'correct_questions' => $data['correct_questions'] ?? null,
                        'notes' => $data['notes'] ?? null,
                    ]);
                }
            }

            // Recalculate total score
            $evaluation->calculateTotalScore();

            // Log the update
            $this->auditService->logUpdated($evaluation, $oldValues);

            $this->clearAnalyticsCache($evaluation);

            return $evaluation->fresh(['details']);
        });
    }

    /**
     * Lock an evaluation.
     */
    public function lockEvaluation(Evaluation $evaluation, User $user, ?string $reason = null): void
    {
        $evaluation->lock($user);
        $this->auditService->logLocked($evaluation, $reason);
        $this->clearAnalyticsCache($evaluation);
    }

    /**
     * Unlock an evaluation (superadmin only).
     */
    public function unlockEvaluation(Evaluation $evaluation, User $user, string $reason): void
    {
        if (!$user->isSuperadmin()) {
            throw new \Exception('Only Superadmin can unlock evaluations.');
        }

        $evaluation->unlock();
        $this->auditService->logUnlocked($evaluation, $reason);
        $this->clearAnalyticsCache($evaluation);
    }

    /**
     * Clear analytics cache for the given evaluation.
     */
    protected function clearAnalyticsCache(Evaluation $evaluation): void
    {
        $enrollmentId = $evaluation->enrollment_id;
        $subjectId = $evaluation->enrollment->subject_id;
        $year = $evaluation->year;

        Cache::forget("analytics.weekly_trend.{$enrollmentId}.{$year}");
        Cache::forget("analytics.component_averages.{$enrollmentId}.{$year}");
        Cache::forget("analytics.compare_students.{$subjectId}.{$year}");
        $timeframes = ['4w', '8w', '12w', '1m', '3m', '6m', '12m'];
        foreach ($timeframes as $timeframe) {
            Cache::forget("analytics.subject_weekly_comparison.{$subjectId}.{$year}.{$timeframe}");
        }
        Cache::forget("analytics.component_breakdown.{$evaluation->id}");
        Cache::forget("analytics.aggregate_stats.{$evaluation->enrollment->user_id}.{$year}");
    }

    /**
     * Get pending evaluations for a pembina.
     */
    public function getPendingEvaluations(User $pembina, int $year = null): array
    {
        $year = $year ?? now()->year;
        $currentWeek = now()->weekOfYear;
        
        // Get all active enrollments for subjects this pembina handles
        $enrollments = Enrollment::active()
            ->whereHas('user', fn($q) => $q->active())
            ->when(!$pembina->isSuperadmin(), function ($query) use ($pembina) {
                $query->whereHas('subject.pembinas', function ($q) use ($pembina) {
                    $q->where('users.id', $pembina->id);
                });
            })
            ->with(['user', 'subject', 'program'])
            ->get();

        $pending = [];
        foreach ($enrollments as $enrollment) {
            $pendingWeeks = $enrollment->getPendingWeeks($year);
            if (!empty($pendingWeeks)) {
                $pending[] = [
                    'enrollment' => $enrollment,
                    'pending_weeks' => $pendingWeeks,
                ];
            }
        }

        return $pending;
    }
}
