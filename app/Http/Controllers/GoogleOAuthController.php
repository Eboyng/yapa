<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GoogleOAuthController extends Controller
{
    protected $settingService;
    
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }
    
    /**
     * Handle Google OAuth callback.
     */
    public function callback(Request $request)
    {
        try {
            $code = $request->get('code');
            $state = $request->get('state');
            $error = $request->get('error');
            
            // Handle OAuth errors
            if ($error) {
                return redirect()->route('profile')
                    ->with('error', 'Google authentication was cancelled or failed.');
            }
            
            if (!$code || !$state) {
                return redirect()->route('profile')
                    ->with('error', 'Invalid Google OAuth response.');
            }
            
            // Decode state to get user info
            $stateData = json_decode(base64_decode($state), true);
            if (!$stateData || !isset($stateData['user_id'])) {
                return redirect()->route('profile')
                    ->with('error', 'Invalid OAuth state.');
            }
            
            $user = User::find($stateData['user_id']);
            if (!$user || $user->id !== Auth::id()) {
                return redirect()->route('profile')
                    ->with('error', 'Unauthorized OAuth request.');
            }
            
            // Check if Google OAuth is enabled
            if (!$this->settingService->isGoogleOAuthEnabled()) {
                return redirect()->route('profile')
                    ->with('error', 'Google OAuth is currently disabled.');
            }
            
            $googleSettings = $this->settingService->getGoogleOAuthSettings();
            
            // Validate required settings
            if (empty($googleSettings['client_id']) || empty($googleSettings['client_secret'])) {
                return redirect()->route('profile')
                    ->with('error', 'Google OAuth is not properly configured.');
            }
            
            // Exchange code for access token
            $tokenResponse = Http::post('https://oauth2.googleapis.com/token', [
                'client_id' => $googleSettings['client_id'],
                'client_secret' => $googleSettings['client_secret'],
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $googleSettings['redirect_uri'] ?: route('google.callback'),
            ]);
            
            if (!$tokenResponse->successful()) {
                Log::error('Google OAuth token exchange failed', [
                    'response' => $tokenResponse->body(),
                    'user_id' => $user->id
                ]);
                return redirect()->route('profile')
                    ->with('error', 'Failed to authenticate with Google.');
            }
            
            $tokenData = $tokenResponse->json();
            
            // Store tokens in user record
            $user->update([
                'google_access_token' => $tokenData['access_token'],
                'google_refresh_token' => $tokenData['refresh_token'] ?? null,
            ]);
            
            return redirect()->route('profile')
                ->with('success', 'Google account connected successfully!');
                
        } catch (\Exception $e) {
            Log::error('Google OAuth callback error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('profile')
                ->with('error', 'An error occurred while connecting to Google.');
        }
    }
}