<?php

use App\Http\Controllers\Admin\ApiTokenController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

// ── Public site ─────────────────────────────────────────────────────
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactMessageController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/shipping-policy', [PageController::class, 'shippingPolicy'])->name('shipping-policy');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [PageController::class, 'robots'])->name('robots');

// ── Public tracking (rate-limited against enumeration) ───────────────
Route::get('/track', [TrackingController::class, 'index'])->name('track');
Route::post('/track', [TrackingController::class, 'lookup'])
    ->middleware('tracking.throttle')
    ->name('track.lookup');
Route::get('/track/{trackingNumber}', [TrackingController::class, 'result'])
    ->middleware('tracking.throttle')
    ->name('track.result');
Route::get('/track/{trackingNumber}/location.json', [TrackingController::class, 'locationJson'])
    ->middleware('tracking.throttle')
    ->name('track.location.json');

// ── Admin dashboard ─────────────────────────────────────────────────
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('shipments/export', [ShipmentController::class, 'export'])->name('shipments.export');
        Route::resource('shipments', ShipmentController::class)->except(['destroy']);
        Route::post('shipments/{shipment}/events', [ShipmentController::class, 'addEvent'])->name('shipments.events.store');
        Route::post('shipments/{shipment}/location', [ShipmentController::class, 'updateLocation'])->name('shipments.location.update');
        Route::post('shipments/{shipment}/archive', [ShipmentController::class, 'archive'])->name('shipments.archive');
        Route::post('shipments/{shipment}/unarchive', [ShipmentController::class, 'unarchive'])->name('shipments.unarchive');
        Route::get('shipments/{shipment}/print', [ShipmentController::class, 'printView'])->name('shipments.print');

        Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');
        Route::post('customers/{customer}/flag', [CustomerController::class, 'toggleFlag'])->name('customers.flag');
        Route::resource('customers', CustomerController::class)->only(['index', 'show', 'edit', 'update']);

        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');

        Route::get('enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
        Route::post('enquiries/{enquiry}/toggle', [EnquiryController::class, 'toggleResolve'])->name('enquiries.toggle');

        // Super-administration
        Route::middleware('role:super_admin')->group(function () {
            Route::resource('users', UserController::class)->except(['show', 'destroy']);
            Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
            Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit.index');
            Route::get('api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
            Route::post('api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
            Route::delete('api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
        });
    });

// Profile (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Legacy convenience redirect
Route::redirect('/dashboard', '/admin')->name('dashboard');

require __DIR__.'/auth.php';
