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

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\FollowUpController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.page');
Route::post('/auth/firebase-login', [AuthController::class, 'handleFirebaseLogin'])->name('auth.firebase');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // Leads - Import/Export (must be before resource route)
    Route::get('/leads-export', [LeadController::class, 'export'])->name('leads.export');
    Route::post('/leads-import', [LeadController::class, 'import'])->name('leads.import');
    Route::get('/leads-sample-csv', [LeadController::class, 'sampleCsv'])->name('leads.sample-csv');

    // Leads
    Route::resource('leads', LeadController::class);

    // Follow-ups (nested under leads)
    Route::get('/leads/{lead}/follow-ups', [FollowUpController::class, 'index'])->name('follow-ups.index');
    Route::post('/leads/{lead}/follow-ups', [FollowUpController::class, 'store'])->name('follow-ups.store');
    Route::put('/follow-ups/{followUp}', [FollowUpController::class, 'update'])->name('follow-ups.update');
    Route::delete('/follow-ups/{followUp}', [FollowUpController::class, 'destroy'])->name('follow-ups.destroy');
});
