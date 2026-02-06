<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Transactions;
use App\Http\Controllers\Api\ChallengesApiController;

/**
 * Transactions (recommended unified endpoints)
 * Flutter should move to POST /api/transactions soon.
 */
Route::get('/transactions', [Transactions::class, 'index']);
Route::post('/transactions', [Transactions::class, 'store']);

/**
 * Backward compatible routes (so your existing Flutter doesn't break)
 * These will call your old methods for now.
 * Later, you can delete them after Flutter is updated.
 */
Route::post('/income', [Transactions::class, 'Income']);
Route::post('/expense', [Transactions::class, 'Expense']);

/**
 * Challenges (keep as-is)
 */
Route::prefix('challenges')->group(function () {
    Route::get('/', [ChallengesApiController::class, 'index']);
    Route::get('/daily', [ChallengesApiController::class, 'daily']);
    Route::get('/stats', [ChallengesApiController::class, 'stats']);
    Route::get('/{id}', [ChallengesApiController::class, 'show']);
    Route::post('/{id}/accept', [ChallengesApiController::class, 'accept']);
});
