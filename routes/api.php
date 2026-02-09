<?php

use Illuminate\Support\Facades\Route;

// Existing controllers
use App\Http\Controllers\Transactions;
use App\Http\Controllers\Api\ChallengesApiController;

// Auth + onboarding
use App\Http\Controllers\AuthSessionController;
use App\Http\Controllers\OnboardingController;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /api)
|--------------------------------------------------------------------------
*/

// --------------------
// Transactions
// --------------------
Route::get('/transactions', [Transactions::class, 'index']);
Route::post('/transactions', [Transactions::class, 'store']);
Route::post('/income', [Transactions::class, 'Income']);
Route::post('/expense', [Transactions::class, 'Expense']);

// --------------------
// Challenges
// --------------------
Route::prefix('challenges')->group(function () {
    Route::get('/', [ChallengesApiController::class, 'index']);
    Route::get('/daily', [ChallengesApiController::class, 'daily']);
    Route::get('/stats', [ChallengesApiController::class, 'stats']);
    Route::get('/{id}', [ChallengesApiController::class, 'show']);
    Route::post('/{id}/accept', [ChallengesApiController::class, 'accept']);
});

// --------------------
// ✅ Session-based Auth + Onboarding (needs web middleware for sessions)
// --------------------
Route::middleware('web')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthSessionController::class, 'register']);
        Route::post('/login', [AuthSessionController::class, 'login']);
        Route::post('/logout', [AuthSessionController::class, 'logout']);
        Route::get('/me', [AuthSessionController::class, 'me']);
    });

    Route::post('/onboarding', [OnboardingController::class, 'store']);
});
