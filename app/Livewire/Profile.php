<?php

namespace App\Livewire;

use App\Models\Interest;
use App\Models\User;
use App\Models\Wallet;
use App\Models\BatchMember;
use App\Models\Batch;
use App\Services\OtpService;
use App\Services\TransactionService;
use App\Services\WhatsAppService;
use App\Services\AvatarService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Otp;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;
    
    protected $listeners = [
        'connectGoogle' => 'connectGoogle',
        'disconnectGoogle' => 'disconnectGoogle',
    ];

    // User data
    public $user;
    public $name;
     public $email; // Add this line
    public $location;
    public $selectedInterests = [];
    public $notifyWhatsapp;
    public $emailVerificationEnabled;
    
    // WhatsApp number change
    public $newWhatsappNumber = '';
    public $whatsappChangeStep = 'password'; // password, otp
    public $whatsappOtpCode = '';
    public $isChangingWhatsapp = false;
    public $whatsappCountryCode = '+234'; // Default to Nigeria
    public $whatsappNumberWithoutCode = '';
    public $password;
    public $otp;
    public $otpSent = false;
    public $otpAttempts = 0;
    public $maxOtpAttempts = 3;
    
    // Google People API
    public $googleConnected = false;
    
    // Profile picture
    public $profilePicture;
    

    
    // Password change
    public $current_password;
    public $new_password;
    public $confirm_password;
    
    // Loading states
    public $isUpdatingProfile = false;
    public $isSendingOtp = false;
    public $isVerifyingOtp = false;
    public $isConnectingGoogle = false;

    public $isUpdatingPassword = false;
    
    // Available interests (cached)
    public $availableInterests = [];
    
    // Edit mode for interests
    public $editingInterests = false;
    
    // Nigerian states for location dropdown
    public $nigerianStates = [
        'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue',
        'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu',
        'FCT - Abuja', 'Gombe', 'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina',
        'Kebbi', 'Kogi', 'Kwara', 'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo',
        'Osun', 'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'location' => 'nullable|string|max:255',
        'selectedInterests' => 'array|max:5',
        'selectedInterests.*' => 'exists:interests,id',
        'newWhatsappNumber' => 'nullable|string|regex:/^\+234[0-9]{10}$/|unique:users,whatsapp_number',
        'password' => 'nullable|string|min:8',
        'otp' => 'nullable|string|size:6',
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:8|confirmed',
        'confirm_password' => 'required|string|min:8',
    ];

    protected $messages = [
        'newWhatsappNumber.regex' => 'WhatsApp number must be in format +234XXXXXXXXXX',
        'newWhatsappNumber.unique' => 'This WhatsApp number is already registered',
        'selectedInterests.max' => 'You can select maximum 5 interests',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->location = $this->user->location;
        
        // Load user's selected interests from relationship
        $this->selectedInterests = $this->user->interests()->pluck('interests.id')->toArray();
        
        // Load available interests for selection
        $this->availableInterests = Interest::active()->ordered()->get();
        
        $this->notifyWhatsapp = $this->user->whatsapp_notifications_enabled;
        $this->emailVerificationEnabled = $this->user->email_verification_enabled;
        
        // Initialize WhatsApp number fields
        if ($this->user->whatsapp_number) {
            $this->initializeWhatsAppFields();
        }
        
        $this->googleConnected = !empty($this->user->google_access_token);
    }

    public function updateProfile()
    {
        $this->isUpdatingProfile = true;
        
        try {
            // Handle interests from Alpine.js (comes as JSON string)
            if (is_string($this->selectedInterests)) {
                $this->selectedInterests = json_decode($this->selectedInterests, true) ?? [];
            }
            
            $this->validate([
                'name' => 'required|string|max:255',
                'location' => 'nullable|string|max:255',
                'selectedInterests' => 'array|max:5',
                'selectedInterests.*' => 'exists:interests,id',
            ]);
            
            // Update user basic info
            $this->user->update([
                'name' => $this->name,
                'location' => $this->location,
            ]);
            
            // Update user interests using the relationship
            $this->user->interests()->sync($this->selectedInterests);
            
            // Exit edit mode for interests
            $this->editingInterests = false;
            
            session()->flash('success', 'Profile updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', $e->getMessage());
        } finally {
            $this->isUpdatingProfile = false;
        }
    }

    public function updatePassword()
    {
        $this->isUpdatingPassword = true;
        
        try {
            $this->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
                'confirm_password' => 'required|string|min:8',
            ]);

            // Verify current password
            if (!Hash::check($this->current_password, $this->user->password)) {
                throw new \Exception('Current password is incorrect.');
            }

            // Check if new password is different from current
            if (Hash::check($this->new_password, $this->user->password)) {
                throw new \Exception('New password must be different from current password.');
            }

            // Update password
            $this->user->update([
                'password' => Hash::make($this->new_password)
            ]);

            // Reset form
            $this->current_password = '';
            $this->new_password = '';
            $this->confirm_password = '';

            session()->flash('success', 'Password updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Password update failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', $e->getMessage());
        } finally {
            $this->isUpdatingPassword = false;
        }
    }

    public function initiateNumberChange()
    {
        $this->isSendingOtp = true;
        
        try {
            $this->validate([
                'newWhatsappNumber' => 'required|string|regex:/^\+234[0-9]{10}$/|unique:users,whatsapp_number,' . $this->user->id,
                'password' => 'required|string',
            ]);

            // Verify password
            if (!Hash::check($this->password, $this->user->password)) {
                throw new \Exception('Incorrect password');
            }

            // Check if user has sufficient credits (100 credits required)
            $creditsWallet = $this->user->getWallet('credits');
            if ($creditsWallet->balance < 100) {
                throw new \Exception('Insufficient credits. You need 100 credits to change your WhatsApp number.');
            }

            // Generate and store OTP using the new OTP model
            $otpData = Otp::generate($this->newWhatsappNumber, 'whatsapp_change', $this->user->id, 5);
            $otp = $otpData['otp'];
            
            $message = 'Your YAPA WhatsApp number change verification code is: {otp}. This code expires in 5 minutes.';
            $formattedMessage = str_replace('{otp}', $otp, $message);
            
            // Send via WhatsApp service directly
            $whatsAppService = app(WhatsAppService::class);
            try {
                $notificationLog = new \App\Models\NotificationLog();
                $whatsAppService->send($this->newWhatsappNumber, $formattedMessage, $notificationLog);
                $result = ['success' => true, 'message' => 'OTP sent successfully'];
            } catch (\Exception $e) {
                $result = ['success' => false, 'message' => $e->getMessage()];
            }

            if ($result['success']) {
                $this->otpSent = true;
                $this->otpAttempts = 0;
                session()->flash('success', 'OTP sent to your new WhatsApp number.');
            } else {
                throw new \Exception($result['message'] ?? 'Failed to send OTP');
            }
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        } finally {
            $this->isSendingOtp = false;
        }
    }

    public function verifyNumberChange()
    {
        $this->isVerifyingOtp = true;
        
        try {
            $this->validate([
                'otp' => 'required|string|size:6',
            ]);

            if ($this->otpAttempts >= $this->maxOtpAttempts) {
                throw new \Exception('Maximum OTP attempts exceeded. Please try again later.');
            }

            // Verify OTP using the new OTP model
            $result = Otp::verify($this->newWhatsappNumber, $this->otp, 'whatsapp_change');

            if (!$result['success']) {
                $this->otpAttempts++;
                throw new \Exception($result['message'] ?? 'Invalid OTP. Please try again.');
            }

            DB::transaction(function () {
                $oldNumber = $this->user->whatsapp_number;
                
                // Debit 100 credits
                $transactionService = app(TransactionService::class);
                $transactionService->debit(
                    $this->user->id,
                    100,
                    'credits',
                    'number_change',
                    'WhatsApp number change fee',
                    null,
                    'profile_update'
                );

                // Update user's WhatsApp number
                $this->user->update([
                    'whatsapp_number' => $this->newWhatsappNumber
                ]);

                // Update BatchMember records
                BatchMember::where('user_id', $this->user->id)
                    ->update(['whatsapp_number' => $this->newWhatsappNumber]);

                // Send notifications to both old and new numbers
                $whatsAppService = app(WhatsAppService::class);
                
                // Notify old number
                if ($oldNumber) {
                    $oldMessage = "Your YAPA account WhatsApp number has been changed to {$this->newWhatsappNumber}. If this wasn't you, contact support immediately.";
                    try {
                        $whatsAppService->send($oldNumber, $oldMessage, new \App\Models\NotificationLog());
                    } catch (\Exception $e) {
                        Log::warning('Failed to notify old WhatsApp number', ['error' => $e->getMessage()]);
                    }
                }
                
                // Notify new number
                $newMessage = "Welcome! Your YAPA account WhatsApp number has been successfully updated to this number.";
                try {
                    $whatsAppService->send($this->newWhatsappNumber, $newMessage, new \App\Models\NotificationLog());
                } catch (\Exception $e) {
                    Log::warning('Failed to notify new WhatsApp number', ['error' => $e->getMessage()]);
                }
                
                // Refresh user data
                $this->user->refresh();
                $this->user->load(['interests', 'wallets']);
            });

            // Reset form
            $this->resetNumberChangeForm();
            session()->flash('success', 'WhatsApp number updated successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        } finally {
            $this->isVerifyingOtp = false;
        }
    }

    public function connectGoogle()
    {
        $this->isConnectingGoogle = true;
        
        try {
            Log::info('Google OAuth connection attempt started', [
                'user_id' => $this->user->id,
                'user_email' => $this->user->email
            ]);
            
            $settingService = app(\App\Services\SettingService::class);
            
            // Check if Google OAuth is enabled
            if (!$settingService->isGoogleOAuthEnabled()) {
                Log::warning('Google OAuth connection failed - OAuth disabled', [
                    'user_id' => $this->user->id
                ]);
                $this->dispatch('google-oauth-error', ['message' => 'Google OAuth is currently disabled. Please contact administrator.']);
                $this->isConnectingGoogle = false;
                return;
            }
            
            $googleSettings = $settingService->getGoogleOAuthSettings();
            
            Log::info('Google OAuth settings retrieved', [
                'user_id' => $this->user->id,
                'has_client_id' => !empty($googleSettings['google_client_id']),
                'has_client_secret' => !empty($googleSettings['google_client_secret']),
                'redirect_uri' => $googleSettings['google_redirect_uri'] ?? 'not_set'
            ]);
            
            // Validate required settings
            if (empty($googleSettings['google_client_id'])) {
                Log::error('Google OAuth connection failed - missing client_id', [
                    'user_id' => $this->user->id,
                    'settings' => array_keys($googleSettings)
                ]);
                $this->dispatch('google-oauth-error', ['message' => 'Google OAuth is not properly configured. Missing Client ID. Please contact administrator.']);
                $this->isConnectingGoogle = false;
                return;
            }
            
            if (empty($googleSettings['google_client_secret'])) {
                Log::error('Google OAuth connection failed - missing client_secret', [
                    'user_id' => $this->user->id
                ]);
                $this->dispatch('google-oauth-error', ['message' => 'Google OAuth is not properly configured. Missing Client Secret. Please contact administrator.']);
                $this->isConnectingGoogle = false;
                return;
            }
            
            // Generate Google OAuth URL
            $clientId = $googleSettings['google_client_id'];
            $redirectUri = $googleSettings['google_redirect_uri'] ?: route('google.callback');
            $scopes = $googleSettings['google_scopes'] ?: 'https://www.googleapis.com/auth/contacts.readonly openid profile email';
            $scope = is_array($scopes) ? implode(' ', $scopes) : $scopes;
            $state = base64_encode(json_encode([
                'user_id' => $this->user->id, 
                'action' => 'connect',
                'timestamp' => time()
            ]));
            
            $googleAuthUrl = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'scope' => $scope,
                'response_type' => 'code',
                'access_type' => 'offline',
                'state' => $state,
                'prompt' => 'consent'
            ]);
            
            Log::info('Google OAuth URL generated successfully', [
                'user_id' => $this->user->id,
                'redirect_uri' => $redirectUri,
                'scopes' => $scope,
                'url_length' => strlen($googleAuthUrl)
            ]);
            
            // Dispatch browser event to redirect to Google
            $this->dispatch('google-oauth-redirect', ['url' => $googleAuthUrl]);
            
        } catch (\Throwable $e) {
            Log::error('Google OAuth connection failed with exception', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('google-oauth-error', [
                'message' => 'Failed to connect to Google. Error: ' . $e->getMessage()
            ]);
        } finally {
            $this->isConnectingGoogle = false;
        }
    }

    public function disconnectGoogle()
    {
        try {
            $this->user->update([
                'google_access_token' => null,
                'google_refresh_token' => null,
                'google_people_cache' => null,
            ]);
            
            $this->googleConnected = false;
            
            // Log the disconnection
            Log::info('Google OAuth disconnected', [
                'user_id' => $this->user->id
            ]);
            
            $this->dispatch('google-oauth-success', ['message' => 'Google account disconnected successfully.']);
            
        } catch (\Exception $e) {
            Log::error('Google OAuth disconnection failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('google-oauth-error', ['message' => 'Failed to disconnect Google account. Please try again.']);
        }
    }

    public function toggleNotifications()
    {
        try {
            $this->user->update([
                'whatsapp_notifications_enabled' => $this->notifyWhatsapp
            ]);
            
            $status = $this->notifyWhatsapp ? 'enabled' : 'disabled';
            session()->flash('success', "WhatsApp notifications {$status} successfully.");
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update notification preferences.');
        }
    }

    public function toggleEmailVerification()
    {
        try {
            $this->user->update([
                'email_verification_enabled' => $this->emailVerificationEnabled
            ]);
            
            $status = $this->emailVerificationEnabled ? 'enabled' : 'disabled';
            session()->flash('success', "Email verification {$status} successfully.");
            
            // If email verification is enabled and email is not verified, send verification email
            if ($this->emailVerificationEnabled && !$this->user->hasVerifiedEmail()) {
                $this->user->sendEmailVerificationNotification();
                session()->flash('info', 'A verification email has been sent to your email address.');
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update email verification preference.');
        }
    }

    public function resendEmailVerification()
    {
        try {
            if (!$this->user->emailVerificationEnabled) {
                session()->flash('error', 'Email verification is not enabled for your account.');
                return;
            }

            if ($this->user->hasVerifiedEmail()) {
                session()->flash('info', 'Your email is already verified.');
                return;
            }

            $this->user->sendEmailVerificationNotification();
            session()->flash('success', 'Verification email sent successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send verification email.');
        }
    }

    public function resetNumberChangeForm()
    {
        $this->newWhatsappNumber = '';
        $this->password = '';
        $this->otp = '';
        $this->otpSent = false;
        $this->otpAttempts = 0;
    }

    public function toggleInterestEdit()
    {
        $this->editingInterests = !$this->editingInterests;
        
        if (!$this->editingInterests) {
            // If canceling edit, reload the current interests
            $this->selectedInterests = $this->user->interests()->pluck('interests.id')->toArray();
        }
    }

    /**
     * Initialize WhatsApp number fields from current user data
     */
    private function initializeWhatsAppFields()
    {
        $whatsappNumber = $this->user->whatsapp_number;
        
        if ($whatsappNumber) {
            // Remove any non-digit characters
            $cleaned = preg_replace('/[^0-9]/', '', $whatsappNumber);
            
            // Check if it starts with 234 (Nigeria)
            if (str_starts_with($cleaned, '234')) {
                $this->whatsappCountryCode = '+234';
                $this->whatsappNumberWithoutCode = substr($cleaned, 3);
            } else {
                // Default to +234 if no country code detected
                $this->whatsappCountryCode = '+234';
                $this->whatsappNumberWithoutCode = $cleaned;
            }
        }
    }

    /**
     * Start WhatsApp number change process
     */
    public function startWhatsAppChange()
    {
        $this->whatsappChangeStep = 'password';
        $this->password = '';
        $this->newWhatsappNumber = '';
        $this->whatsappOtpCode = '';
        $this->otpSent = false;
        $this->isChangingWhatsapp = true;
    }

    /**
     * Cancel WhatsApp number change process
     */
    public function cancelWhatsAppChange()
    {
        $this->whatsappChangeStep = 'password';
        $this->password = '';
        $this->newWhatsappNumber = '';
        $this->whatsappOtpCode = '';
        $this->otpSent = false;
        $this->isChangingWhatsapp = false;
        $this->whatsappCountryCode = '+234';
        $this->whatsappNumberWithoutCode = '';
        
        // Reinitialize from current user data
        if ($this->user->whatsapp_number) {
            $this->initializeWhatsAppFields();
        }
    }

    /**
     * Confirm password and proceed to OTP step
     */
    public function confirmPasswordForWhatsApp()
    {
        try {
            // Validate password
            $this->validate([
                'password' => 'required|string',
                'whatsappCountryCode' => 'required|string',
                'whatsappNumberWithoutCode' => 'required|string|min:10|max:11',
            ], [
                'password.required' => 'Password is required to change WhatsApp number.',
                'whatsappNumberWithoutCode.required' => 'WhatsApp number is required.',
                'whatsappNumberWithoutCode.min' => 'WhatsApp number must be at least 10 digits.',
                'whatsappNumberWithoutCode.max' => 'WhatsApp number must not exceed 11 digits.',
            ]);

            // Check password
            if (!Hash::check($this->password, $this->user->password)) {
                $this->addError('password', 'Invalid password.');
                return;
            }

            // Format the new WhatsApp number
            $countryCode = str_replace('+', '', $this->whatsappCountryCode);
            $this->newWhatsappNumber = $countryCode . $this->whatsappNumberWithoutCode;

            // Check if the new number is different from current
            if ($this->newWhatsappNumber === $this->user->whatsapp_number) {
                $this->addError('whatsappNumberWithoutCode', 'This is already your current WhatsApp number.');
                return;
            }

            // Check if number is already taken by another user
            $existingUser = User::where('whatsapp_number', $this->newWhatsappNumber)
                ->where('id', '!=', $this->user->id)
                ->first();

            if ($existingUser) {
                $this->addError('whatsappNumberWithoutCode', 'This WhatsApp number is already registered to another user.');
                return;
            }

            // Check if user has sufficient credits (100 credits required)
            $creditWallet = $this->user->getWallet('credits');
            if ($creditWallet->balance < 100) {
                $this->addError('whatsappNumberWithoutCode', 'Insufficient credits. You need 100 credits to change your WhatsApp number.');
                return;
            }

            // Proceed to OTP step
            $this->whatsappChangeStep = 'otp';
            $this->password = ''; // Clear password for security
            
            Log::info('WhatsApp number change password confirmed', [
                'user_id' => $this->user->id,
                'old_number' => $this->user->whatsapp_number,
                'new_number' => $this->newWhatsappNumber,
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp number change password confirmation failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
            ]);
            
            $this->addError('password', 'An error occurred. Please try again.');
        }
    }

    /**
     * Send OTP to new WhatsApp number
     */
    public function sendWhatsAppOtp()
    {
        try {
            if (!$this->newWhatsappNumber) {
                $this->addError('whatsappOtpCode', 'Invalid WhatsApp number.');
                return;
            }

            $otpService = app(\App\Services\OtpService::class);
            $templates = $otpService->getMessageTemplates();
            $message = $templates['whatsapp_change'];

            Log::info('Sending WhatsApp change OTP', [
                'user_id' => $this->user->id,
                'new_number' => $this->newWhatsappNumber,
            ]);

            // Send OTP via text message
            $result = $otpService->sendOtp(
                $this->newWhatsappNumber,
                $message,
                $this->user->email,
                false // Not registration
            );

            if ($result['success']) {
                $this->otpSent = true;
                
                Log::info('WhatsApp change OTP sent successfully', [
                    'user_id' => $this->user->id,
                    'new_number' => $this->newWhatsappNumber,
                    'method' => $result['method'],
                ]);
                
                session()->flash('success', 'OTP sent to your new WhatsApp number via ' . $result['method'] . '. Please check your messages.');
            } else {
                Log::error('WhatsApp change OTP sending failed', [
                    'user_id' => $this->user->id,
                    'new_number' => $this->newWhatsappNumber,
                    'error' => $result['message'],
                ]);
                
                $this->addError('whatsappOtpCode', 'Failed to send OTP: ' . $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp change OTP sending exception', [
                'user_id' => $this->user->id,
                'new_number' => $this->newWhatsappNumber,
                'error' => $e->getMessage(),
            ]);
            
            $this->addError('whatsappOtpCode', 'Failed to send OTP. Please try again.');
        }
    }

    /**
     * Verify OTP and complete WhatsApp number change
     */
    public function verifyWhatsAppOtp()
    {
        try {
            // Validate OTP code
            $this->validate([
                'whatsappOtpCode' => 'required|string|size:6',
            ], [
                'whatsappOtpCode.required' => 'OTP code is required.',
                'whatsappOtpCode.size' => 'OTP code must be 6 digits.',
            ]);

            $otpService = app(\App\Services\OtpService::class);
            $walletService = app(\App\Services\WalletService::class);

            // Verify OTP
            $otpResult = $otpService->verifyOtp(
                $this->newWhatsappNumber,
                $this->whatsappOtpCode,
                'whatsapp_change'
            );

            if (!$otpResult['success']) {
                $this->addError('whatsappOtpCode', $otpResult['message']);
                return;
            }

            // Start database transaction
            DB::transaction(function () use ($walletService) {
                // Deduct 100 credits from user's wallet
                $walletService->deductWallet(
                    $this->user,
                    'credits',
                    100,
                    'WhatsApp number change fee',
                    $this->user // Self-initiated transaction
                );

                // Update user's WhatsApp number
                $oldNumber = $this->user->whatsapp_number;
                $this->user->update([
                    'whatsapp_number' => $this->newWhatsappNumber,
                ]);

                Log::info('WhatsApp number changed successfully', [
                    'user_id' => $this->user->id,
                    'old_number' => $oldNumber,
                    'new_number' => $this->newWhatsappNumber,
                    'credits_deducted' => 100,
                ]);
            });

            // Reset form state
            $this->cancelWhatsAppChange();
            
            // Refresh user model
            $this->user = $this->user->fresh();
            $this->initializeWhatsAppFields();

            session()->flash('success', 'WhatsApp number changed successfully! 100 credits have been deducted from your account.');

        } catch (\Exception $e) {
            Log::error('WhatsApp number change verification failed', [
                'user_id' => $this->user->id,
                'new_number' => $this->newWhatsappNumber,
                'error' => $e->getMessage(),
            ]);
            
            $this->addError('whatsappOtpCode', 'Verification failed. Please try again.');
        }
    }

    public function getUserInterestsProperty()
    {
        return $this->user->interests()->get();
    }

    public function getCreditsBalanceProperty()
    {
        return $this->user->getWallet('credits')->balance ?? 0;
    }

    public function getNairaBalanceProperty()
    {
        return $this->user->getWallet(Wallet::TYPE_NAIRA)->balance ?? 0;
    }

    public function getEarningsBalanceProperty()
    {
        return $this->user->getWallet('earnings')->balance ?? 0;
    }

    /**
     * Get the number of batches the user has participated in.
     */
    public function getBatchParticipationCountProperty()
    {
        return $this->user->batchMemberships()->count();
    }

    /**
     * Get the number of AdTasks the user has completed.
     */
    public function getCompletedAdTasksCountProperty()
    {
        return $this->user->adTasks()->where('status', 'approved')->count();
    }

    public function getAvatarUrlProperty()
    {
        $avatarService = app(AvatarService::class);
        return $avatarService->generateAvatarUrl($this->user, [
            'size' => 200,
            'background' => 'EBF4FF',
            'color' => '7F9CF5'
        ]);
    }



    public function render()
    {
        return view('livewire.profile');
    }
}