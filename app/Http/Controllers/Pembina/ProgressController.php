<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display student progress overview.
     */
    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;

        $subjects = Subject::active()
            ->when(!$request->user()->isSuperadmin(), function ($query) use ($request) {
                $query->whereHas('pembinas', function ($q) use ($request) {
                    $q->where('users.id', $request->user()->id);
                });
            })
            ->with(['program', 'enrollments' => fn($q) => $q->active()->whereHas('user', fn($u) => $u->active())->with('user')])
            ->orderBy('name')
            ->get();

        return view('pembina.progress.index', compact('subjects', 'year'));
    }

    /**
     * Show progress for a specific subject.
     */
    public function subject(Request $request, Subject $subject)
    {
        $year = $request->year ?? now()->year;

        if (!$request->user()->isSuperadmin()) {
            $isAssigned = $subject->pembinas()
                ->where('users.id', $request->user()->id)
                ->exists();
            if (!$isAssigned) {
                abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
            }
        }

        $timeframe = $request->timeframe ?? '12w';
        $comparison = $this->analyticsService->compareStudents($subject, $year);
        $weeklyComparison = $this->analyticsService->getSubjectWeeklyComparison($subject, $year, $timeframe);

        return view('pembina.progress.subject', compact('subject', 'comparison', 'weeklyComparison', 'year', 'timeframe'));
    }

    /**
     * Show progress for a specific student enrollment.
     */
    public function student(Request $request, Enrollment $enrollment)
    {
        $year = $request->year ?? now()->year;

        $enrollment->load(['user', 'subject', 'program']);

        if (!$request->user()->isSuperadmin()) {
            $isAssigned = $enrollment->subject->pembinas()
                ->where('users.id', $request->user()->id)
                ->exists();
            if (!$isAssigned) {
                abort(403, 'Anda tidak memiliki akses ke data siswa ini.');
            }
        }

        $weeklyTrend = $this->analyticsService->getWeeklyTrend($enrollment, $year);
        $componentAverages = $this->analyticsService->getAverageComponentScores($enrollment, $year);

        // Get latest evaluation for component breakdown
        $latestEvaluation = $enrollment->evaluations()
            ->where('year', $year)
            ->orderByDesc('week_number')
            ->first();

        $componentBreakdown = $latestEvaluation 
            ? $this->analyticsService->getComponentBreakdown($latestEvaluation)
            : [];

        return view('pembina.progress.student', compact(
            'enrollment',
            'weeklyTrend',
            'componentAverages',
            'componentBreakdown',
            'latestEvaluation',
            'year'
        ));
    }

    /**
     * Get chart data as JSON for AJAX requests.
     */
    public function chartData(Request $request, Enrollment $enrollment)
    {
        $year = $request->year ?? now()->year;

        if (!$request->user()->isSuperadmin()) {
            $isAssigned = $enrollment->subject->pembinas()
                ->where('users.id', $request->user()->id)
                ->exists();
            if (!$isAssigned) {
                abort(403, 'Anda tidak memiliki akses ke data siswa ini.');
            }
        }

        $weeklyTrend = $this->analyticsService->getWeeklyTrend($enrollment, $year);
        $componentAverages = $this->analyticsService->getAverageComponentScores($enrollment, $year);

        return response()->json([
            'weekly_trend' => $weeklyTrend,
            'component_averages' => $componentAverages,
        ]);
    }
}
