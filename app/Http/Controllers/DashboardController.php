<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the main dashboard based on user level.
     */
    public function index()
    {
        $user = $this->currentUser();

        // Developer gets full dashboard with system health
        if ($this->isDeveloper()) {
            $data = $this->dashboardService->getDeveloperDashboardData();
            return view('index.children_views.dashboard', $data);
        }

        // Admin and user get basic dashboard
        return view('index.children_views.dashboard', [
            'totalTickets' => 0,
            'completedTickets' => 0,
            'completionRate' => 0,
            'OGPTickets' => 0,
            'RegistTickets' => 0,
            'dbStatus' => ['label' => 'Active', 'class' => 'success', 'latency' => '--'],
            'storageStatus' => ['label' => 'Stable', 'class' => 'success', 'latency' => '--'],
            'envStatus' => ['label' => 'STABLE', 'class' => 'success', 'latency' => '--'],
        ]);
    }
}
