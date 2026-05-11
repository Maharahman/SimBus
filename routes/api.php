<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;

// Public route for logging in
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (Only accessible with a valid token)
Route::middleware('auth:sanctum')->group(function () {
    // Add your Service CRUD routes here later
    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
});