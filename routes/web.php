<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaystackController;
use App\Livewire\CreditPurchase;
use App\Livewire\TransactionHistory;
use App\Livewire\BatchList;
use App\Livewire\ChannelList;
use App\Livewire\ChannelCreate;
use App\Livewire\ChannelAdList;
use App\Livewire\MyChannelApplications;

// Homepage - Batch List (protected by auth and verified.otp middleware)
Route::get('/', BatchList::class)
    ->middleware(['auth', 'verified.otp'])
    ->name('home');

// Redirect unauthenticated users to login
Route::redirect('/welcome', '/login');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Credit System Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Credit Purchase
    Route::get('/credits/purchase', CreditPurchase::class)
        ->name('credits.purchase');
    
    // Withdrawal
    Route::get('/withdrawal', \App\Livewire\Withdrawal::class)
        ->name('withdrawal.index');
    
    // Transaction History
    Route::get('/transactions', TransactionHistory::class)
        ->name('transactions.index');
});

// Channel Routes
Route::prefix('channels')->name('channels.')->group(function () {
    // Public channel listing (no auth required)
    Route::get('/', ChannelList::class)
        ->name('index');
    
    // Protected channel routes
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/create', ChannelCreate::class)
            ->name('create');
    });
});

// Channel Ads Routes
Route::prefix('channel-ads')->name('channel-ads.')->group(function () {
    // Public channel ads listing (no auth required)
    Route::get('/', ChannelAdList::class)
        ->name('index');
    
    // Protected channel ads routes
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/my-applications', MyChannelApplications::class)
            ->name('my-applications');
    });
});

// Batch Routes
Route::middleware(['auth', 'verified.otp'])->group(function () {
    // Batch download route (handled by BatchList component)
    Route::get('/batches/{batch}/download', function() {
        // This route is handled by the BatchList component's downloadVcf method
        // We just need it for route generation in emails/notifications
        return redirect()->route('home');
    })->name('batches.download');
});

// Paystack Routes
Route::prefix('paystack')->name('paystack.')->group(function () {
    // Payment callback (accessible without auth for Paystack redirects)
    Route::get('/callback', [PaystackController::class, 'callback'])
        ->name('callback');
    
    // Webhook endpoint (no auth required for Paystack webhooks)
    Route::post('/webhook', [PaystackController::class, 'webhook'])
        ->name('webhook');
    
    // API endpoints (require auth)
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/public-key', [PaystackController::class, 'getPublicKey'])
            ->name('public-key');
        
        Route::post('/initialize', [PaystackController::class, 'initializePayment'])
            ->name('initialize');
        
        Route::post('/verify', [PaystackController::class, 'verifyPayment'])
            ->name('verify');
    });
});

require __DIR__.'/auth.php';
