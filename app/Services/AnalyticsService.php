<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    /**
     * Get weekly trend data for a student enrollment.
     */
    public function getWeeklyTrend(Enrollment $enrollment, int $year = null): array
    {
        $year = $year ?? now()->year;

        $cacheKey = "analytics.weekly_trend.{$enrollment->id}.{$year}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($enrollment, $year) {
            $evaluations = $enrollment->evaluations()
                ->where('year', $year)
                ->orderBy('week_number')
                ->get();

            return $evaluations->map(function ($evaluation) {
                return [
                    'week' => $evaluation->week_number,
                    'year' => $evaluation->year,
                    'total_score' => $evaluation->total_score,
                    'evaluation_date' => $evaluation->evaluation_date->format('Y-m-d'),
                    'is_locked' => $evaluation->is_locked,
                ];
            })->toArray();
        });
    }

    /**
     * Get component breakdown for an evaluation.
     */
    public function getComponentBreakdown(Evaluation $evaluation): array
    {
        $cacheKey = "analytics.component_breakdown.{$evaluation->id}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($evaluation) {
            return $evaluation->details()
                ->with('component')
                ->get()
                ->map(function ($detail) {
                    return [
                        'component_id' => $detail->component_id,
                        'component_name' => $detail->component->name,
                        'weight' => $detail->component->weight,
                        'score' => $detail->score,
                        'time_spent_minutes' => $detail->time_spent_minutes,
                        'total_questions' => $detail->total_questions,
                        'attempted_questions' => $detail->attempted_questions,
                        'correct_questions' => $detail->correct_questions,
                        'attempt_rate' => $detail->attempt_rate,
                        'accuracy_rate' => $detail->accuracy_rate,
                        'efficiency_rate' => $detail->efficiency_rate,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get average component scores over multiple weeks.
     */
    public function getAverageComponentScores(Enrollment $enrollment, int $year = null): array
    {
        $year = $year ?? now()->year;
        $cacheKey = "analytics.component_averages.{$enrollment->id}.{$year}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($enrollment, $year) {
            $evaluations = $enrollment->evaluations()
                ->where('year', $year)
                ->with('details.component')
                ->get();

            if ($evaluations->isEmpty()) {
                return [];
            }

            $componentAverages = [];
            
            foreach ($evaluations as $evaluation) {
                foreach ($evaluation->details as $detail) {
                    $componentId = $detail->component_id;
                    if (!isset($componentAverages[$componentId])) {
                        $componentAverages[$componentId] = [
                            'component_name' => $detail->component->name,
                            'weight' => $detail->component->weight,
                            'scores' => [],
                            'times' => [],
                            'attempt_rates' => [],
                            'accuracy_rates' => [],
                        ];
                    }
                    
                    if ($detail->score !== null) {
                        $componentAverages[$componentId]['scores'][] = $detail->score;
                    }
                    if ($detail->time_spent_minutes !== null) {
                        $componentAverages[$componentId]['times'][] = $detail->time_spent_minutes;
                    }
                    if ($detail->attempt_rate !== null) {
                        $componentAverages[$componentId]['attempt_rates'][] = $detail->attempt_rate;
                    }
                    if ($detail->accuracy_rate !== null) {
                        $componentAverages[$componentId]['accuracy_rates'][] = $detail->accuracy_rate;
                    }
                }
            }

            // Calculate averages
            return collect($componentAverages)->map(function ($data) {
                return [
                    'component_name' => $data['component_name'],
                    'weight' => $data['weight'],
                    'avg_score' => !empty($data['scores']) ? round(array_sum($data['scores']) / count($data['scores']), 2) : null,
                    'avg_time' => !empty($data['times']) ? round(array_sum($data['times']) / count($data['times']), 2) : null,
                    'avg_attempt_rate' => !empty($data['attempt_rates']) ? round(array_sum($data['attempt_rates']) / count($data['attempt_rates']), 2) : null,
                    'avg_accuracy_rate' => !empty($data['accuracy_rates']) ? round(array_sum($data['accuracy_rates']) / count($data['accuracy_rates']), 2) : null,
                    'evaluation_count' => count($data['scores']),
                ];
            })->values()->toArray();
        });
    }

    /**
     * Compare students in a subject.
     */
    public function compareStudents(Subject $subject, int $year = null): array
    {
        $year = $year ?? now()->year;
        $cacheKey = "analytics.compare_students.{$subject->id}.{$year}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($subject, $year) {
            $enrollments = $subject->enrollments()
                ->active()
                ->whereHas('user', fn($q) => $q->active())
                ->with(['user', 'evaluations' => function ($query) use ($year) {
                    $query->where('year', $year);
                }])
                ->get();

            return $enrollments->map(function ($enrollment) {
                $evaluations = $enrollment->evaluations;
                $avgScore = $evaluations->avg('total_score');
                $latestScore = $evaluations->first()?->total_score;
                $evaluationCount = $evaluations->count();

                // Calculate trend (comparing last 2 evaluations)
                $trend = 'stable';
                if ($evaluationCount >= 2) {
                    $sorted = $evaluations->sortByDesc('week_number')->values();
                    $latest = $sorted[0]->total_score ?? 0;
                    $previous = $sorted[1]->total_score ?? 0;
                    
                    if ($latest > $previous + 5) {
                        $trend = 'improving';
                    } elseif ($latest < $previous - 5) {
                        $trend = 'declining';
                    }
                }

                return [
                    'student_id' => $enrollment->user_id,
                    'student_name' => $enrollment->user->name,
                    'average_score' => $avgScore ? round($avgScore, 2) : null,
                    'latest_score' => $latestScore,
                    'evaluation_count' => $evaluationCount,
                    'trend' => $trend,
                ];
            })->sortByDesc('average_score')->values()->toArray();
        });
    }

    /**
     * Get weekly score series for each student in a subject.
     */
    public function getSubjectWeeklyComparison(Subject $subject, int $year = null, ?string $timeframe = null): array
    {
        $year = $year ?? now()->year;
        $timeframe = $timeframe ?: '12w';
        $cacheKey = "analytics.subject_weekly_comparison.{$subject->id}.{$year}.{$timeframe}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($subject, $year, $timeframe) {
            $dateRange = $this->resolveTimeframeRange($subject, $year, $timeframe);
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];

            $enrollments = $subject->enrollments()
                ->active()
                ->whereHas('user', fn($q) => $q->active())
                ->with(['user', 'evaluations' => function ($query) use ($year, $startDate, $endDate) {
                    $query->where('year', $year)
                        ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                            $q->whereBetween('evaluation_date', [$startDate, $endDate]);
                        })
                        ->orderBy('week_number');
                }])
                ->get();

            $allWeeks = [];
            $cursor = Carbon::parse($startDate)->startOfWeek();
            $endCursor = Carbon::parse($endDate)->endOfWeek();
            while ($cursor <= $endCursor) {
                if ((int) $cursor->format('Y') === $year) {
                    $allWeeks[] = $cursor->weekOfYear;
                }
                $cursor->addWeek();
            }
            $allWeeks = collect($allWeeks)->unique()->sort()->values()->all();

            $series = $enrollments->map(function ($enrollment) use ($allWeeks) {
                $scoresByWeek = $enrollment->evaluations
                    ->pluck('total_score', 'week_number')
                    ->toArray();

                $data = array_map(function ($week) use ($scoresByWeek) {
                    return $scoresByWeek[$week] ?? null;
                }, $allWeeks);

                return [
                    'student_id' => $enrollment->user_id,
                    'student_name' => $enrollment->user->name,
                    'data' => $data,
                ];
            })->values()->toArray();

            return [
                'weeks' => $allWeeks,
                'series' => $series,
                'timeframe' => $timeframe,
            ];
        });
    }

    /**
     * Resolve start/end date range based on timeframe.
     */
    private function resolveTimeframeRange(Subject $subject, int $year, string $timeframe): array
    {
        $latestEvaluation = Evaluation::where('year', $year)
            ->whereHas('enrollment', fn($q) => $q->where('subject_id', $subject->id))
            ->orderByDesc('evaluation_date')
            ->first();

        $end = $latestEvaluation?->evaluation_date ? Carbon::parse($latestEvaluation->evaluation_date) : now();

        // Keep end within the selected year if possible
        if ((int) $end->format('Y') !== $year) {
            $end = Carbon::create($year, 12, 31);
        }

        $start = match ($timeframe) {
            '4w' => $end->copy()->subWeeks(4),
            '8w' => $end->copy()->subWeeks(8),
            '12w' => $end->copy()->subWeeks(12),
            '1m' => $end->copy()->subMonths(1),
            '3m' => $end->copy()->subMonths(3),
            '6m' => $end->copy()->subMonths(6),
            '12m' => $end->copy()->subMonths(12),
            default => $end->copy()->subWeeks(12),
        };

        // Clamp to selected year
        if ((int) $start->format('Y') < $year) {
            $start = Carbon::create($year, 1, 1);
        }

        return [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
        ];
    }

    /**
     * Get aggregate statistics for dashboard.
     */
    public function getAggregateStats(User $user, int $year = null): array
    {
        $year = $year ?? now()->year;
        $currentWeek = now()->weekOfYear;

        $cacheKey = "analytics.aggregate_stats.{$user->id}.{$year}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user, $year) {
            // Different stats based on user role
            if ($user->isStudent()) {
                return $this->getStudentStats($user, $year);
            }

        if ($user->isPembina()) {
            return $this->getPembinaStats($user, $year);
        }

            return $this->getAdminStats($year);
        });
    }

    /**
     * Get stats for student dashboard.
     */
    private function getStudentStats(User $student, int $year): array
    {
        $enrollments = $student->enrollments()->active()->get();
        
        $stats = [
            'total_subjects' => $enrollments->count(),
            'evaluations_this_year' => 0,
            'average_score' => null,
            'latest_scores' => [],
        ];

        foreach ($enrollments as $enrollment) {
            $evaluations = $enrollment->evaluations()->where('year', $year)->get();
            $stats['evaluations_this_year'] += $evaluations->count();
            
            $latest = $evaluations->first();
            if ($latest) {
                $stats['latest_scores'][] = [
                    'subject' => $enrollment->subject->name,
                    'score' => $latest->total_score,
                    'week' => $latest->week_number,
                ];
            }
        }

        // Calculate overall average
        $allScores = $student->enrollments()
            ->with(['evaluations' => fn($q) => $q->where('year', $year)])
            ->get()
            ->pluck('evaluations')
            ->flatten()
            ->pluck('total_score')
            ->filter();

        $stats['average_score'] = $allScores->isNotEmpty() 
            ? round($allScores->avg(), 2) 
            : null;

        return $stats;
    }

    /**
     * Get stats for pembina dashboard.
     */
    private function getPembinaStats(User $pembina, int $year): array
    {
        $currentWeek = now()->weekOfYear;
        $subjectIds = $pembina->pembinaSubjects()->pluck('subjects.id');

        $totalEnrollments = Enrollment::active()
            ->whereIn('subject_id', $subjectIds)
            ->count();
        $evaluationsThisWeek = Evaluation::where('year', $year)
            ->where('week_number', $currentWeek)
            ->whereHas('enrollment', fn($q) => $q->whereIn('subject_id', $subjectIds))
            ->count();
        
        $pendingCount = $totalEnrollments - $evaluationsThisWeek;
        
        $averageScore = Evaluation::where('year', $year)
            ->whereNotNull('total_score')
            ->whereHas('enrollment', fn($q) => $q->whereIn('subject_id', $subjectIds))
            ->avg('total_score');

        return [
            'total_students' => $totalEnrollments,
            'evaluations_this_week' => $evaluationsThisWeek,
            'pending_evaluations' => max(0, $pendingCount),
            'average_score' => $averageScore ? round($averageScore, 2) : null,
        ];
    }

    /**
     * Get stats for admin dashboard.
     */
    private function getAdminStats(int $year): array
    {
        return [
            'total_programs' => \App\Models\Program::active()->count(),
            'total_subjects' => Subject::active()->count(),
            'total_students' => User::whereHas('roles', fn($q) => $q->where('name', 'student'))->active()->count(),
            'total_evaluations' => Evaluation::where('year', $year)->count(),
            'average_score' => round(Evaluation::where('year', $year)->avg('total_score') ?? 0, 2),
        ];
    }
}
