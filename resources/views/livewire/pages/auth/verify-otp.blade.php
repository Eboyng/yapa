<?php

use App\Livewire\Actions\Logout;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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

    public function mount(): void
    {
        // Redirect if already verified
        if (Auth::user()->whatsapp_verified_at) {
            $this->redirect(route('home'), navigate: true);
            return;
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
            $user = Auth::user();
            $otpService = app(OtpService::class);
            
            // Check if can resend
            $canResend = $otpService->canResendOtp($user->whatsapp_number, 'login');
            
            if (!$canResend['can_resend']) {
                Session::flash('error', $canResend['message']);
                $this->checkResendStatus();
                return;
            }
            
            // Send OTP
            $templates = OtpService::getMessageTemplates();
            $result = $otpService->sendOtp(
                $user->whatsapp_number,
                $templates['login']
            );
            
            if ($result['success']) {
                $otpService->trackResend($user->whatsapp_number, 'login');
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
            $user = Auth::user();
            $otpService = app(OtpService::class);
            
            $result = $otpService->verifyOtp(
                $user->whatsapp_number,
                $this->otp,
                'login'
            );
            
            if ($result['success']) {
                // Mark WhatsApp as verified
                $user->update([
                    'whatsapp_verified_at' => now(),
                ]);
                
                Session::flash('status', 'otp-verified');
                $this->redirect(route('home'), navigate: true);
                return;
            }
            
            // Handle verification failure
            if (!$result['can_retry']) {
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
     * Check resend status.
     */
    private function checkResendStatus(): void
    {
        $user = Auth::user();
        $otpService = app(OtpService::class);
        
        $canResend = $otpService->canResendOtp($user->whatsapp_number, 'login');
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
        <strong>WhatsApp Number:</strong> {{ Auth::user()->whatsapp_number }}
    </div>

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
                
                <button 
                    wire:click="logout" 
                    type="button" 
                    class="mt-2 underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Log Out') }}
                </button>
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