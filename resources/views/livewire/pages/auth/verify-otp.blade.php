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
        
        if ($registrationData && isset($registrationData['whatsapp_number'])) {
            // This is a pending user registration
            $this->pendingUser = PendingUser::where('whatsapp_number', $registrationData['whatsapp_number'])
                ->where('expires_at', '>', now())
                ->first();
                
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
            $emailVerificationEnabled = Session::get('registration_data.email_verification_enabled', false);
            
            // Create the actual user
            $user = User::create([
                'name' => $this->pendingUser->name,
                'email' => $this->pendingUser->email,
                'whatsapp_number' => $this->pendingUser->whatsapp_number,
                'password' => $this->pendingUser->password, // Already hashed
                'whatsapp_verified_at' => now(),
                'referral_code' => $this->pendingUser->referral_code,
                'referred_by' => $this->pendingUser->referred_by,
                'referred_at' => $this->pendingUser->referred_by ? now() : null,
                'email_verification_enabled' => $emailVerificationEnabled,
            ]);
            
            // Credit 100 free credits
            $transactionService = app(TransactionService::class);
            $transactionService->credit(
                $user->id,
                100,
                'registration_bonus',
                'Welcome bonus - 100 free credits'
            );
            
            // Process referral if applicable
            if ($this->pendingUser->referred_by) {
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

<div>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Please verify your WhatsApp number to continue. We\'ll send a 6-digit verification code to your registered WhatsApp number.') }}
    </div>

    <div class="mb-4 text-sm text-gray-500">
        <strong>WhatsApp Number:</strong> 
        @if($whatsappNumber)
            {{ $whatsappNumber }}
        @else
            <span class="text-red-500">Not available</span>
        @endif
    </div>
    
    @if($userType === 'pending')
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-blue-800">
                    <strong>Complete Your Registration:</strong> Please verify your WhatsApp number to finish creating your account and receive your 100 free credits.
                </p>
            </div>
        </div>
    @endif

    @if (session('status') == 'otp-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Verification code sent via ' . ucfirst(session('method', 'WhatsApp')) . '. Please check your messages.') }}
        </div>
    @endif

    @if (session('status') == 'otp-verified')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('WhatsApp number verified successfully! Redirecting...') }}
        </div>
    @endif
    
    @if (session('status') == 'registration-completed')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 font-medium text-sm text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="verifyOtp">
        <div class="mb-4">
            <x-input-label for="otp" :value="__('Verification Code')" />
            <x-text-input 
                wire:model="otp" 
                id="otp" 
                class="block mt-1 w-full text-center text-lg tracking-widest" 
                type="text" 
                maxlength="6"
                placeholder="000000"
                required 
                autofocus 
                autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <x-primary-button :disabled="$isVerifying">
                @if($isVerifying)
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Verifying...') }}
                @else
                    {{ __('Verify Code') }}
                @endif
            </x-primary-button>

            <div class="flex flex-col items-end">
                @if($canResend)
                    <button 
                        wire:click="sendOtp" 
                        type="button" 
                        :disabled="$isSending"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        @if($isSending)
                            {{ __('Sending...') }}
                        @else
                            {{ __('Resend Code') }}
                        @endif
                    </button>
                @else
                    <span class="text-xs text-gray-500">{{ $resendMessage }}</span>
                @endif
                
                @if($userType === 'existing')
                    <button 
                        wire:click="logout" 
                        type="button" 
                        class="mt-2 underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Log Out') }}
                    </button>
                @else
                    <a href="{{ route('register') }}" 
                       wire:navigate
                       class="mt-2 underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Back to Registration') }}
                    </a>
                @endif
            </div>
        </div>
    </form>

    <script>
        // Auto-submit when 6 digits are entered
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.addEventListener('input', function(e) {
                    if (e.target.value.length === 6) {
                        // Small delay to ensure Livewire has updated the model
                        setTimeout(() => {
                            @this.verifyOtp();
                        }, 100);
                    }
                });
            }
        });
    </script>
</div>