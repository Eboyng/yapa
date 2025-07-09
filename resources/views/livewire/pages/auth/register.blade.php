<?php

use App\Models\User;
use App\Models\PendingUser;
use App\Services\OtpService;
use App\Services\ReferralService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new #[Layout('layouts.guest')] class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';
    
    #[Validate('required|string|lowercase|email|max:255')]
    public string $email = '';
    
    #[Validate('required|string|regex:/^\+?[1-9]\d{1,14}$/')]
    public string $whatsapp_number = '';
    
    #[Validate('required|string|confirmed')]
    public string $password = '';
    
    public string $password_confirmation = '';
    
    #[Validate('nullable|string|max:20')]
    public string $referral_code = '';
    
    #[Validate('boolean')]
    public bool $email_verification_enabled = false;
    
    public bool $isRegistering = false;
    public string $registrationStep = 'form'; // form, otp_verification, success
    public string $otpMessage = '';
    public string $otpMethod = '';

    public function mount(): void
    {
        // Pre-fill referral code from URL parameter
        $this->referral_code = request()->get('ref', '');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $this->isRegistering = true;
        
        try {
            // Validate all fields
            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email', 'unique:pending_users,email'],
                'whatsapp_number' => ['required', 'string', 'regex:/^\+?[1-9]\d{1,14}$/', 'unique:'.User::class.',whatsapp_number', 'unique:pending_users,whatsapp_number'],
                'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
                'referral_code' => ['nullable', 'string', 'max:20'],
                'email_verification_enabled' => ['boolean'],
            ]);

            // Format WhatsApp number
            $validated['whatsapp_number'] = $this->formatWhatsAppNumber($validated['whatsapp_number']);
            
            // Validate referral code if provided
            if (!empty($validated['referral_code'])) {
                $referrer = User::withReferralCode($validated['referral_code'])->first();
                if (!$referrer) {
                    $this->addError('referral_code', 'Invalid referral code.');
                    return;
                }
            }

            // Create pending user
            $pendingUser = PendingUser::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'whatsapp_number' => $validated['whatsapp_number'],
                'password' => Hash::make($validated['password']),
            ]);

            // Send OTP
            $otpService = app(OtpService::class);
            $templates = OtpService::getMessageTemplates();
            
            $result = $otpService->sendOtp(
                $validated['whatsapp_number'],
                $templates['registration'],
                $validated['email'],
                true
            );

            if ($result['success']) {
                // Store registration data in session for OTP verification
                session([
                    'pending_registration' => [
                        'pending_user_id' => $pendingUser->id,
                        'referral_code' => $validated['referral_code'],
                        'email_verification_enabled' => $validated['email_verification_enabled'],
                    ]
                ]);
                
                $this->registrationStep = 'otp_verification';
                $this->otpMessage = 'OTP sent successfully via ' . ucfirst($result['method']);
                $this->otpMethod = $result['method'];
            } else {
                // Store failure reason and allow retry
                $pendingUser->update([
                    'failure_reason' => $result['message']
                ]);
                
                session()->flash('error', 'Failed to send OTP: ' . $result['message'] . '. Please try again or contact support.');
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Registration failed. Please try again.');
        } finally {
            $this->isRegistering = false;
        }
    }
    
    /**
     * Format WhatsApp number to international format.
     */
    private function formatWhatsAppNumber(string $number): string
    {
        // Remove all non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Add + prefix if not present
        if (!str_starts_with($number, '+')) {
            $number = '+' . $number;
        }
        
        return $number;
    }
};

?>

