<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DemoAccessController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketReplyController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Demo access routes
Route::prefix('demo')->group(function () {
    Route::post('/quick-login', [DemoAccessController::class, 'quickDemo']);
    Route::get('/accounts', [DemoAccessController::class, 'getDemoAccounts']);
    
    // Protected demo routes (only in development)
    if (app()->environment('local', 'development')) {
        Route::post('/reset-data', [DemoAccessController::class, 'resetDemoData']);
    }
});

// Public ticket routes
Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);
    Route::get('/stats/tickets', [TicketController::class, 'getStats']);
    
    Route::post('/tickets/{ticket}/replies', [TicketReplyController::class, 'store']);
});