<?php

namespace App\Http\Controllers;

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
     * Show the dashboard based on user role.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $stats = $this->analyticsService->getAggregateStats($user);

        // Redirect to role-specific dashboard
        if ($user->isSuperadmin() || $user->isAdmin()) {
            return view('dashboard.admin', compact('stats', 'user'));
        }

        if ($user->isPembina()) {
            return view('dashboard.pembina', compact('stats', 'user'));
        }

        if ($user->isStudent()) {
            return view('dashboard.student', compact('stats', 'user'));
        }

        return view('dashboard.index', compact('stats', 'user'));
    }
}
