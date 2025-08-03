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
    public $newWhatsappNumber;
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
        $this->user = Auth::user()->load(['wallets']);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->location = $this->user->location;
        
        // Load interests from JSON field
        $userInterests = $this->user->interests;
        if (is_string($userInterests)) {
            $this->selectedInterests = json_decode($userInterests, true) ?? [];
        } elseif (is_array($userInterests)) {
            $this->selectedInterests = $userInterests;
        } else {
            $this->selectedInterests = [];
        }
        
        $this->notifyWhatsapp = $this->user->whatsapp_notifications_enabled;
        $this->emailVerificationEnabled = $this->user->email_verification_enabled;
        $this->googleConnected = !empty($this->user->google_access_token);
    }

    public function updateProfile()
    {
        $this->isUpdatingProfile = true;
        
        try {
            // Handle interests from Alpine.js (comes as JSON string)
            $interests = request('interests');
            if (is_string($interests)) {
                $interests = json_decode($interests, true) ?? [];
            } elseif (!is_array($interests)) {
                $interests = [];
            }
            
            $this->validate([
                'name' => 'required|string|max:255',
                'location' => 'nullable|string|max:255',
            ]);
            
            // Validate interests separately since they come from request
            if (count($interests) > 5) {
                throw new \Exception('You can select maximum 5 interests.');
            }

            DB::transaction(function () use ($interests) {
                // Update user basic info
                $this->user->update([
                    'name' => $this->name,
                    'location' => $this->location,
                ]);

                // For now, we'll store interests as a simple array in user preferences
                // Since we don't have an interests table, we'll store them as JSON
                $this->user->update([
                    'interests' => json_encode($interests)
                ]);
                
                // Update the component property
                $this->selectedInterests = $interests;
                
                // Refresh user data
                $this->user->refresh();
            });

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

            // Send OTP to new number
            $otpService = app(OtpService::class);
            $message = 'Your YAPA WhatsApp number change verification code is: {otp}. This code expires in 5 minutes.';
            
            $result = $otpService->sendOtp(
                $this->newWhatsappNumber,
                $message,
                $this->user->email,
                false
            );

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

            // Verify OTP
            $otpService = app(OtpService::class);
            $isValid = $otpService->verifyOtp($this->newWhatsappNumber, $this->otp, false);

            if (!$isValid) {
                $this->otpAttempts++;
                throw new \Exception('Invalid OTP. Please try again.');
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
        try {
            $this->isConnectingGoogle = true;
            
            $settingService = app(\App\Services\SettingService::class);
            
            // Check if Google OAuth is enabled
            if (!$settingService->isGoogleOAuthEnabled()) {
                $this->dispatch('google-oauth-error', ['message' => 'Google OAuth is currently disabled.']);
                $this->isConnectingGoogle = false;
                return;
            }
            
            $googleSettings = $settingService->getGoogleOAuthSettings();
            
            // Validate required settings
            if (empty($googleSettings['client_id'])) {
                $this->dispatch('google-oauth-error', ['message' => 'Google OAuth is not properly configured. Please contact administrator.']);
                $this->isConnectingGoogle = false;
                return;
            }
            
            // Generate Google OAuth URL
            $clientId = $googleSettings['client_id'];
            $redirectUri = $googleSettings['redirect_uri'] ?: route('google.callback');
            $scopes = $googleSettings['scopes'] ?: ['openid', 'profile', 'email'];
            $scope = is_array($scopes) ? implode(' ', $scopes) : $scopes;
            $state = base64_encode(json_encode(['user_id' => $this->user->id, 'action' => 'connect']));
            
            $googleAuthUrl = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'scope' => $scope,
                'response_type' => 'code',
                'access_type' => 'offline',
                'state' => $state,
            ]);
            
            // Log the OAuth attempt
            \Log::info('Google OAuth connection initiated', [
                'user_id' => $this->user->id,
                'redirect_uri' => $redirectUri
            ]);
            
            // Dispatch browser event to redirect to Google
            $this->dispatch('google-oauth-redirect', ['url' => $googleAuthUrl]);
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth connection failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('google-oauth-error', ['message' => 'Failed to connect to Google. Please try again later.']);
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
            \Log::info('Google OAuth disconnected', [
                'user_id' => $this->user->id
            ]);
            
            $this->dispatch('google-oauth-success', ['message' => 'Google account disconnected successfully.']);
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth disconnection failed', [
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