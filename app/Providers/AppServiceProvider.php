<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Services\UserService;
use App\Services\TicketService;
use App\Services\DashboardService;
use App\Services\TransactionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services into the container for dependency injection
        $this->app->singleton(UserService::class);
        $this->app->singleton(TicketService::class);
        $this->app->singleton(DashboardService::class);
        $this->app->singleton(TransactionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Gate: Only developers can access developer features
        Gate::define('developer-only', function (User $user) {
            return $user->level === 'developer';
        });

        // Gate: Admins and developers can access admin features
        Gate::define('admin', function (User $user) {
            return in_array($user->level, ['admin', 'developer']);
        });
    }
}
