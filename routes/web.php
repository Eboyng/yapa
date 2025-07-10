<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaystackController;
use App\Livewire\CreditPurchase;
use App\Livewire\TransactionHistory;
use App\Livewire\BatchList;
use App\Livewire\ChannelCreate;
use App\Livewire\ChannelAdList;
use App\Livewire\MyChannelApplications;
use App\Livewire\AdList;
use App\Livewire\AdTask;
use App\Livewire\AdTaskHistory;
use App\Livewire\Profile;
use App\Livewire\MyBatches;

// Homepage - Batch List (protected by auth and verified.otp middleware)
Route::get('/', BatchList::class)->name('home');

// Redirect unauthenticated users to login
Route::redirect('/welcome', '/login');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/profile', Profile::class)
    ->middleware(['auth', 'verified.otp'])
    ->name('profile');

Route::get('/my-batches', MyBatches::class)
    ->middleware(['auth', 'verified.otp'])
    ->name('my-batches');

Route::get('/referrals', \App\Livewire\Referrals::class)
    ->middleware(['auth', 'verified.otp'])
    ->name('referrals');

// Credit System Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Credit Purchase
    Route::get('/credits/purchase', CreditPurchase::class)
        ->name('credits.purchase');
    

    
    // Transaction History
    Route::get('/transactions', TransactionHistory::class)
        ->name('transactions.index');
});

// Channel Routes
Route::prefix('channels')->name('channels.')->group(function () {
    // Redirect to channel ads (since channel list is redundant)
    Route::get('/', function() {
        return redirect()->route('channel-ads.index');
    })->name('index');
    
    // Channel show page (public access for viewing and booking)
    Route::get('/{channelAd}', \App\Livewire\ChannelShow::class)
        ->name('show');
    
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

// Channel Bookings Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/channel-bookings', \App\Livewire\ChannelBookings::class)
        ->name('channel-bookings.index');
});

// Channel Sale Marketplace Routes
Route::prefix('channel-sale')->name('channel-sale.')->group(function () {
    // Public browse page (no auth required)
    Route::get('/', \App\Livewire\ChannelSale\Browse::class)
        ->name('browse');
    
    // Protected routes requiring authentication
    Route::middleware(['auth', 'verified'])->group(function () {
        // Seller routes
        Route::get('/create', \App\Livewire\ChannelSale\CreateListing::class)
            ->name('create');
        Route::get('/my-listings', \App\Livewire\ChannelSale\MyListings::class)
            ->name('my-listings');
        
        // Buyer routes
        Route::get('/buy/{channelSale}', \App\Livewire\ChannelSale\BuyNow::class)
            ->name('buy-now');
        Route::get('/my-purchases', \App\Livewire\ChannelSale\MyPurchases::class)
            ->name('my-purchases');
        Route::get('/confirm/{purchase}', \App\Livewire\ChannelSale\BuyerConfirm::class)
            ->name('buyer-confirm');
        
        // Purchase details route
        Route::get('/purchase/{purchase}', function(\App\Models\ChannelPurchase $purchase) {
            // Ensure user can only view their own purchases
            if ($purchase->buyer_id !== auth()->id()) {
                abort(403);
            }
            return redirect()->route('channel-sale.buyer-confirm', $purchase);
        })->name('purchase-details');
    });
});

// Ad (Share & Earn) Routes
Route::prefix('ads')->name('ads.')->middleware(['auth',  'ads.enabled'])->group(function () {
    // Ad listing page
    Route::get('/', AdList::class)
        ->name('index');
    
    // Ad task page
    Route::get('/task/{adTask}', AdTask::class)
        ->name('task');
    
    // Ad task history
    Route::get('/tasks', AdTaskHistory::class)
        ->name('tasks');
});

// Notification Routes
Route::middleware(['auth', 'verified.otp'])->group(function () {
    Route::get('/notifications', function() {
        // For now, redirect to profile where notifications can be managed
        // In the future, this could be a dedicated notifications page
        return redirect()->route('profile');
    })->name('notifications.index');
});

// Impersonation Routes
Route::middleware(['auth', 'verified.otp'])->group(function () {
    Route::post('/impersonate/{user}', function(\App\Models\User $user) {
        // Store the original admin ID in session
        session(['original_admin_id' => auth()->id()]);
        
        // Log the impersonation
        \Illuminate\Support\Facades\Log::info('Admin impersonation started', [
            'admin_id' => auth()->id(),
            'target_user_id' => $user->id,
            'admin_email' => auth()->user()->email,
            'target_user_email' => $user->email,
        ]);
        
        // Login as the target user
        auth()->login($user);
        
        return redirect()->route('home')->with('success', 'You are now impersonating ' . $user->name);
    })->name('impersonate.start');
    
    Route::post('/stop-impersonation', function() {
        $originalAdminId = session('original_admin_id');
        
        if ($originalAdminId) {
            $originalAdmin = \App\Models\User::find($originalAdminId);
            
            if ($originalAdmin) {
                // Log the end of impersonation
                \Illuminate\Support\Facades\Log::info('Admin impersonation ended', [
                    'admin_id' => $originalAdminId,
                    'impersonated_user_id' => auth()->id(),
                    'admin_email' => $originalAdmin->email,
                    'impersonated_user_email' => auth()->user()->email,
                ]);
                
                // Login back as the original admin
                auth()->login($originalAdmin);
                session()->forget('original_admin_id');
                
                return redirect()->route('home')->with('success', 'Impersonation ended. You are now logged in as ' . $originalAdmin->name);
            }
        }
        
        return redirect()->route('home')->with('error', 'Unable to end impersonation.');
    })->name('impersonate.stop');
});

// Tips Routes
Route::prefix('tips')->name('tips.')->group(function () {
    // Public tips listing (no auth required)
    Route::get('/', \App\Livewire\Tips\ListTips::class)
        ->name('index');
    
    // Individual tip page (no auth required)
    Route::get('/{tip:slug}', \App\Livewire\Tips\ShowTip::class)
        ->name('show');
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

// Google OAuth Routes
Route::prefix('google')->name('google.')->group(function () {
    Route::get('/callback', [\App\Http\Controllers\GoogleOAuthController::class, 'callback'])
        ->name('callback');
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
