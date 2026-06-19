<?php

/**
 * HK CRM Lead Management System
 *
 * @author    Harsshh (@Soulcynics404)
 * @github    https://github.com/Soulcynics404/crm-lead-management
 * @quote     "Breaking systems to make them secure."
 * @copyright 2026 Harsshh. All rights reserved.
 *
 * NOTICE: This code is proprietary. Do not copy, modify, or redistribute
 * without proper attribution to the original author.
 */

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
