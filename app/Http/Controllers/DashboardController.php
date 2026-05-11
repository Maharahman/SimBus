<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard based on user level.
     */
    public function index()
    {
        $user = Auth::user();

        // --- DEVELOPER ROLE LOGIC ---
        if ($user->level === 'developer') {

            // 1. Ticket Statistics using DB facade (Clears "Not enough arguments" errors)
            $totalTickets = DB::table('tickets')->count();

            $completedTickets = DB::table('tickets')
                ->whereIn('status', ['Closed', 'Approve', 'Reject'])
                ->count();

            $OGPTickets = DB::table('tickets')
                ->where('status', 'Progress')
                ->count();

            $OpenTickets = DB::table('tickets')
                ->where('status', 'Open')
                ->count();

            $RegistTickets = DB::table('tickets')
                ->where('status', 'Open')
                ->where('category', 'registration')
                ->count();

            // Calculate percentage for the Radial Chart
            $completionRate = $totalTickets > 0
                ? round(($completedTickets / $totalTickets) * 100)
                : 0;

            // 2. Live System Health Checks
            // Database Connection Check
            try {
                DB::connection()->getPdo();
                $dbStatus = [
                    'label' => 'Online',
                    'class' => 'success',
                    'latency' => 'Stable'
                ];
            } catch (\Exception $e) {
                $dbStatus = [
                    'label' => 'Offline',
                    'class' => 'danger',
                    'latency' => '--'
                ];
            }

            // Asset Storage Permission Check
            $storagePath = storage_path('app/public');
            $storageStatus = is_writable($storagePath)
                ? ['label' => 'Stable', 'class' => 'success', 'latency' => 'Writable']
                : ['label' => 'Error', 'class' => 'danger', 'latency' => 'Read-Only'];

            // Application Environment Detection
            $env = strtoupper(app()->environment());
            $envStatus = [
                'label' => $env,
                'class' => ($env == 'LOCAL') ? 'warning' : 'success',
                'latency' => ($env == 'LOCAL') ? 'Debug On' : 'Optimized'
            ];

            return view('index.children_views.dashboard', compact(
                'totalTickets',
                'completedTickets',
                'OGPTickets',
                'OpenTickets',
                'RegistTickets',
                'completionRate',
                'dbStatus',
                'storageStatus',
                'envStatus'
            ));
        }

        // --- ADMIN & CREW ROLE LOGIC ---
        if ($user->level === 'admin' || $user->level === 'user') {

            $totalTickets = 0;
            $completedTickets = 0;
            $completionRate = 0;
            $OGPTickets = 0;

            // ADD THIS LINE: Define the missing variable for Admins
            $RegistTickets = 0;

            // Default health values to prevent view errors
            $dbStatus = ['label' => 'Active', 'class' => 'success', 'latency' => '--'];
            $storageStatus = ['label' => 'Stable', 'class' => 'success', 'latency' => '--'];
            $envStatus = ['label' => 'STABLE', 'class' => 'success', 'latency' => '--'];

            return view('index.children_views.dashboard', compact(
                'totalTickets',
                'completedTickets',
                'completionRate',
                'OGPTickets',
                'RegistTickets', // Pass it here
                'dbStatus',
                'storageStatus',
                'envStatus'
            ));
        }

        // Fallback for unexpected roles
        return redirect()->route('login')->with('error', 'Unauthorized access.');
    }
}
