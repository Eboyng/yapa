<?php

namespace App\Services;

use App\Models\User;
use App\Models\PendingUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OtpService
{
    private string $kudismsApiKey;
    private string $kudismsBaseUrl;
    private string $kudismsSenderId;

    public function __construct()
    {
        $this->kudismsApiKey = config('services.kudisms.api_key');
        $this->kudismsBaseUrl = config('services.kudisms.base_url', 'https://api.kudisms.net');
        $this->kudismsSenderId = config('services.kudisms.sender_id', 'YAPA');
    }

    /**
     * Send OTP via WhatsApp with SMS fallback.
     */
    public function sendOtp(
        string $whatsappNumber,
        string $message,
        ?string $email = null,
        bool $isRegistration = false
    ): array {
        $otp = $this->generateOtp();
        $formattedMessage = str_replace('{otp}', $otp, $message);
        
        // Store OTP in cache for verification
        $cacheKey = $this->getOtpCacheKey($whatsappNumber, $isRegistration ? 'registration' : 'login');
        Cache::put($cacheKey, [
            'otp' => Hash::make($otp),
            'attempts' => 0,
            'created_at' => now(),
        ], 300); // 5 minutes

        $result = [
            'success' => false,
            'method' => null,
            'message' => '',
            'otp' => $otp, // Remove in production
        ];

        try {
            // Try WhatsApp first
            $whatsappResult = $this->sendWhatsAppMessage($whatsappNumber, $formattedMessage);
            
            if ($whatsappResult['success']) {
                $result['success'] = true;
                $result['method'] = 'whatsapp';
                $result['message'] = 'OTP sent via WhatsApp';
                
                Log::info('OTP sent via WhatsApp', [
                    'whatsapp_number' => $whatsappNumber,
                    'is_registration' => $isRegistration,
                ]);
                
                return $result;
            }

            // Fallback to SMS
            Log::warning('WhatsApp OTP failed, falling back to SMS', [
                'whatsapp_number' => $whatsappNumber,
                'whatsapp_error' => $whatsappResult['message'],
            ]);

            $smsResult = $this->sendSmsMessage($whatsappNumber, $formattedMessage);
            
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
     * Verify OTP code.
     */
    public function verifyOtp(
        string $whatsappNumber,
        string $otpCode,
        string $context = 'login'
    ): array {
        $cacheKey = $this->getOtpCacheKey($whatsappNumber, $context);
        $otpData = Cache::get($cacheKey);

        if (!$otpData) {
            return [
                'success' => false,
                'message' => 'OTP has expired or not found',
                'can_retry' => true,
            ];
        }

        // Check attempts
        if ($otpData['attempts'] >= 3) {
            Cache::forget($cacheKey);
            return [
                'success' => false,
                'message' => 'Maximum OTP attempts exceeded',
                'can_retry' => false,
            ];
        }

        // Increment attempts
        $otpData['attempts']++;
        Cache::put($cacheKey, $otpData, 300);

        // Verify OTP
        if (Hash::check($otpCode, $otpData['otp'])) {
            Cache::forget($cacheKey);
            
            Log::info('OTP verified successfully', [
                'whatsapp_number' => $whatsappNumber,
                'context' => $context,
                'attempts' => $otpData['attempts'],
            ]);

            return [
                'success' => true,
                'message' => 'OTP verified successfully',
                'can_retry' => false,
            ];
        }

        Log::warning('Invalid OTP attempt', [
            'whatsapp_number' => $whatsappNumber,
            'context' => $context,
            'attempts' => $otpData['attempts'],
        ]);

        return [
            'success' => false,
            'message' => 'Invalid OTP code',
            'can_retry' => $otpData['attempts'] < 3,
            'attempts_remaining' => 3 - $otpData['attempts'],
        ];
    }

    /**
     * Check if user can resend OTP (rate limiting).
     */
    public function canResendOtp(string $whatsappNumber, string $context = 'login'): array
    {
        $rateLimitKey = "otp_resend:{$whatsappNumber}:{$context}";
        $resendData = Cache::get($rateLimitKey, ['count' => 0, 'last_sent' => null]);

        // Reset count if more than 1 hour has passed
        if ($resendData['last_sent'] && Carbon::parse($resendData['last_sent'])->diffInHours(now()) >= 1) {
            $resendData = ['count' => 0, 'last_sent' => null];
        }

        // Check if max resends reached (3 per hour)
        if ($resendData['count'] >= 3) {
            $nextAllowedTime = Carbon::parse($resendData['last_sent'])->addHour();
            return [
                'can_resend' => false,
                'message' => 'Maximum resend attempts reached. Try again after ' . $nextAllowedTime->format('H:i'),
                'next_allowed_at' => $nextAllowedTime,
            ];
        }

        // Check if minimum interval has passed (1 minute)
        if ($resendData['last_sent'] && Carbon::parse($resendData['last_sent'])->diffInMinutes(now()) < 1) {
            $nextAllowedTime = Carbon::parse($resendData['last_sent'])->addMinute();
            return [
                'can_resend' => false,
                'message' => 'Please wait before requesting another OTP',
                'next_allowed_at' => $nextAllowedTime,
            ];
        }

        return [
            'can_resend' => true,
            'message' => 'OTP can be resent',
            'resends_remaining' => 3 - $resendData['count'],
        ];
    }

    /**
     * Update resend tracking.
     */
    public function trackResend(string $whatsappNumber, string $context = 'login'): void
    {
        $rateLimitKey = "otp_resend:{$whatsappNumber}:{$context}";
        $resendData = Cache::get($rateLimitKey, ['count' => 0, 'last_sent' => null]);
        
        $resendData['count']++;
        $resendData['last_sent'] = now();
        
        Cache::put($rateLimitKey, $resendData, 3600); // 1 hour
    }

    /**
     * Send WhatsApp message via Kudisms.
     */
    public function sendWhatsAppMessage(string $whatsappNumber, string $message): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->kudismsApiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->kudismsBaseUrl . '/whatsapp/send', [
                    'to' => $this->formatPhoneNumber($whatsappNumber),
                    'message' => $message,
                    'sender_id' => $this->kudismsSenderId,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => 'WhatsApp message sent successfully',
                    'response' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => 'WhatsApp API error: ' . $response->body(),
                'status_code' => $response->status(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'WhatsApp request failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS message via Kudisms.
     */
    private function sendSmsMessage(string $phoneNumber, string $message): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->kudismsApiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->kudismsBaseUrl . '/sms/send', [
                    'to' => $this->formatPhoneNumber($phoneNumber),
                    'message' => $message,
                    'sender_id' => $this->kudismsSenderId,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'response' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => 'SMS API error: ' . $response->body(),
                'status_code' => $response->status(),
            ];

        } catch (\Exception $e) {
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
     * Get OTP cache key.
     */
    private function getOtpCacheKey(string $whatsappNumber, string $context): string
    {
        return "otp:{$context}:" . md5($whatsappNumber);
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
        
        return '+' . $cleaned;
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