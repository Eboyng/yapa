<?php

use App\Livewire\Actions\Logout;
use App\Services\OtpService;
use App\Services\ReferralService;
use App\Services\TransactionService;
use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new #[Layout('layouts.guest')] class extends Component
{
    #[Validate('required|string|size:6')]
    public string $otp = '';
    
    public bool $canResend = true;
    public string $resendMessage = '';
    public bool $isVerifying = false;
    public bool $isSending = false;
    public ?PendingUser $pendingUser = null;
    public string $whatsappNumber = '';
    public string $userType = 'existing'; // 'existing' or 'pending'

    public function mount(): void
    {
        // Check if this is for a pending user registration
        $registrationData = Session::get('registration_data');
        
        if ($registrationData && isset($registrationData['pending_user_id'])) {
            // This is a pending user registration
            $this->pendingUser = PendingUser::find($registrationData['pending_user_id']);
                
            if ($this->pendingUser) {
                $this->userType = 'pending';
                $this->whatsappNumber = $this->pendingUser->whatsapp_number;
            } else {
                // Pending user not found or expired, redirect to register
                Session::forget('registration_data');
                Session::flash('error', 'Registration session expired. Please register again.');
                $this->redirect(route('register'), navigate: true);
                return;
            }
        } else {
            // This is for an existing authenticated user
            if (!Auth::check()) {
                $this->redirect(route('login'), navigate: true);
                return;
            }
            
            $user = Auth::user();
            
            // Redirect if already verified
            if ($user->whatsapp_verified_at) {
                $this->redirect(route('home'), navigate: true);
                return;
            }
            
            $this->userType = 'existing';
            $this->whatsappNumber = $user->whatsapp_number ?? '';
        }
        
        $this->checkResendStatus();
    }

    /**
     * Send OTP to user's WhatsApp number.
     */
    public function sendOtp(): void
    {
        $this->isSending = true;
        
        try {
            $otpService = app(OtpService::class);
            
            // Check if WhatsApp number is available
            if (!$this->whatsappNumber) {
                Session::flash('error', 'WhatsApp number not available. Please try again.');
                return;
            }
            
            // Determine the context based on user type
            $context = $this->userType === 'pending' ? 'registration' : 'login';
            
            // Check if can resend
            $canResend = $otpService->canResendOtp($this->whatsappNumber, $context);
            
            if (!$canResend['can_resend']) {
                Session::flash('error', $canResend['message']);
                $this->checkResendStatus();
                return;
            }
            
            // Send OTP
            $templates = OtpService::getMessageTemplates();
            $template = $this->userType === 'pending' ? $templates['registration'] : $templates['login'];
            
            $result = $otpService->sendOtp($this->whatsappNumber, $template);
            
            if ($result['success']) {
                $otpService->trackResend($this->whatsappNumber, $context);
                
                // Update pending user if applicable
                if ($this->pendingUser) {
                    $this->pendingUser->increment('resend_attempts');
                    $this->pendingUser->update(['last_resend_at' => now()]);
                }
                
                Session::flash('status', 'otp-sent');
                Session::flash('method', $result['method']);
            } else {
                Session::flash('error', $result['message']);
            }
            
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to send OTP. Please try again.');
        } finally {
            $this->isSending = false;
            $this->checkResendStatus();
        }
    }

    /**
     * Verify the OTP code.
     */
    public function verifyOtp(): void
    {
        $this->validate();
        $this->isVerifying = true;
        
        try {
            $otpService = app(OtpService::class);
            
            // Check if WhatsApp number is available
            if (!$this->whatsappNumber) {
                $this->addError('otp', 'WhatsApp number not available. Please try again.');
                return;
            }
            
            // Determine the context based on user type
            $context = $this->userType === 'pending' ? 'registration' : 'login';
            
            $result = $otpService->verifyOtp(
                $this->whatsappNumber,
                $this->otp,
                $context
            );
            
            if ($result['success']) {
                if ($this->userType === 'pending') {
                    // Complete pending user registration
                    $this->completePendingRegistration();
                } else {
                    // Mark existing user's WhatsApp as verified
                    $user = Auth::user();
                    $user->update([
                        'whatsapp_verified_at' => now(),
                    ]);
                    
                    Session::flash('status', 'otp-verified');
                    $this->redirect(route('home'), navigate: true);
                }
                return;
            }
            
            // Handle verification failure
            if (!$result['can_retry']) {
                if ($this->userType === 'pending' && $this->pendingUser) {
                    // Mark pending user as failed if max attempts reached
                    $this->pendingUser->update([
                        'failure_reason' => $result['message']
                    ]);
                }
                Session::flash('error', $result['message']);
                $this->otp = '';
            } else {
                $this->addError('otp', $result['message']);
            }
            
        } catch (\Exception $e) {
            $this->addError('otp', 'Verification failed. Please try again.');
        } finally {
            $this->isVerifying = false;
        }
    }
    
    /**
     * Complete pending user registration after successful OTP verification.
     */
    private function completePendingRegistration(): void
    {
        if (!$this->pendingUser) {
            Session::flash('error', 'Registration data not found.');
            $this->redirect(route('register'), navigate: true);
            return;
        }
        
        DB::transaction(function () {
            $registrationData = Session::get('registration_data', []);
            $emailVerificationEnabled = $registrationData['email_verification_enabled'] ?? false;
            $referralCode = $registrationData['referral_code'] ?? null;
            
            // Find referrer if referral code provided
            $referrer = null;
            if (!empty($referralCode)) {
                $referrer = User::withReferralCode($referralCode)->first();
            }
            
            // Create the actual user
            $user = User::create([
                'name' => $this->pendingUser->name,
                'email' => $this->pendingUser->email,
                'whatsapp_number' => $this->pendingUser->whatsapp_number,
                'password' => $this->pendingUser->password, // Already hashed
                'whatsapp_verified_at' => now(),
                'referred_by' => $referrer?->id,
                'referred_at' => $referrer ? now() : null,
                'email_verification_enabled' => $emailVerificationEnabled,
            ]);
            
            // Wallets are automatically created with default balances via User::boot()
            // No need to manually credit registration bonus
            
            // Process referral if applicable
            if ($referrer) {
                $referralService = app(ReferralService::class);
                $referralService->processReferral($user, 'registration');
            }
            
            // Send email verification if enabled
            if ($emailVerificationEnabled) {
                $user->sendEmailVerificationNotification();
            }
            
            // Clean up
            $this->pendingUser->delete();
            Session::forget('registration_data');
            
            // Log the user in
            Auth::login($user);
            
            $message = 'Registration completed successfully! You have been credited with 100 free credits.';
            if ($emailVerificationEnabled) {
                $message .= ' Please check your email to verify your email address.';
            }
            
            Session::flash('status', 'registration-completed');
            Session::flash('message', $message);
        });
        
        $this->redirect(route('home'), navigate: true);
    }

    /**
     * Check resend status.
     */
    private function checkResendStatus(): void
    {
        $otpService = app(OtpService::class);
        
        // Check if WhatsApp number is available
        if (!$this->whatsappNumber) {
            $this->canResend = false;
            $this->resendMessage = 'WhatsApp number not available';
            return;
        }
        
        // Determine the context based on user type
        $context = $this->userType === 'pending' ? 'registration' : 'login';
        
        $canResend = $otpService->canResendOtp($this->whatsappNumber, $context);
        $this->canResend = $canResend['can_resend'];
        $this->resendMessage = $canResend['message'];
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-orange-50 flex items-center justify-center p-4">
    <!-- Decorative background elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-orange-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <div class="relative w-full max-w-md">
        <!-- Main card -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-sm p-8 md:p-10 transform transition-all duration-500 hover:scale-[1.01]">
            
            <!-- WhatsApp Icon and Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-orange-500 rounded-full mb-4 animate-float">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.149-.67.149-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.123-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-purple-600 to-orange-600 bg-clip-text text-transparent">
                    Verify WhatsApp
                </h2>
                <p class="text-gray-600 mt-2 text-sm md:text-base">
                    Enter the 6-digit code we sent to
                </p>
                @if($whatsappNumber)
                    <div class="inline-flex items-center mt-2 px-4 py-2 bg-gradient-to-r from-purple-100 to-orange-100 rounded-full">
                        <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="font-semibold text-gray-800">{{ $whatsappNumber }}</span>
                    </div>
                @else
                    <span class="inline-block mt-2 px-4 py-2 bg-red-100 text-red-600 rounded-full text-sm">
                        Number not available
                    </span>
                @endif
            </div>

            <!-- Status Messages -->
            @if (session('status') == 'otp-sent')
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl transform animate-slideDown">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600 animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <p class="ml-3 text-sm text-green-800">
                            Code sent via {{ ucfirst(session('method', 'WhatsApp')) }}! Check your messages.
                        </p>
                    </div>
                </div>
            @endif

            @if (session('status') == 'otp-verified')
                <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl transform animate-slideDown">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="ml-3 text-sm text-green-800 font-medium">
                            Verified successfully! Redirecting...
                        </p>
                    </div>
                </div>
            @endif

            @if (session('status') == 'registration-completed')
                <div class="mb-6 p-4 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-2xl transform animate-slideDown">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                        </svg>
                        <p class="ml-3 text-sm text-purple-800">
                            {{ session('message') }}
                        </p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl transform animate-shake">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="ml-3 text-sm text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            @endif

            @if($userType === 'pending')
                <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-blue-900">Complete Your Registration</h3>
                            <p class="mt-1 text-sm text-blue-700">
                                Verify your number to finish creating your account and receive your <span class="font-bold">100 free credits!</span>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- OTP Input Form -->
            <form wire:submit="verifyOtp" class="space-y-6">
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">
                        Verification Code
                    </label>
                    <div class="relative group">
                        <input 
                            wire:model="otp"
                            id="otp"
                            type="text"
                            maxlength="6"
                            placeholder="000000"
                            required
                            autofocus
                            autocomplete="one-time-code"
                            class="w-full px-6 py-4 text-center text-2xl font-bold tracking-[0.5em] border-2 border-gray-200 rounded-2xl focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-300 hover:border-gray-300 @error('otp') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                        />
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-orange-600 rounded-2xl opacity-0 group-hover:opacity-10 transition-opacity duration-300 pointer-events-none"></div>
                    </div>
                    @error('otp')
                        <p class="mt-2 text-sm text-red-600 flex items-center animate-slideDown">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="space-y-4">
                    <button
                        type="submit"
                        :disabled="$isVerifying"
                        class="w-full py-4 px-6 bg-gradient-to-r from-purple-600 to-orange-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transform transition-all duration-300 hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 focus:outline-none focus:ring-4 focus:ring-purple-200"
                    >
                        @if($isVerifying)
                            <span class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Verifying...
                            </span>
                        @else
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Verify Code
                            </span>
                        @endif
                    </button>

                    <div class="flex items-center justify-between">
                        <div>
                            @if($canResend)
                                <button
                                    wire:click="sendOtp"
                                    type="button"
                                    :disabled="$isSending"
                                    class="text-purple-600 hover:text-orange-600 font-medium text-sm transition-colors duration-300 flex items-center group disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    @if($isSending)
                                        <svg class="animate-spin mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Sending...
                                    @else
                                        <svg class="w-4 h-4 mr-2 group-hover:rotate-45 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Resend Code
                                    @endif
                                </button>
                            @else
                                <span class="text-xs text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $resendMessage }}
                                </span>
                            @endif
                        </div>

                        @if($userType === 'existing')
                            <button
                                wire:click="logout"
                                type="button"
                                class="text-gray-600 hover:text-red-600 font-medium text-sm transition-colors duration-300 flex items-center group"
                            >
                                <svg class="w-4 h-4 mr-1 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Log Out
                            </button>
                        @else
                            <a 
                                href="{{ route('register') }}"
                                wire:navigate
                                class="text-gray-600 hover:text-purple-600 font-medium text-sm transition-colors duration-300 flex items-center group"
                            >
                                <svg class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Registration
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Security Note -->
            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="flex items-center justify-center text-xs text-gray-500">
                    <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Secure verification • End-to-end encrypted</span>
                </div>
            </div>
        </div>
    </div>
    <style>
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
        20%, 40%, 60%, 80% { transform: translateX(2px); }
    }
    
    .animate-blob {
        animation: blob 7s infinite;
    }
    
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    
    .animation-delay-4000 {
        animation-delay: 4s;
    }
    
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    
    .animate-slideDown {
        animation: slideDown 0.3s ease-out;
    }
    
    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
</style>

<script>
    // Auto-submit when 6 digits are entered
    document.addEventListener('DOMContentLoaded', function() {
        const otpInput = document.getElementById('otp');
        if (otpInput) {
            otpInput.addEventListener('input', function(e) {
                // Add haptic feedback on mobile
                if (navigator.vibrate) {
                    navigator.vibrate(10);
                }
                
                if (e.target.value.length === 6) {
                    // Visual feedback
                    e.target.classList.add('ring-4', 'ring-green-200');
                    
                    // Small delay to ensure Livewire has updated the model
                    setTimeout(() => {
                        @this.verifyOtp();
                    }, 100);
                }
            });
            
            // Focus effect
            otpInput.addEventListener('focus', function() {
                this.parentElement.classList.add('scale-105');
            });
            
            otpInput.addEventListener('blur', function() {
                this.parentElement.classList.remove('scale-105');
            });
        }
    });
</script>
</div>

