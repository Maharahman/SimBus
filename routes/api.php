<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;

/* ========== PUBLIC ROUTES ========== */
Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');


/* ========== PROTECTED ROUTES ========== */
Route::middleware('auth:sanctum')->group(function () {

    /* --- Services --- */
    Route::apiResource('services', ServiceController::class)->names('api.services');
});
