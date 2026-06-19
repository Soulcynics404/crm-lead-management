<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\FollowUpController;
use App\Http\Middleware\VerifyFirebaseToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All API routes are prefixed with /api and require Firebase token auth.
| Send the Firebase ID token in the Authorization header:
|   Authorization: Bearer <firebase_id_token>
|
*/

Route::middleware(VerifyFirebaseToken::class)->prefix('')->name('api.')->group(function () {

    // Dashboard Stats
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // Leads CRUD
    Route::apiResource('leads', LeadController::class);

    // Follow-ups (nested under leads)
    Route::get('/leads/{lead}/follow-ups', [FollowUpController::class, 'index'])->name('follow-ups.index');
    Route::post('/leads/{lead}/follow-ups', [FollowUpController::class, 'store'])->name('follow-ups.store');
    Route::put('/follow-ups/{followUp}', [FollowUpController::class, 'update'])->name('follow-ups.update');
    Route::delete('/follow-ups/{followUp}', [FollowUpController::class, 'destroy'])->name('follow-ups.destroy');
});
