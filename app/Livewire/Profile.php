<?php

namespace App\Livewire;

use App\Models\Interest;
use App\Models\User;
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

    // User data
    public $user;
    public $name;
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
        $this->user = Auth::user()->load(['interests', 'wallets']);
        $this->name = $this->user->name;
        $this->location = $this->user->location;
        
        // Ensure selectedInterests is always an array
        $userInterests = $this->user->interests;
        $this->selectedInterests = $userInterests ? $userInterests->pluck('id')->toArray() : [];
        
        $this->notifyWhatsapp = $this->user->whatsapp_notifications_enabled;
        $this->emailVerificationEnabled = $this->user->email_verification_enabled;
        $this->googleConnected = !empty($this->user->google_access_token);
        

        
        // Cache interests for performance
        $this->availableInterests = Cache::remember('interests', 3600, function () {
            return Interest::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'icon', 'color']);
        });
        
        // Ensure availableInterests is a collection
        if (!$this->availableInterests) {
            $this->availableInterests = collect([]);
        }
    }

    public function updateProfile()
    {
        $this->isUpdatingProfile = true;
        
        try {
            $this->validate([
                'name' => 'required|string|max:255',
                'location' => 'nullable|string|max:255',
                'selectedInterests' => 'array|max:5',
                'selectedInterests.*' => 'exists:interests,id',
            ]);

            DB::transaction(function () {
                // Update user basic info
                $this->user->update([
                    'name' => $this->name,
                    'location' => $this->location,
                ]);

                // Sync interests
                $this->user->interests()->sync($this->selectedInterests);
                
                // Refresh user data
                $this->user->refresh();
                $this->user->load(['interests', 'wallets']);
            });

            session()->flash('success', 'Profile updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Failed to update profile. Please try again.');
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
        $this->isConnectingGoogle = true;
        
        // Generate Google OAuth URL
        $clientId = config('services.google.client_id');
        $redirectUri = route('google.callback');
        $scope = 'https://www.googleapis.com/auth/contacts.readonly';
        $state = base64_encode(json_encode(['user_id' => $this->user->id, 'action' => 'connect']));
        
        $googleAuthUrl = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'response_type' => 'code',
            'access_type' => 'offline',
            'state' => $state,
        ]);
        
        return redirect($googleAuthUrl);
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
            session()->flash('success', 'Google account disconnected successfully.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to disconnect Google account.');
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
        return $this->user->getWallet('naira')->balance ?? 0;
    }

    public function getEarningsBalanceProperty()
    {
        return $this->user->getWallet('earnings')->balance ?? 0;
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