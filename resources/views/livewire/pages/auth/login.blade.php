<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();
        
        // Check if user needs OTP verification
        $user = Auth::user();
        if ($user && !$user->whatsapp_verified_at) {
            // Redirect to OTP verification
            $this->redirect(route('verify-otp'), navigate: true);
            return;
        }

        $this->redirectIntended(default: route('home', absolute: false), navigate: true);
    }
}; ?>

<div class=" flex flex-col sm:justify-center items-center pt-6 sm:pt-0 ">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-sm overflow-hidden sm:rounded-lg">
        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                Welcome Back
            </h2>
            <p class="text-gray-600">
                Sign in to your account
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="login" class="space-y-6">
            <!-- Login Field (Email or WhatsApp) -->
            <div>
                <x-input-label for="login" :value="__('Email or WhatsApp Number')" />
                <x-text-input 
                    wire:model="form.login" 
                    id="login" 
                    class="block mt-1 w-full" 
                    type="text" 
                    name="login" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="Enter your email or WhatsApp number" 
                />
                <x-input-error :messages="$errors->get('form.login')" class="mt-2" />
                <p class="mt-1 text-xs text-gray-500">You can use either your email address or WhatsApp number</p>
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input 
                    wire:model="form.password" 
                    id="password" 
                    class="block mt-1 w-full"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password" 
                    placeholder="Enter your password"
                />
                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember" class="inline-flex items-center">
                    <input 
                        wire:model="form.remember" 
                        id="remember" 
                        type="checkbox" 
                        class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500" 
                        name="remember"
                    >
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
                
                @if (Route::has('password.request'))
                    <a class="text-sm text-orange-600 hover:text-orange-500 transition-colors duration-200 hover:underline" 
                       href="{{ route('password.request') }}" 
                       wire:navigate>
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <div>
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-orange-500 to-purple-500 hover:from-orange-600 hover:to-purple-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 shadow-lg hover:shadow-xl"
                    wire:loading.attr="disabled"
                    wire:target="login"
                >
                    <span wire:loading.remove wire:target="login">
                        {{ __('Sign In') }}
                    </span>
                    <span wire:loading wire:target="login" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Signing In...') }}
                    </span>
                </button>
            </div>

            <!-- Register Link -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('register') }}" 
                       wire:navigate
                       class="font-medium text-orange-600 hover:text-orange-500 transition-colors duration-200 hover:underline">
                        Create one here
                    </a>
                </p>
            </div>
        </form>
      
    </div>
</div>
