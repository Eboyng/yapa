<?php

namespace App\Services;

use App\Models\User;
use App\Models\PendingUser;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
class OtpService
{
    private ?string $kudismsApiKey;
    private string $kudismsBaseUrl;
    private string $kudismsSenderId;
    private WhatsAppService $whatsAppService;
    private SettingService $settingService;

    public function __construct(WhatsAppService $whatsAppService, SettingService $settingService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->settingService = $settingService;
        
        // Keep SMS settings from config for fallback
        $this->kudismsApiKey = config('services.kudisms.api_key') ?? '';
        $this->kudismsBaseUrl = config('services.kudisms.base_url', 'https://api.kudisms.net');
        $this->kudismsSenderId = config('services.kudisms.sender_id', 'YAPA');
    }

    /**
     * Send OTP via preferred method (SMS or WhatsApp) with fallback.
     */
    public function sendOtp(
        string $whatsappNumber,
        string $message,
        ?string $email = null,
        bool $isRegistration = false,
        string $context = 'login'
    ): array {
        // Generate OTP using the Otp model
        $otpData = \App\Models\Otp::generate($whatsappNumber, $context, null, 5);
        $otp = $otpData['otp'];
        $otpRecord = $otpData['record'];
        
        $formattedMessage = str_replace('{otp}', $otp, $message);
        
        Log::info('OTP generated and stored in database', [
            'whatsapp_number' => $whatsappNumber,
            'context' => $context,
            'otp_id' => $otpRecord->id,
        ]);

        $result = [
            'success' => false,
            'method' => null,
            'message' => '',
            'otp' => $otp, // Remove in production
        ];

        try {
            // Get admin preference for OTP delivery method
            $otpSettings = $this->settingService->getOtpSettings();
            $preferredMethod = $otpSettings['otp_delivery_method'] ?? 'whatsapp';
            $fallbackEnabled = $otpSettings['otp_sms_fallback_enabled'] ?? true;

            if ($preferredMethod === 'sms') {
                // Try SMS first
                $smsResult = $this->sendSmsMessage($whatsappNumber, $formattedMessage, $otp);
                
                if ($smsResult['success']) {
                    $result['success'] = true;
                    $result['method'] = 'sms';
                    $result['message'] = 'OTP sent via SMS';
                    
                    Log::info('OTP sent via SMS (preferred)', [
                        'whatsapp_number' => $whatsappNumber,
                        'is_registration' => $isRegistration,
                    ]);
                    
                    return $result;
                }

                // Fallback to WhatsApp if enabled
                if ($fallbackEnabled) {
                    Log::warning('SMS OTP failed, falling back to WhatsApp', [
                        'whatsapp_number' => $whatsappNumber,
                        'sms_error' => $smsResult['message'],
                    ]);

                    $whatsappResult = $this->sendWhatsAppTemplate($whatsappNumber, $otp);
                    
                    if ($whatsappResult['success']) {
                        $result['success'] = true;
                        $result['method'] = 'whatsapp';
                        $result['message'] = 'OTP sent via WhatsApp (SMS unavailable)';
                        
                        Log::info('OTP sent via WhatsApp fallback', [
                            'whatsapp_number' => $whatsappNumber,
                            'is_registration' => $isRegistration,
                        ]);
                        
                        return $result;
                    }
                    
                    // Both methods failed
                    $result['message'] = 'Failed to send OTP via SMS and WhatsApp';
                    
                    Log::error('Both SMS and WhatsApp OTP failed', [
                        'whatsapp_number' => $whatsappNumber,
                        'sms_error' => $smsResult['message'],
                        'whatsapp_error' => $whatsappResult['message'],
                    ]);
                } else {
                    $result['message'] = 'Failed to send OTP via SMS';
                    
                    Log::error('SMS OTP failed and fallback disabled', [
                        'whatsapp_number' => $whatsappNumber,
                        'sms_error' => $smsResult['message'],
                    ]);
                }
            } else {
                // Try WhatsApp first (default behavior)
                $whatsappResult = $this->sendWhatsAppTemplate($whatsappNumber, $otp);
                
                if ($whatsappResult['success']) {
                    $result['success'] = true;
                    $result['method'] = 'whatsapp';
                    $result['message'] = 'OTP sent via WhatsApp';
                    
                    Log::info('OTP sent via WhatsApp (preferred)', [
                        'whatsapp_number' => $whatsappNumber,
                        'is_registration' => $isRegistration,
                    ]);
                    
                    return $result;
                }

                // Fallback to SMS if enabled
                if ($fallbackEnabled) {
                    Log::warning('WhatsApp OTP failed, falling back to SMS', [
                        'whatsapp_number' => $whatsappNumber,
                        'whatsapp_error' => $whatsappResult['message'],
                    ]);

                    $smsResult = $this->sendSmsMessage($whatsappNumber, $formattedMessage, $otp);
                    
                    if ($smsResult['success']) {
                        $result['success'] = true;
                        $result['method'] = 'sms';
                        $result['message'] = 'OTP sent via SMS (WhatsApp unavailable)';
                        
                        Log::info('OTP sent via SMS fallback', [
                            'whatsapp_number' => $whatsappNumber,
                            'is_registration' => $isRegistration,
                        ]);
                        
                        return $result;
                    }
                    
                    // Both methods failed
                    $result['message'] = 'Failed to send OTP via WhatsApp and SMS';
                    
                    Log::error('Both WhatsApp and SMS OTP failed', [
                        'whatsapp_number' => $whatsappNumber,
                        'whatsapp_error' => $whatsappResult['message'],
                        'sms_error' => $smsResult['message'],
                    ]);
                } else {
                    $result['message'] = 'Failed to send OTP via WhatsApp';
                    
                    Log::error('WhatsApp OTP failed and fallback disabled', [
                        'whatsapp_number' => $whatsappNumber,
                        'whatsapp_error' => $whatsappResult['message'],
                    ]);
                }
            }

        } catch (\Exception $e) {
            $result['message'] = 'OTP service error: ' . $e->getMessage();
            
            Log::error('OTP service exception', [
                'whatsapp_number' => $whatsappNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $result;
    }

    /**
     * Verify OTP code using Otp model.
     */
    public function verifyOtp(
        string $whatsappNumber,
        string $otpCode,
        string $context = 'login'
    ): array {
        Log::info('Verifying OTP using Otp model', [
            'whatsapp_number' => $whatsappNumber,
            'context' => $context,
            'provided_otp' => $otpCode,
        ]);
        
        // Use the Otp model to verify the OTP
        $result = \App\Models\Otp::verify($whatsappNumber, $otpCode, $context);
        
        if ($result['success']) {
            Log::info('OTP verified successfully using Otp model', [
                'whatsapp_number' => $whatsappNumber,
                'context' => $context,
                'otp_id' => $result['record']->id ?? null,
            ]);
            
            return [
                'success' => true,
                'message' => 'OTP verified successfully',
                'can_retry' => false,
            ];
        } else {
            Log::warning('OTP verification failed using Otp model', [
                'whatsapp_number' => $whatsappNumber,
                'context' => $context,
                'error' => $result['message'],
            ]);
            
            return [
                'success' => false,
                'message' => $result['message'],
                'can_retry' => !str_contains($result['message'], 'Maximum'),
            ];
        }
    }

    /**
     * Check if user can resend OTP (rate limiting) using Otp model.
     */
    public function canResendOtp(string $whatsappNumber, string $context = 'login'): array
    {
        // Use the Otp model to check rate limiting
        $result = \App\Models\Otp::canResend($whatsappNumber, $context);
        
        return [
            'can_resend' => $result['can_resend'],
            'wait_time' => $result['wait_time'] ?? 0,
            'message' => $result['message'],
        ];
    }

    /**
     * Update resend tracking.
     */
    public function trackResend(string $whatsappNumber, string $context = 'login'): void
    {
        // Resend tracking is now handled by the Otp model directly
        // No cache needed as we use database records for rate limiting
        Log::info('Resend tracked for OTP', [
            'whatsapp_number' => $whatsappNumber,
            'context' => $context,
        ]);
    }

    /**
     * Send WhatsApp template message with OTP parameter.
     */
    public function sendWhatsAppTemplate(string $whatsappNumber, string $otp): array
    {
        try {
            // Check if WhatsApp notifications are enabled
            if (!$this->settingService->isFeatureEnabled('whatsapp_notifications')) {
                Log::warning('WhatsApp notifications are disabled in settings');
                return [
                    'success' => false,
                    'message' => 'WhatsApp notifications are disabled',
                ];
            }
            
            // Create notification log for tracking
            $notificationLog = NotificationLog::create([
                'type' => 'otp_whatsapp',
                'channel' => NotificationLog::CHANNEL_WHATSAPP,
                'recipient' => $this->formatPhoneNumber($whatsappNumber),
                'status' => 'pending',
                'message' => "OTP: {$otp}",
            ]);
            
            Log::info('Attempting to send WhatsApp OTP template', [
                'phone' => $whatsappNumber,
                'notification_id' => $notificationLog->id,
            ]);
            
            // Use WhatsAppService to send template with OTP parameter
            $this->whatsAppService->sendTemplate(
                $whatsappNumber,
                '', // Use default template code from settings
                [$otp], // OTP as template parameter
                $notificationLog
            );
            
            return [
                'success' => true,
                'message' => 'WhatsApp template sent successfully',
                'notification_id' => $notificationLog->id,
            ];
            
        } catch (\Exception $e) {
            Log::error('WhatsApp OTP template send failed', [
                'phone' => $whatsappNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return [
                'success' => false,
                'message' => 'WhatsApp request failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send WhatsApp message via Kudisms (legacy method).
     */
    public function sendWhatsAppMessage(string $whatsappNumber, string $message): array
    {
        try {
            // Check if WhatsApp notifications are enabled
            if (!$this->settingService->isFeatureEnabled('whatsapp_notifications')) {
                Log::warning('WhatsApp notifications are disabled in settings');
                return [
                    'success' => false,
                    'message' => 'WhatsApp notifications are disabled',
                ];
            }
            
            // Create notification log for tracking
            $notificationLog = NotificationLog::create([
                'type' => 'otp_whatsapp',
                'channel' => NotificationLog::CHANNEL_WHATSAPP,
                'recipient' => $this->formatPhoneNumber($whatsappNumber),
                'status' => 'pending',
                'message' => $message,
            ]);
            
            Log::info('Attempting to send WhatsApp OTP', [
                'phone' => $whatsappNumber,
                'notification_id' => $notificationLog->id,
            ]);
            
            // Use WhatsAppService to send message
            $this->whatsAppService->send(
                $whatsappNumber,
                $message,
                $notificationLog
            );
            
            return [
                'success' => true,
                'message' => 'WhatsApp message sent successfully',
                'notification_id' => $notificationLog->id,
            ];
            
        } catch (\Exception $e) {
            Log::error('WhatsApp OTP send failed', [
                'phone' => $whatsappNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return [
                'success' => false,
                'message' => 'WhatsApp request failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS message via Kudisms OTP API.
     */
    private function sendSmsMessage(string $phoneNumber, string $message, string $otp): array
    {
        try {
            // Get SMS settings from SettingService
            $smsSettings = $this->settingService->getSmsSettings();
            
            Log::info('SMS settings retrieved', [
                'settings' => array_map(function($value) {
                    return $value ? (strlen($value) > 10 ? substr($value, 0, 10) . '...' : $value) : 'NULL';
                }, $smsSettings)
            ]);
            
            // Validate required settings
            if (empty($smsSettings['kudisms_api_key'])) {
                Log::error('Kudisms API key is missing from settings');
                return [
                    'success' => false,
                    'message' => 'SMS API key not configured',
                ];
            }
            
            if (empty($smsSettings['kudisms_sms_url'])) {
                Log::error('Kudisms SMS URL is missing from settings');
                return [
                    'success' => false,
                    'message' => 'SMS API URL not configured',
                ];
            }
            
            // Create notification log for tracking
            $notificationLog = NotificationLog::create([
                'type' => 'otp_sms',
                'channel' => NotificationLog::CHANNEL_SMS,
                'recipient' => $this->formatPhoneNumberForSms($phoneNumber),
                'status' => 'pending',
                'message' => $message,
            ]);
            
            Log::info('Attempting to send SMS OTP', [
                'phone' => $phoneNumber,
                'notification_id' => $notificationLog->id,
                'api_url' => $smsSettings['kudisms_sms_url'],
            ]);
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->withOptions([
                    'verify' => false, // Disable SSL verification for development
                ])
                ->post($smsSettings['kudisms_sms_url'], [
                    'token' => $smsSettings['kudisms_api_key'],
                    'senderID' => $smsSettings['kudisms_sender_id'],
                    'recipients' => $this->formatPhoneNumberForSms($phoneNumber),
                    'otp' => $otp,
                    'appnamecode' => $smsSettings['kudisms_app_name_code'],
                    'templatecode' => $smsSettings['kudisms_sms_template_code'],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Update notification log
                $notificationLog->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'response_data' => $data,
                ]);
                
                Log::info('SMS OTP sent successfully', [
                    'phone' => $phoneNumber,
                    'notification_id' => $notificationLog->id,
                    'response' => $data,
                ]);
                
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'response' => $data,
                    'notification_id' => $notificationLog->id,
                ];
            }

            // Update notification log with failure
            $notificationLog->update([
                'status' => 'failed',
                'error_message' => $response->body(),
                'failed_at' => now(),
            ]);
            
            Log::error('SMS OTP API error', [
                'phone' => $phoneNumber,
                'status_code' => $response->status(),
                'response' => $response->body(),
                'notification_id' => $notificationLog->id,
            ]);

            return [
                'success' => false,
                'message' => 'SMS API error: ' . $response->body(),
                'status_code' => $response->status(),
            ];

        } catch (\Exception $e) {
            // Update notification log with exception
            if (isset($notificationLog)) {
                $notificationLog->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'failed_at' => now(),
                ]);
            }
            
            Log::error('SMS OTP request failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
                'notification_id' => $notificationLog->id ?? null,
            ]);
            
            return [
                'success' => false,
                'message' => 'SMS request failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate 6-digit OTP.
     */
    private function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }



    /**
     * Format phone number for API.
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Add country code if not present (assuming Nigeria +234)
        if (strlen($cleaned) === 11 && substr($cleaned, 0, 1) === '0') {
            $cleaned = '234' . substr($cleaned, 1);
        } elseif (strlen($cleaned) === 10) {
            $cleaned = '234' . $cleaned;
        }
        
        return $cleaned; // Return without + prefix for WhatsApp service
    }

    /**
     * Format phone number for SMS API (with + prefix).
     */
    private function formatPhoneNumberForSms(string $phoneNumber): string
    {
        return '+' . $this->formatPhoneNumber($phoneNumber);
    }

    /**
     * Get default OTP message templates.
     */
    public static function getMessageTemplates(): array
    {
        return [
            'registration' => 'Welcome to YAPA! Your verification code is {otp}. This code expires in 5 minutes. Do not share this code with anyone.',
            'login' => 'Your YAPA login verification code is {otp}. This code expires in 5 minutes. Do not share this code with anyone.',
            'whatsapp_change' => 'Your YAPA WhatsApp number change verification code is {otp}. This code expires in 5 minutes. Do not share this code with anyone.',
            'withdrawal' => 'Your YAPA withdrawal verification code is {otp}. This code expires in 5 minutes. Do not share this code with anyone.',
        ];
    }
}