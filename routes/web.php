<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TicketController;

/* --- Public Routes --- */
Route::get('/', function () { return view('auth'); })->name('login');
Route::post('/login', [AuthController::class, 'webLogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/tickets/store', [TicketController::class, 'store'])->name('tickets.store');

/* --- Protected Routes --- */
Route::middleware(['auth'])->group(function () {

    Route::prefix('admin')->group(function () {
        //Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Services, Users, Inventory, Transactions...
        Route::resource('/services', ServiceController::class)->names('services');
        Route::resource('/users', UserController::class)->names('users');
        Route::resource('/inventory', InventoryController::class)->names('inventory');
        Route::resource('/transactions', TransactionController::class)->names('transactions');

        /* --- Ticket Management --- */
        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::post('/tickets/{id}/approve', [TicketController::class, 'approve'])->name('tickets.approve');
        Route::post('/tickets/{id}/reject', [TicketController::class, 'reject'])->name('tickets.reject');
        
        // Use PUT to match the @method('PUT') in your Developer modal
        Route::put('/tickets/{id}/update-status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
        Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');

        
    });

    Route::put('/admin/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
});