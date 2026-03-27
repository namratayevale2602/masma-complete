<?php

use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\RegistrationAuthController;
use App\Http\Controllers\Api\VisitorController;
use App\Http\Controllers\Api\HeroImageController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\BoardDirectorController;
use App\Http\Controllers\Api\GetInTouchController;
use App\Http\Controllers\Api\CompanyLogoController;
use App\Http\Controllers\Api\StatController;
use App\Http\Controllers\Api\CtaCardController;
use App\Http\Controllers\Api\AboutMasmaController;
use App\Http\Controllers\Api\OurObjectiveController;
use App\Http\Controllers\Api\CommitteeController;
use App\Http\Controllers\Api\RegionalDirectorController;
use App\Http\Controllers\Api\AssociateController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\MembershipFeatureController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\CircularController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\SocialMediaController;
use App\Http\Controllers\Api\RenewalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ============ PUBLIC API ROUTES (No Authentication Required) ============

// masmaexpo contact us page data collection endpoint
Route::get('/test-phpmailer', [ContactController::class, 'testMail']);

// Contact form route
Route::post('/contact/submit', [ContactController::class, 'submit']);

// Existing route
Route::post('/visitors', [VisitorController::class, 'store']);

// New route for QR code processing
Route::post('/visitors/qrcode', [VisitorController::class, 'storeQrCode']);
Route::get('/visitors/{id}/download-qrcode', [VisitorController::class, 'downloadQrCode'])
    ->name('visitor.qrcode.download');
Route::get('/visitors/{id}', [VisitorController::class, 'show']);
// Add these routes
Route::get('/visitors/{id}/idcard/download', [VisitorController::class, 'downloadIdCard'])
    ->name('visitor.idcard.download');
    
Route::get('/visitors/{id}/idcard/view', [VisitorController::class, 'viewIdCard'])
    ->name('visitor.idcard.view');

// Registration Routes
Route::prefix('registrations')->group(function () {
    Route::post('/', [RegistrationController::class, 'store']);
    Route::post('/{registration}/send-credentials', [RegistrationController::class, 'sendCredentials']);
    Route::post('/{registration}/reset-password', [RegistrationController::class, 'resetPassword']);
    Route::post('/verify-password', [RegistrationController::class, 'verifyPassword']);
    Route::get('/{registration}', [RegistrationController::class, 'show']);
    Route::put('/{registration}', [RegistrationController::class, 'update']);
    Route::delete('/{registration}', [RegistrationController::class, 'destroy']);
});

Route::prefix('renewals')->group(function () {
    Route::get('/expiring', [RenewalController::class, 'getExpiringMembers']);
    Route::post('/send', [RenewalController::class, 'sendRenewalReminder']);
    Route::post('/bulk', [RenewalController::class, 'sendBulkReminders']);
});

// Registration Auth Routes
Route::prefix('registration')->group(function () {
    // Public routes
    Route::post('/login', [RegistrationAuthController::class, 'login']);
    Route::get('/check-auth', [RegistrationAuthController::class, 'checkAuth']);
    Route::get('/csrf-token', [RegistrationAuthController::class, 'getCsrfToken']);
    Route::post('/refresh', [RegistrationAuthController::class, 'refresh']);
    
    // Debug routes
    Route::get('/debug-tokens', function (Request $request) {
        $accessToken = $request->cookie('access_token');
        $refreshToken = $request->cookie('refresh_token');
        
        $tokens = \Laravel\Sanctum\PersonalAccessToken::all();
        
        return response()->json([
            'cookies' => [
                'access_token' => $accessToken ? substr($accessToken, 0, 20) . '...' : 'missing',
                'refresh_token' => $refreshToken ? substr($refreshToken, 0, 20) . '...' : 'missing',
            ],
            'tokens_in_database' => $tokens->map(function($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'tokenable_type' => $token->tokenable_type,
                    'tokenable_id' => $token->tokenable_id,
                    'abilities' => $token->abilities,
                    'expires_at' => $token->expires_at?->toDateTimeString(),
                    'created_at' => $token->created_at->toDateTimeString(),
                ];
            }),
        ]);
    });
    
    Route::get('/debug-cookies', function (Request $request) {
        return response()->json([
            'all_cookies' => $request->cookie(),
            'has_access_token' => $request->hasCookie('access_token'),
            'has_refresh_token' => $request->hasCookie('refresh_token'),
        ]);
    });
    
    // Protected routes
    Route::middleware([\App\Http\Middleware\SanctumCookieAuth::class])->group(function () {
        Route::post('/logout', [RegistrationAuthController::class, 'logout']);
        Route::get('/user', [RegistrationAuthController::class, 'user']);
        Route::post('/change-password', [RegistrationAuthController::class, 'changePassword']);
    });
});