<div class="">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-r from-orange-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">
                Create Your Account
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Join our community and start connecting with like-minded people
            </p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if ($registrationStep === 'form')
            <form wire:submit="register" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Full Name
                    </label>
                    <input 
                        wire:model="name" 
                        id="name" 
                        type="text" 
                        name="name" 
                        required 
                        autofocus 
                        autocomplete="name"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                        placeholder="Enter your full name"
                    />
                    @error('name') 
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.83 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email Address
                    </label>
                    <input 
                        wire:model="email" 
                        id="email" 
                        type="email" 
                        name="email" 
                        required 
                        autocomplete="username"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                        placeholder="Enter your email address"
                    />
                    @error('email') 
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <!-- WhatsApp Number -->
                <div>
                    <label for="whatsapp_number" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        WhatsApp Number
                    </label>
                    <input 
                        wire:model="whatsapp_number" 
                        id="whatsapp_number" 
                        type="tel" 
                        name="whatsapp_number" 
                        required 
                        autocomplete="tel"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                        placeholder="+234XXXXXXXXXX"
                    />
                    @error('whatsapp_number') 
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            wire:model="password" 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-gray-50 focus:bg-white pr-10"
                            placeholder="Create a secure password"
                        />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('password') 
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Confirm Password
                    </label>
                    <div class="relative">
                        <input 
                            wire:model="password_confirmation" 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-gray-50 focus:bg-white pr-10"
                            placeholder="Confirm your password"
                        />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('password_confirmation') 
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <!-- Referral Code -->
                <div>
                    <label for="referral_code" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Referral Code (Optional)
                    </label>
                    <input 
                        wire:model="referral_code" 
                        id="referral_code" 
                        type="text" 
                        name="referral_code" 
                        autocomplete="off"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                        placeholder="Enter referral code (if any)"
                    />
                    @error('referral_code') 
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <!-- Email Verification Toggle -->
                <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-4">
                    <div class="flex items-start">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900 mb-1">Email Verification (Optional)</h4>
                            <p class="text-xs text-gray-600 mb-3">Enable email verification for sensitive actions like withdrawals and WhatsApp number changes.</p>
                            <label class="inline-flex items-center">
                                <input 
                                    wire:model="email_verification_enabled" 
                                    type="checkbox" 
                                    class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">Enable email verification for my account</span>
                            </label>
                        </div>
                    </div>
                </div>

               

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit"
                        class="w-full flex justify-center items-center py-4 px-6 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        wire:loading.attr="disabled"
                        wire:target="register"
                    >
                        <span wire:loading.remove wire:target="register" class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Create Account
                        </span>
                        <span wire:loading wire:target="register" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating Account...
                        </span>
                    </button>
                </div>

                @if ($registrationStep === 'form')
                <!-- Already Registered Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="{{ route('login') }}" 
                           wire:navigate
                           class="font-medium text-orange-600 hover:text-orange-500 transition-colors duration-200 hover:underline">
                            Sign in here
                        </a>
                    </p>
                </div>
                @endif
            </form>
            @elseif ($registrationStep === 'otp_verification')
                <div class="text-center space-y-6">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Verify Your WhatsApp Number</h3>
                    <p class="text-gray-600">{{ $otpMessage }}</p>
                    <p class="text-sm text-gray-500">Please check your {{ ucfirst($otpMethod) }} for the verification code and complete the process on the verification page.</p>
                    
                    <div class="pt-4">
                        <a href="{{ route('verify-otp') }}" 
                           wire:navigate
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 shadow-lg hover:shadow-xl">
                            Continue to Verification
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-xs text-gray-500">
                By creating an account, you agree to our 
                <a href="#" class="text-orange-600 hover:text-orange-500 transition-colors">Terms of Service</a> 
                and 
                <a href="#" class="text-orange-600 hover:text-orange-500 transition-colors">Privacy Policy</a>
            </p>
        </div>
    </div>
    <style>
    /* Input focus animations */
    input:focus {
        transform: scale(1.02);
    }
    
    /* Custom transition for inputs */
    input {
        transition: all 0.2s ease-out;
    }
    
    /* Button ripple effect */
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    /* Loading animation */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    /* Form animations */
    form > div {
        animation: slideUp 0.5s ease-out forwards;
        opacity: 0;
        transform: translateY(20px);
    }
    
    form > div:nth-child(1) { animation-delay: 0.1s; }
    form > div:nth-child(2) { animation-delay: 0.2s; }
    form > div:nth-child(3) { animation-delay: 0.3s; }
    form > div:nth-child(4) { animation-delay: 0.4s; }
    form > div:nth-child(5) { animation-delay: 0.5s; }
    form > div:nth-child(6) { animation-delay: 0.6s; }
    form > div:nth-child(7) { animation-delay: 0.7s; }
    
    @keyframes slideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add ripple effect to submit button
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.addEventListener('click', function(e) {
                if (this.disabled) return;
                
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, 0.5);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                `;
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        }
        
        // Add input focus effects
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
                this.parentElement.style.transition = 'transform 0.2s ease-out';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
        
        // Password strength indicator (optional enhancement)
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        
        if (passwordInput && confirmPasswordInput) {
            // Add real-time password matching feedback
            confirmPasswordInput.addEventListener('input', function() {
                if (this.value && passwordInput.value) {
                    if (this.value === passwordInput.value) {
                        this.style.borderColor = '#10b981';
                        this.style.boxShadow = '0 0 0 2px rgba(16, 185, 129, 0.2)';
                    } else {
                        this.style.borderColor = '#ef4444';
                        this.style.boxShadow = '0 0 0 2px rgba(239, 68, 68, 0.2)';
                    }
                }
            });
        }
        
        // Smooth scrolling for any hash links
        document.documentElement.style.scrollBehavior = 'smooth';
    });
</script>
</div>

