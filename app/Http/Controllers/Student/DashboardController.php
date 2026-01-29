<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display student dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $year = $request->year ?? now()->year;

        $stats = $this->analyticsService->getAggregateStats($user, $year);

        // Get enrollments with recent evaluations
        $enrollments = $user->enrollments()
            ->active()
            ->with(['subject', 'program', 'evaluations' => fn($q) => $q->where('year', $year)->orderByDesc('week_number')->limit(5)])
            ->get();

        return view('student.dashboard', compact('user', 'stats', 'enrollments', 'year'));
    }

    /**
     * Show progress for a specific enrollment.
     */
    public function progress(Request $request, Enrollment $enrollment)
    {
        $user = $request->user();
        $year = $request->year ?? now()->year;

        // Ensure student can only view their own enrollments
        if ($enrollment->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $enrollment->load(['subject', 'program']);

        $weeklyTrend = $this->analyticsService->getWeeklyTrend($enrollment, $year);
        $componentAverages = $this->analyticsService->getAverageComponentScores($enrollment, $year);

        // Get latest evaluation
        $latestEvaluation = $enrollment->evaluations()
            ->where('year', $year)
            ->orderByDesc('week_number')
            ->first();

        $componentBreakdown = $latestEvaluation 
            ? $this->analyticsService->getComponentBreakdown($latestEvaluation)
            : [];

        return view('student.progress', compact(
            'enrollment',
            'weeklyTrend',
            'componentAverages',
            'componentBreakdown',
            'latestEvaluation',
            'year'
        ));
    }

    /**
     * Show evaluation details.
     */
    public function evaluation(Request $request, Enrollment $enrollment, $evaluationId)
    {
        $user = $request->user();

        // Ensure student can only view their own evaluations
        if ($enrollment->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $evaluation = $enrollment->evaluations()
            ->with(['details.component'])
            ->findOrFail($evaluationId);

        return view('student.evaluation', compact('enrollment', 'evaluation'));
    }
}
