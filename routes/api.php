<?php

use App\Http\Controllers\Api\ChallengesApiController;
use Illuminate\Support\Facades\Route;

Route::post('/income', [\App\Http\Controllers\Transactions::class, 'Income']);
Route::post('/expense', [\App\Http\Controllers\Transactions::class, 'Expense']);

Route::prefix('challenges')->group(function () {
    Route::get('/', [ChallengesApiController::class, 'index']);
    Route::get('/daily', [ChallengesApiController::class, 'daily']);
    Route::get('/stats', [ChallengesApiController::class, 'stats']);
    Route::get('/{id}', [ChallengesApiController::class, 'show']);
    Route::post('/{id}/accept', [ChallengesApiController::class, 'accept']);
});
