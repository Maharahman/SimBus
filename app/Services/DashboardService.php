<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get ticket statistics.
     */
    public function getTicketStats(): array
    {
        return [
            'total' => Ticket::count(),
            'completed' => Ticket::completed()->count(),
            'in_progress' => Ticket::inProgress()->count(),
            'open' => Ticket::open()->count(),
            'registrations' => Ticket::registrations()->open()->count(),
        ];
    }

    /**
     * Calculate ticket completion rate percentage.
     */
    public function getCompletionRate(): int
    {
        $total = Ticket::count();
        if ($total === 0) {
            return 0;
        }

        $completed = Ticket::completed()->count();
        return round(($completed / $total) * 100);
    }

    /**
     * Get database connection status.
     */
    public function getDatabaseStatus(): array
    {
        try {
            DB::connection()->getPdo();
            return [
                'label' => 'Online',
                'class' => 'success',
                'latency' => 'Stable'
            ];
        } catch (\Exception $e) {
            return [
                'label' => 'Offline',
                'class' => 'danger',
                'latency' => '--'
            ];
        }
    }

    /**
     * Get storage permission status.
     */
    public function getStorageStatus(): array
    {
        $storagePath = storage_path('app/public');
        
        return is_writable($storagePath) ? [
            'label' => 'Stable',
            'class' => 'success',
            'latency' => 'Writable'
        ] : [
            'label' => 'Error',
            'class' => 'danger',
            'latency' => 'Read-Only'
        ];
    }

    /**
     * Get environment status.
     */
    public function getEnvironmentStatus(): array
    {
        $env = strtoupper(app()->environment());
        
        return [
            'label' => $env,
            'class' => $env === 'LOCAL' ? 'warning' : 'success',
            'latency' => $env === 'LOCAL' ? 'Debug On' : 'Optimized'
        ];
    }

    /**
     * Get all system health metrics.
     */
    public function getSystemHealth(): array
    {
        return [
            'database' => $this->getDatabaseStatus(),
            'storage' => $this->getStorageStatus(),
            'environment' => $this->getEnvironmentStatus(),
        ];
    }

    /**
     * Get developer dashboard data.
     */
    public function getDeveloperDashboardData(): array
    {
        $stats = $this->getTicketStats();

        return [
            'totalTickets' => $stats['total'],
            'completedTickets' => $stats['completed'],
            'OGPTickets' => $stats['in_progress'],
            'OpenTickets' => $stats['open'],
            'RegistTickets' => $stats['registrations'],
            'completionRate' => $this->getCompletionRate(),
            'dbStatus' => $this->getDatabaseStatus(),
            'storageStatus' => $this->getStorageStatus(),
            'envStatus' => $this->getEnvironmentStatus(),
        ];
    }
}
