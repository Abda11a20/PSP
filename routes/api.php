<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return Auth::guard('api')->user();
});

// Authentication Routes (with rate limiting)
Route::prefix('auth')->middleware('throttle:api')->group(function () {
    Route::post('register', [App\Http\Controllers\Auth\AuthController::class, 'register']);
    Route::post('login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::post('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [App\Http\Controllers\Auth\AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('me', [App\Http\Controllers\Auth\AuthController::class, 'me'])->middleware('auth:api');
    Route::post('change-password', [App\Http\Controllers\Auth\AuthController::class, 'changePassword'])->middleware('auth:api');
});

// Public Email Tracking Routes (with rate limiting)
Route::prefix('track')->middleware('throttle:api')->group(function () {
    Route::get('/{token}/opened', [App\Http\Controllers\EmailTrackingController::class, 'trackOpen']);
    Route::get('/{token}/clicked', [App\Http\Controllers\EmailTrackingController::class, 'trackClick']);
    Route::post('/{token}/submitted', [App\Http\Controllers\EmailTrackingController::class, 'trackSubmit']);
});

// Public Campaign Routes (with rate limiting)
// Constrain token to avoid conflicts with reserved words like "create"
Route::middleware('throttle:api')->group(function () {
    Route::get('/campaign/{token}', [App\Http\Controllers\EmailTrackingController::class, 'showPhishingPage'])
        ->where('token', '[A-Za-z0-9\-_=]{20,}')
        ->name('phishing.page');
    Route::post('/campaign/{token}/submit', [App\Http\Controllers\EmailTrackingController::class, 'trackSubmit'])
        ->where('token', '[A-Za-z0-9\-_=]{20,}');
    Route::get('/fake-phishing-page', [App\Http\Controllers\EmailTrackingController::class, 'showFakePhishingPage']);
});

// Protected Routes (require authentication and rate limiting)
Route::middleware(['auth:api', 'throttle:api'])->group(function () {
    
    // Dashboard Routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [App\Http\Controllers\Company\DashboardController::class, 'dashboard'])->name('api.dashboard');
        Route::get('/stats', [App\Http\Controllers\Company\DashboardController::class, 'stats'])->name('api.dashboard.stats');
        Route::get('/analytics', [App\Http\Controllers\Company\DashboardController::class, 'analytics'])->name('api.dashboard.analytics');
        Route::get('/recent-activity', [App\Http\Controllers\Company\DashboardController::class, 'recentActivity'])->name('api.dashboard.activity');
        Route::get('/performance', [App\Http\Controllers\Company\DashboardController::class, 'performance'])->name('api.dashboard.performance');
        Route::get('/charts', [App\Http\Controllers\Company\DashboardController::class, 'charts'])->name('api.dashboard.charts');
    });
    
    // Legacy Company Dashboard (for backward compatibility)
    Route::get('/company/dashboard', [App\Http\Controllers\Company\DashboardController::class, 'dashboard']);
    
    // Company Routes
    Route::prefix('companies')->group(function () {
        Route::get('/', [App\Http\Controllers\Company\CompanyController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Company\CompanyController::class, 'store']);
        Route::get('/{company}', [App\Http\Controllers\Company\CompanyController::class, 'show']);
        Route::put('/{company}', [App\Http\Controllers\Company\CompanyController::class, 'update']);
        Route::delete('/{company}', [App\Http\Controllers\Company\CompanyController::class, 'destroy']);
        Route::get('/{company}/statistics', [App\Http\Controllers\Company\CompanyController::class, 'statistics']);
    });

    // Campaign Routes
    Route::prefix('campaign')->group(function () {
        Route::get('/templates', [App\Http\Controllers\Campaign\CampaignController::class, 'templates'])->name('api.campaign.templates');
        Route::post('/create', [App\Http\Controllers\Campaign\CampaignController::class, 'create'])->name('api.campaign.create');
        Route::post('/add-targets', [App\Http\Controllers\Campaign\CampaignController::class, 'addTargets']);
        Route::post('/{campaignId}/add-targets', [App\Http\Controllers\Campaign\CampaignController::class, 'addTargets']);
        Route::get('/{id}/details', [App\Http\Controllers\Campaign\CampaignController::class, 'details']);
        Route::post('/{id}/send-emails', [App\Http\Controllers\Campaign\CampaignController::class, 'sendEmails']);
        Route::get('/{id}/stats', [App\Http\Controllers\Campaign\CampaignController::class, 'stats']);
        Route::get('/{id}/ai-analysis', [App\Http\Controllers\Campaign\CampaignController::class, 'aiAnalysis']);
        Route::post('/{campaignId}/resend-email/{targetId}', [App\Http\Controllers\Campaign\CampaignController::class, 'resendEmail']);
    });

    // Legacy Campaign Routes (for backward compatibility)
    Route::prefix('campaigns')->group(function () {
        Route::get('/', [App\Http\Controllers\Campaign\CampaignController::class, 'index'])->name('api.campaign.index');
        Route::post('/', [App\Http\Controllers\Campaign\CampaignController::class, 'store'])->name('api.campaign.store');
        Route::get('/{campaign}', [App\Http\Controllers\Campaign\CampaignController::class, 'show'])->name('api.campaign.show');
        Route::put('/{campaign}', [App\Http\Controllers\Campaign\CampaignController::class, 'update'])->name('api.campaign.update');
        Route::delete('/{campaign}', [App\Http\Controllers\Campaign\CampaignController::class, 'destroy'])->name('api.campaign.destroy');
        Route::post('/{campaign}/launch', [App\Http\Controllers\Campaign\CampaignController::class, 'launch'])->name('api.campaign.launch');
        Route::post('/{campaign}/pause', [App\Http\Controllers\Campaign\CampaignController::class, 'pause'])->name('api.campaign.pause');
        Route::post('/{campaign}/stop', [App\Http\Controllers\Campaign\CampaignController::class, 'stop'])->name('api.campaign.stop');
    });

    // Plans Routes (public)
    Route::get('/plans', [App\Http\Controllers\Payment\PaymentController::class, 'plans']);

    // Payment Routes
    Route::prefix('payment')->group(function () {
        Route::post('/checkout', [App\Http\Controllers\Payment\PaymentController::class, 'checkout']);
        Route::post('/confirm', [App\Http\Controllers\Payment\PaymentController::class, 'confirm']);
        Route::get('/status/{transactionId}', [App\Http\Controllers\Payment\PaymentController::class, 'status']);
        Route::post('/cancel/{transactionId}', [App\Http\Controllers\Payment\PaymentController::class, 'cancel']);
    });

    // Legacy Payment Routes (for backward compatibility)
    Route::prefix('payments')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\PaymentController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Payment\PaymentController::class, 'store']);
        Route::get('/{payment}', [App\Http\Controllers\Payment\PaymentController::class, 'show']);
        Route::post('/{payment}/process', [App\Http\Controllers\Payment\PaymentController::class, 'process']);
    });

    // Report Routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [App\Http\Controllers\Report\ReportController::class, 'index']);
        Route::get('/campaign/{id}', [App\Http\Controllers\Report\ReportController::class, 'campaignReport']);
        Route::get('/companies/{company}', [App\Http\Controllers\Report\ReportController::class, 'companyReport']);
        Route::get('/export/{type}', [App\Http\Controllers\Report\ReportController::class, 'export']);
    });

    // AI Routes
    Route::prefix('ai')->group(function () {
        Route::post('/generate-email', [App\Http\Controllers\AI\AIController::class, 'generateEmail']);
        Route::post('/analyze-response', [App\Http\Controllers\AI\AIController::class, 'analyzeResponse']);
        Route::post('/suggest-improvements', [App\Http\Controllers\AI\AIController::class, 'suggestImprovements']);
    });
});