// ============ API V1 ROUTES ============
Route::prefix('v1')->group(function () {
    
    // ============ PUBLIC ROUTES ============
    
    // Hero Images
    Route::get('/hero-images', [HeroImageController::class, 'index']);
    Route::get('/hero-images/{id}', [HeroImageController::class, 'show']);

    // Participants
    Route::get('/participants', [ParticipantController::class, 'index']);
    Route::get('/participants/{id}', [ParticipantController::class, 'show']);

    // About Us
    Route::get('/about-us', [AboutUsController::class, 'index']);

    // Board Directors
    Route::get('/board-directors', [BoardDirectorController::class, 'index']);
    Route::get('/board-directors/years', [BoardDirectorController::class, 'getYears']);
    Route::get('/board-directors/{id}', [BoardDirectorController::class, 'show']);

    // Get In Touch
    Route::get('/get-in-touch', [GetInTouchController::class, 'index']);
    Route::get('/get-in-touch/{id}', [GetInTouchController::class, 'show']);

    // Company Logos
    Route::get('/company-logos', [CompanyLogoController::class, 'index']);
    Route::get('/company-logos/{id}', [CompanyLogoController::class, 'show']);

    // Stats
    Route::get('/stats', [StatController::class, 'index']);
    Route::get('/stats/{id}', [StatController::class, 'show']);

    // CTA Cards
    Route::get('/cta-cards', [CtaCardController::class, 'index']);
    Route::get('/cta-cards/{id}', [CtaCardController::class, 'show']);

    // About Masma
    Route::get('/about-masma', [AboutMasmaController::class, 'index']);
    Route::get('/about-masma/{id}', [AboutMasmaController::class, 'show']);

    // Our Objective
    Route::get('/our-objective', [OurObjectiveController::class, 'index']);
    
    // Committees
    Route::get('/committees', [CommitteeController::class, 'index']);
    
    // Regional Directors
    Route::get('/regional-directors', [RegionalDirectorController::class, 'index']);
    
    // Associates
    Route::get('/associates', [AssociateController::class, 'index']);
    
    // Membership Plans
    Route::get('/membership-plans', [MembershipController::class, 'index']);
    Route::get('/membership-plans/{id}', [MembershipController::class, 'show']);
    
    // Membership Features
    Route::get('/membership-features', [MembershipFeatureController::class, 'index']);
    
    // Gallery
    Route::get('/gallery', [GalleryController::class, 'index']);
    Route::get('/gallery/{id}', [GalleryController::class, 'show']);
    
    // FAQ
    Route::get('/faqs', [FaqController::class, 'index']);
    Route::get('/faqs/{id}', [FaqController::class, 'show']);
    Route::get('/faq-categories', [FaqController::class, 'getCategories']);
    
    // Circulars
    Route::get('/circulars', [CircularController::class, 'index']);
    Route::get('/circulars/{id}', [CircularController::class, 'show']);
    
    // ============ CONTACT ROUTES ============
    // Public contact routes
    Route::get('/contact', [ContactController::class, 'index']);
    Route::post('/contact/messages', [ContactMessageController::class, 'store']);
    
    // Social Media (Public)
    Route::get('/social-media', [SocialMediaController::class, 'index']);
    
    // ============ PROTECTED ADMIN ROUTES ============
    Route::middleware([\App\Http\Middleware\SanctumCookieAuth::class])->group(function () {
        
        // Hero Images Admin
        Route::post('/hero-images', [HeroImageController::class, 'store']);
        Route::put('/hero-images/{id}', [HeroImageController::class, 'update']);
        Route::delete('/hero-images/{id}', [HeroImageController::class, 'destroy']);
        
        // Participants Admin
        Route::post('/participants', [ParticipantController::class, 'store']);
        Route::put('/participants/{id}', [ParticipantController::class, 'update']);
        Route::delete('/participants/{id}', [ParticipantController::class, 'destroy']);
        Route::post('/participants/update-order', [ParticipantController::class, 'updateOrder']);
        Route::post('/participants/bulk-upload', [ParticipantController::class, 'bulkUpload']);
        
        // About Us Admin
        Route::post('/about-us', [AboutUsController::class, 'store']);
        Route::put('/about-us/{id}', [AboutUsController::class, 'update']);
        Route::delete('/about-us/{id}', [AboutUsController::class, 'destroy']);
        Route::patch('/about-us/{id}/toggle-active', [AboutUsController::class, 'toggleActive']);
        
        // Board Directors Admin
        Route::post('/board-directors', [BoardDirectorController::class, 'store']);
        Route::put('/board-directors/{id}', [BoardDirectorController::class, 'update']);
        Route::delete('/board-directors/{id}', [BoardDirectorController::class, 'destroy']);
        Route::post('/board-directors/update-order', [BoardDirectorController::class, 'updateOrder']);
        
        // Get In Touch Admin
        Route::post('/get-in-touch', [GetInTouchController::class, 'store']);
        Route::put('/get-in-touch/{id}', [GetInTouchController::class, 'update']);
        Route::delete('/get-in-touch/{id}', [GetInTouchController::class, 'destroy']);
        Route::patch('/get-in-touch/{id}/toggle-active', [GetInTouchController::class, 'toggleActive']);
        
        // Company Logo Admin
        Route::post('/company-logos', [CompanyLogoController::class, 'store']);
        Route::put('/company-logos/{id}', [CompanyLogoController::class, 'update']);
        Route::delete('/company-logos/{id}', [CompanyLogoController::class, 'destroy']);
        Route::post('/company-logos/update-order', [CompanyLogoController::class, 'updateOrder']);
        
        // Stats Admin
        Route::post('/stats', [StatController::class, 'store']);
        Route::put('/stats/{id}', [StatController::class, 'update']);
        Route::delete('/stats/{id}', [StatController::class, 'destroy']);
        Route::post('/stats/update-order', [StatController::class, 'updateOrder']);
        
        // CTA Cards Admin
        Route::post('/cta-cards', [CtaCardController::class, 'store']);
        Route::put('/cta-cards/{id}', [CtaCardController::class, 'update']);
        Route::delete('/cta-cards/{id}', [CtaCardController::class, 'destroy']);
        Route::post('/cta-cards/update-order', [CtaCardController::class, 'updateOrder']);
        
        // About Masma Admin
        Route::post('/about-masma', [AboutMasmaController::class, 'store']);
        Route::put('/about-masma/{id}', [AboutMasmaController::class, 'update']);
        Route::delete('/about-masma/{id}', [AboutMasmaController::class, 'destroy']);
        Route::patch('/about-masma/{id}/toggle-active', [AboutMasmaController::class, 'toggleActive']);
        
        // Our Objective Admin
        Route::post('/our-objective', [OurObjectiveController::class, 'store']);
        
        // Committees Admin
        Route::post('/committees', [CommitteeController::class, 'store']);
        Route::put('/committees/{id}', [CommitteeController::class, 'update']);
        Route::delete('/committees/{id}', [CommitteeController::class, 'destroy']);
        
        // Regional Directors Admin
        Route::post('/regional-directors', [RegionalDirectorController::class, 'store']);
        Route::put('/regional-directors/{id}', [RegionalDirectorController::class, 'update']);
        Route::delete('/regional-directors/{id}', [RegionalDirectorController::class, 'destroy']);
        
        // Associates Admin
        Route::post('/associates', [AssociateController::class, 'store']);
        Route::put('/associates/{id}', [AssociateController::class, 'update']);
        Route::delete('/associates/{id}', [AssociateController::class, 'destroy']);
        Route::post('/associates/update-order', [AssociateController::class, 'updateOrder']);
        
        // Membership Plans Admin
        Route::post('/membership-plans', [MembershipController::class, 'store']);
        Route::put('/membership-plans/{id}', [MembershipController::class, 'update']);
        Route::delete('/membership-plans/{id}', [MembershipController::class, 'destroy']);
        Route::post('/membership-plans/update-order', [MembershipController::class, 'updateOrder']);
        
        // Membership Features Admin
        Route::post('/membership-features', [MembershipFeatureController::class, 'store']);
        Route::put('/membership-features/{id}', [MembershipFeatureController::class, 'update']);
        Route::delete('/membership-features/{id}', [MembershipFeatureController::class, 'destroy']);
        
        // Gallery Admin
        Route::post('/gallery', [GalleryController::class, 'store']);
        Route::put('/gallery/{id}', [GalleryController::class, 'update']);
        Route::delete('/gallery/{id}', [GalleryController::class, 'destroy']);
        
        // FAQ Admin
        Route::post('/faqs', [FaqController::class, 'store']);
        Route::put('/faqs/{id}', [FaqController::class, 'update']);
        Route::delete('/faqs/{id}', [FaqController::class, 'destroy']);
        
        // Circulars Admin
        Route::post('/circulars', [CircularController::class, 'store']);
        Route::put('/circulars/{id}', [CircularController::class, 'update']);
        Route::delete('/circulars/{id}', [CircularController::class, 'destroy']);
        
        // ============ CONTACT ADMIN ROUTES ============
        // Contact Messages Admin
         Route::get('/contact', [ContactController::class, 'index']);
        Route::get('/contact/messages', [ContactMessageController::class, 'index']);
        Route::get('/contact/messages/{id}', [ContactMessageController::class, 'show']);
        Route::post('/contact/messages/{id}/reply', [ContactMessageController::class, 'reply']);
        Route::delete('/contact/messages/{id}', [ContactMessageController::class, 'destroy']);
        
        // Social Media Admin
         // Social Media routes
    Route::get('/social-media', [SocialMediaController::class, 'index']);
    Route::get('/social-media/{id}', [SocialMediaController::class, 'show']);
        Route::post('/social-media', [SocialMediaController::class, 'store']);
        Route::put('/social-media/{id}', [SocialMediaController::class, 'update']);
        Route::delete('/social-media/{id}', [SocialMediaController::class, 'destroy']);
    });
});