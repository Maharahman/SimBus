<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TicketController;

/* ========== PUBLIC ROUTES ========== */
Route::get('/', function () {
    return view('auth');
})->name('login');

Route::post('/login', [AuthController::class, 'webLogin'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Public ticket submission (no auth required)
Route::post('/tickets/store', [TicketController::class, 'store'])->name('tickets.store');


/* ========== PROTECTED ROUTES ========== */
Route::middleware(['auth'])->prefix('admin')->group(function () {

    /* --- Dashboard --- */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* --- Resources: Use Laravel resource conventions --- */
    Route::resource('services', ServiceController::class)->names('services');
    Route::resource('users', UserController::class)->names('users');
    Route::resource('inventory', InventoryController::class)->names('inventory');
    Route::resource('transactions', TransactionController::class)->names('transactions');

    /* --- Ticket Management --- */
    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('tickets.index');
        Route::post('{id}/approve', [TicketController::class, 'approve'])->name('tickets.approve');
        Route::put('{id}/update-status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
        Route::delete('{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    });

    /* --- User Profile --- */
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
});
