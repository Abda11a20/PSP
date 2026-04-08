<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\ClientPageController;
use App\Http\Controllers\CheckoutController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Contact Support routes (public)
Route::get('/contact-support', [ClientPageController::class, 'contactSupport'])->name('contact-support');
Route::post('/contact-support', [ClientPageController::class, 'submitContactSupport'])->name('contact-support.submit');
Route::post('/chatbot/message', [ClientPageController::class, 'chatbotMessage'])->name('chatbot.message');

// Public Campaign Phishing Routes (for email links - accessible without /api prefix)
Route::get('/campaign/{token}', [App\Http\Controllers\EmailTrackingController::class, 'showPhishingPage'])
    ->where('token', '[A-Za-z0-9\-_=]{20,}')
    ->name('phishing.page.web');
Route::post('/campaign/{token}/submit', [App\Http\Controllers\EmailTrackingController::class, 'trackSubmit'])
    ->where('token', '[A-Za-z0-9\-_=]{20,}');
Route::get('/track/{token}/opened', [App\Http\Controllers\EmailTrackingController::class, 'trackOpen']);
Route::get('/track/{token}/clicked', [App\Http\Controllers\EmailTrackingController::class, 'trackClick']);

// Public Phishing Pages Routes
Route::get('/page/{slug}', [ClientPageController::class, 'showPublicPhishingPage'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('public.phishing-page');

// Payment/Checkout routes
Route::get('/checkout/{transactionId}', [CheckoutController::class, 'showCheckout'])->name('checkout');
Route::post('/checkout/{transactionId}/process', [CheckoutController::class, 'processPayment'])->name('checkout.process');
Route::get('/payment/{transactionId}/success', [CheckoutController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/{transactionId}/failed', [CheckoutController::class, 'paymentFailed'])->name('payment.failed');
Route::get('/payment/{transactionId}/status', [CheckoutController::class, 'paymentStatus'])->name('payment.status');
Route::post('/payment/{transactionId}/cancel', [CheckoutController::class, 'cancelPayment'])->name('payment.cancel');

// Authentication routes
Route::middleware('guest:company')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Password Reset Routes
    Route::get('/forgot-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.update');
    
    // OTP Password Reset Routes
    Route::get('/reset-password-otp', [App\Http\Controllers\Auth\PasswordResetController::class, 'showResetFormOtp'])->name('password.reset.otp');
    Route::post('/reset-password-otp', [App\Http\Controllers\Auth\PasswordResetController::class, 'resetWithOtp'])->name('password.reset.otp.store');
    
    // OTP Login Routes
    Route::post('/login/otp/send', [App\Http\Controllers\Auth\OtpController::class, 'sendLoginOtp'])->name('login.otp.send');
    Route::post('/login/otp/verify', [App\Http\Controllers\Auth\OtpController::class, 'verifyLoginOtp'])->name('login.otp.verify');
    Route::post('/otp/resend', [App\Http\Controllers\Auth\OtpController::class, 'resendOtp'])->name('otp.resend');
    
    // 2FA Verification Route (for login)
    Route::get('/2fa/verify', [App\Http\Controllers\Auth\TwoFactorController::class, 'showVerification'])->name('2fa.verify');
    Route::post('/2fa/verify', [App\Http\Controllers\Auth\TwoFactorController::class, 'verify'])->name('2fa.verify.store');
});

// Authenticated routes
Route::middleware('auth:company')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Admin routes
    Route::middleware(['check.role:admin'])->group(function () {
        Route::get('/admin/dashboard', [HomeController::class, 'adminDashboard'])->name('admin.dashboard');
        
        // Company Management Routes
        Route::get('/admin/companies', [ClientPageController::class, 'indexCompanies'])->name('admin.companies.index');
        Route::get('/admin/companies/create', [ClientPageController::class, 'createCompany'])->name('admin.companies.create');
        Route::post('/admin/companies', [ClientPageController::class, 'storeCompany'])->name('admin.companies.store');
        Route::get('/admin/companies/{company}', [ClientPageController::class, 'showCompany'])->name('admin.companies.show');
        Route::get('/admin/companies/{company}/edit', [ClientPageController::class, 'editCompany'])->name('admin.companies.edit');
        Route::put('/admin/companies/{company}', [ClientPageController::class, 'updateCompany'])->name('admin.companies.update');
        Route::delete('/admin/companies/{company}', [ClientPageController::class, 'destroyCompany'])->name('admin.companies.destroy');
        Route::get('/admin/companies/{company}/statistics', [ClientPageController::class, 'companyStatistics'])->name('admin.companies.statistics');
    });
    
    // Client routes
    Route::middleware(['check.role:client,admin'])->group(function () {
        Route::get('/dashboard', [HomeController::class, 'clientDashboard'])->name('client.dashboard');

        // Client pages
        Route::get('/dashboard/templates', [ClientPageController::class, 'viewTemplates'])->name('client.templates');
        Route::get('/dashboard/templates/{template}/preview', [ClientPageController::class, 'previewTemplate'])->name('client.templates.preview');
        Route::put('/dashboard/templates/{template}', [ClientPageController::class, 'updateTemplate'])->name('client.templates.update');
        Route::get('/dashboard/templates/{template}/use', [ClientPageController::class, 'useTemplate'])->name('client.templates.use');
        
        // Phishing Pages Routes
        Route::get('/dashboard/phishing-pages', [ClientPageController::class, 'viewPhishingPages'])->name('client.phishing-pages');
        Route::get('/dashboard/phishing-pages/create', [ClientPageController::class, 'createPhishingPage'])->name('client.phishing-pages.create');
        Route::post('/dashboard/phishing-pages', [ClientPageController::class, 'storePhishingPage'])->name('client.phishing-pages.store');
        Route::get('/dashboard/phishing-pages/{phishingPage}/edit', [ClientPageController::class, 'editPhishingPage'])->name('client.phishing-pages.edit');
        Route::put('/dashboard/phishing-pages/{phishingPage}', [ClientPageController::class, 'updatePhishingPage'])->name('client.phishing-pages.update');
        Route::delete('/dashboard/phishing-pages/{phishingPage}', [ClientPageController::class, 'deletePhishingPage'])->name('client.phishing-pages.delete');
        Route::get('/dashboard/phishing-pages/{phishingPage}/preview', [ClientPageController::class, 'previewPhishingPage'])->name('client.phishing-pages.preview');
        Route::get('/dashboard/users', [ClientPageController::class, 'manageUsers'])->name('client.users');
        Route::get('/dashboard/users/invite', [ClientPageController::class, 'showInviteUser'])->name('client.users.invite');
        Route::post('/dashboard/users/invite', [ClientPageController::class, 'inviteUser'])->name('client.users.invite.store');
        Route::get('/dashboard/users/{user}/edit', [ClientPageController::class, 'editUser'])->name('client.users.edit');
        Route::put('/dashboard/users/{user}', [ClientPageController::class, 'updateUser'])->name('client.users.update');
        Route::delete('/dashboard/users/{user}', [ClientPageController::class, 'deleteUser'])->name('client.users.delete');
        Route::get('/dashboard/reports', [ClientPageController::class, 'viewReports'])->name('client.reports');
        Route::get('/dashboard/api', [ClientPageController::class, 'viewDashboardApi'])->name('client.dashboard.api');
        Route::get('/dashboard/upgrade-plan', [ClientPageController::class, 'upgradePlan'])->name('client.upgrade-plan');
        Route::post('/dashboard/upgrade-plan/checkout', [ClientPageController::class, 'upgradeCheckout'])->name('client.upgrade-plan.checkout');
        Route::get('/dashboard/contact-support', [ClientPageController::class, 'contactSupport'])->name('client.contact-support');
        Route::get('/dashboard/billing', [ClientPageController::class, 'billing'])->name('client.billing');
        
        // Profile Routes
        Route::get('/dashboard/profile', [ClientPageController::class, 'showProfile'])->name('client.profile');
        Route::put('/dashboard/profile', [ClientPageController::class, 'updateProfile'])->name('client.profile.update');
        Route::put('/dashboard/profile/change-password', [ClientPageController::class, 'changePassword'])->name('client.profile.change-password');
        
        // Settings Routes
        Route::get('/dashboard/settings', [ClientPageController::class, 'showSettings'])->name('client.settings');
        Route::put('/dashboard/settings', [ClientPageController::class, 'updateSettings'])->name('client.settings.update');
        Route::post('/dashboard/settings/test-telegram', [ClientPageController::class, 'testTelegramConnection'])->name('client.settings.test-telegram');
        
        // Two-Factor Authentication Routes
        Route::get('/dashboard/2fa/setup', [App\Http\Controllers\Auth\TwoFactorController::class, 'showSetup'])->name('2fa.setup');
        Route::post('/dashboard/2fa/enable', [App\Http\Controllers\Auth\TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::post('/dashboard/2fa/disable', [App\Http\Controllers\Auth\TwoFactorController::class, 'disable'])->name('2fa.disable');
        
        // Notifications Routes
        Route::get('/dashboard/notifications', [ClientPageController::class, 'viewNotifications'])->name('client.notifications');
        Route::post('/dashboard/notifications/mark-all-read', [ClientPageController::class, 'markAllNotificationsRead'])->name('client.notifications.mark-all-read');
        
        // Campaign CRUD Routes
        Route::get('/dashboard/campaigns', [ClientPageController::class, 'indexCampaigns'])->name('client.campaigns.index');
        Route::get('/dashboard/campaigns/create', [ClientPageController::class, 'createCampaign'])->name('client.campaigns.create');
        Route::post('/dashboard/campaigns', [ClientPageController::class, 'storeCampaign'])->name('client.campaigns.store');
        Route::get('/dashboard/campaigns/{campaign}', [ClientPageController::class, 'showCampaign'])->name('client.campaigns.show');
        Route::get('/dashboard/campaigns/{campaign}/edit', [ClientPageController::class, 'editCampaign'])->name('client.campaigns.edit');
        Route::put('/dashboard/campaigns/{campaign}', [ClientPageController::class, 'updateCampaign'])->name('client.campaigns.update');
        Route::delete('/dashboard/campaigns/{campaign}', [ClientPageController::class, 'destroyCampaign'])->name('client.campaigns.destroy');
        Route::get('/dashboard/campaigns/{campaign}/report', [ClientPageController::class, 'viewCampaignReport'])->name('client.campaigns.report');
        
        // Campaign Management Routes
        Route::get('/dashboard/campaigns/{campaign}/add-targets', [ClientPageController::class, 'addTargetsForm'])->name('client.campaigns.add-targets');
        Route::post('/dashboard/campaigns/{campaign}/add-targets', [ClientPageController::class, 'storeTargets'])->name('client.campaigns.store-targets');
        Route::get('/dashboard/campaigns/{campaign}/stats', [ClientPageController::class, 'campaignStats'])->name('client.campaigns.stats');
        Route::get('/dashboard/campaigns/{campaign}/ai-analysis', [ClientPageController::class, 'campaignAiAnalysis'])->name('client.campaigns.ai-analysis');
        Route::post('/dashboard/campaigns/{campaign}/send-emails', [ClientPageController::class, 'sendCampaignEmails'])->name('client.campaigns.send-emails');
        Route::post('/dashboard/campaigns/{campaign}/resend-email/{target}', [ClientPageController::class, 'resendEmailToTarget'])->name('client.campaigns.resend-email');
        Route::post('/dashboard/campaigns/{campaign}/launch', [ClientPageController::class, 'launchCampaign'])->name('client.campaigns.launch');
        Route::post('/dashboard/campaigns/{campaign}/pause', [ClientPageController::class, 'pauseCampaign'])->name('client.campaigns.pause');
        Route::post('/dashboard/campaigns/{campaign}/stop', [ClientPageController::class, 'stopCampaign'])->name('client.campaigns.stop');
    });
    
    // Developer routes
    Route::middleware(['check.role:developer,admin'])->group(function () {
        Route::get('/api/documentation', function () {
            return redirect('/api/documentation');
        })->name('api.documentation');
    });
});
