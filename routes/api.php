<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthSessionController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Transactions;

use App\Http\Controllers\Api\ChallengesApiController;
use App\Http\Controllers\Api\UserChallengesApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| /api/*
| Your app is using session auth, so keep web middleware.
*/

Route::middleware('web')->group(function () {

    // Auth (session)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthSessionController::class, 'register']);
        Route::post('/login', [AuthSessionController::class, 'login']);
        Route::post('/logout', [AuthSessionController::class, 'logout']);
        Route::get('/me', [AuthSessionController::class, 'me']);
    });

    Route::post('/onboarding', [OnboardingController::class, 'store']);

    // Logged-in only
    Route::middleware('auth')->group(function () {

        // ✅ Transactions (ONLY these - Option A)
        Route::get('/transactions', [Transactions::class, 'index']);
        Route::post('/transactions', [Transactions::class, 'store']);

        // ✅ Accept quest (must be authenticated)
        Route::post('/challenges/{id}/accept', [ChallengesApiController::class, 'accept']);

        // ✅ Active/Completed quests for tabs
        Route::get('/my-challenges', [UserChallengesApiController::class, 'index']);
    });
});

// Public challenge catalogue (Suggested tab)
Route::prefix('challenges')->group(function () {
    Route::get('/', [ChallengesApiController::class, 'index']);
    Route::get('/daily', [ChallengesApiController::class, 'daily']);
    Route::get('/stats', [ChallengesApiController::class, 'stats']);
    Route::get('/{id}', [ChallengesApiController::class, 'show']);
});
