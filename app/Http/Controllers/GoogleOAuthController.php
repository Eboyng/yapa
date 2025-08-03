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
            $errorDescription = $request->get('error_description');
            
            Log::info('Google OAuth callback received', [
                'has_code' => !empty($code),
                'has_state' => !empty($state),
                'has_error' => !empty($error),
                'error' => $error,
                'error_description' => $errorDescription,
                'user_id' => Auth::id()
            ]);
            
            // Handle OAuth errors
            if ($error) {
                Log::warning('Google OAuth callback error received', [
                    'error' => $error,
                    'error_description' => $errorDescription,
                    'user_id' => Auth::id()
                ]);
                
                $errorMessage = 'Google authentication failed';
                if ($error === 'access_denied') {
                    $errorMessage = 'Google authentication was cancelled by user';
                } elseif ($errorDescription) {
                    $errorMessage = 'Google authentication failed: ' . $errorDescription;
                }
                
                return redirect()->route('profile')
                    ->with('error', $errorMessage);
            }
            
            if (!$code || !$state) {
                Log::error('Google OAuth callback missing required parameters', [
                    'has_code' => !empty($code),
                    'has_state' => !empty($state),
                    'user_id' => Auth::id()
                ]);
                return redirect()->route('profile')
                    ->with('error', 'Invalid Google OAuth response. Missing required parameters.');
            }
            
            // Decode state to get user info
            $stateData = json_decode(base64_decode($state), true);
            if (!$stateData || !isset($stateData['user_id'])) {
                Log::error('Google OAuth callback invalid state', [
                    'state_data' => $stateData,
                    'user_id' => Auth::id()
                ]);
                return redirect()->route('profile')
                    ->with('error', 'Invalid OAuth state. Please try again.');
            }
            
            $user = User::find($stateData['user_id']);
            if (!$user) {
                Log::error('Google OAuth callback user not found', [
                    'requested_user_id' => $stateData['user_id'],
                    'current_user_id' => Auth::id()
                ]);
                return redirect()->route('profile')
                    ->with('error', 'User not found. Please try again.');
            }
            
            if ($user->id !== Auth::id()) {
                Log::error('Google OAuth callback user mismatch', [
                    'requested_user_id' => $stateData['user_id'],
                    'current_user_id' => Auth::id()
                ]);
                return redirect()->route('profile')
                    ->with('error', 'Unauthorized OAuth request. Please try again.');
            }
            
            // Check if Google OAuth is enabled
            if (!$this->settingService->isGoogleOAuthEnabled()) {
                Log::warning('Google OAuth callback but OAuth is disabled', [
                    'user_id' => $user->id
                ]);
                return redirect()->route('profile')
                    ->with('error', 'Google OAuth is currently disabled.');
            }
            
            $googleSettings = $this->settingService->getGoogleOAuthSettings();
            
            Log::info('Google OAuth settings for token exchange', [
                'user_id' => $user->id,
                'has_client_id' => !empty($googleSettings['google_client_id']),
                'has_client_secret' => !empty($googleSettings['google_client_secret']),
                'redirect_uri' => $googleSettings['google_redirect_uri'] ?? 'not_set'
            ]);
            
            // Validate required settings
            if (empty($googleSettings['google_client_id']) || empty($googleSettings['google_client_secret'])) {
                Log::error('Google OAuth callback missing configuration', [
                    'user_id' => $user->id,
                    'has_client_id' => !empty($googleSettings['google_client_id']),
                    'has_client_secret' => !empty($googleSettings['google_client_secret'])
                ]);
                return redirect()->route('profile')
                    ->with('error', 'Google OAuth is not properly configured. Please contact administrator.');
            }
            
            // Exchange code for access token
            $tokenRequestData = [
                'client_id' => $googleSettings['google_client_id'],
                'client_secret' => $googleSettings['google_client_secret'],
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $googleSettings['google_redirect_uri'] ?: route('google.callback'),
            ];
            
            Log::info('Attempting Google token exchange', [
                'user_id' => $user->id,
                'redirect_uri' => $tokenRequestData['redirect_uri']
            ]);
            
            $tokenResponse = Http::timeout(30)->post('https://oauth2.googleapis.com/token', $tokenRequestData);
            
            if (!$tokenResponse->successful()) {
                Log::error('Google OAuth token exchange failed', [
                    'user_id' => $user->id,
                    'status_code' => $tokenResponse->status(),
                    'response_body' => $tokenResponse->body(),
                    'request_data' => array_merge($tokenRequestData, ['client_secret' => '[HIDDEN]'])
                ]);
                return redirect()->route('profile')
                    ->with('error', 'Failed to authenticate with Google. Please try again.');
            }
            
            $tokenData = $tokenResponse->json();
            
            if (!isset($tokenData['access_token'])) {
                Log::error('Google OAuth token response missing access_token', [
                    'user_id' => $user->id,
                    'token_data' => $tokenData
                ]);
                return redirect()->route('profile')
                    ->with('error', 'Invalid token response from Google. Please try again.');
            }
            
            // Store tokens in user record
            $user->update([
                'google_access_token' => $tokenData['access_token'],
                'google_refresh_token' => $tokenData['refresh_token'] ?? null,
            ]);
            
            Log::info('Google OAuth connection successful', [
                'user_id' => $user->id,
                'has_refresh_token' => !empty($tokenData['refresh_token'])
            ]);
            
            return redirect()->route('profile')
                ->with('success', 'Google account connected successfully!');
                
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('profile')
                ->with('error', 'An error occurred while connecting to Google: ' . $e->getMessage());
        }
    }
}