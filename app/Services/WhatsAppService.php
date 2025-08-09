<?php

namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $senderId;
    protected string $templateCode;
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
        $this->loadSettings();
    }

    /**
     * Load settings from SettingService.
     */
    protected function loadSettings(): void
    {
        $settings = $this->settingService->getKudismsSettings();
        
        $this->apiUrl = $settings['kudisms_whatsapp_url'] ?: 'https://my.kudisms.net/api/whatsapp';
        $this->apiKey = $settings['kudisms_api_key'] ?: '';
        $this->senderId = $settings['kudisms_sender_id'] ?: 'Yapa';
        $this->templateCode = $settings['kudisms_whatsapp_template_code'] ?: '';
    }

    /**
     * Send WhatsApp message via Kudisms.
     */
    public function send(string $phone, string $message, NotificationLog $notificationLog, ?string $templateCode = null, array $parameters = [], array $buttonParameters = [], array $headerParameters = []): void
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Kudisms API key not configured');
        }

        $phone = $this->formatPhoneNumber($phone);
        $templateCode = $templateCode ?: $this->templateCode;
        
        // If no template code provided and no parameters, use message as single parameter
        if (empty($templateCode) && empty($parameters)) {
            $parameters = [$message];
        }
        
        try {
            $formData = [
                'token' => $this->apiKey,
                'recipient' => $phone,
                'template_code' => $templateCode ?: $this->templateCode, // Always include template_code
            ];
            
            // Validate that template_code is provided
            if (empty($formData['template_code'])) {
                throw new \Exception('WhatsApp template code is required but not configured in settings');
            }
            
            // Add parameters
            if (!empty($parameters)) {
                $formData['parameters'] = is_array($parameters) ? implode(',', $parameters) : $parameters;
            }
            
            // Add button parameters if provided
            if (!empty($buttonParameters)) {
                $formData['button_parameters'] = is_array($buttonParameters) ? implode(',', $buttonParameters) : $buttonParameters;
            }
            
            // Add header parameters if provided
            if (!empty($headerParameters)) {
                $formData['header_parameters'] = is_array($headerParameters) ? implode(',', $headerParameters) : $headerParameters;
            }
            
            $response = Http::timeout(30)
                ->withoutVerifying() // Disable SSL verification for local development
                ->asForm()
                ->post($this->apiUrl, $formData);

            $responseData = $response->json();
            
            if ($response->successful() && isset($responseData['status']) && $responseData['status'] === 'success') {
                $notificationLog->update([
                    'channel' => NotificationLog::CHANNEL_WHATSAPP,
                    'recipient' => $phone,
                ]);
                
                // Extract message ID from data field (format: phone|message_id)
                $messageId = null;
                if (isset($responseData['data']) && is_string($responseData['data'])) {
                    $parts = explode('|', $responseData['data']);
                    $messageId = count($parts) > 1 ? $parts[1] : $responseData['data'];
                }
                
                $notificationLog->markAsSent(
                    $messageId,
                    $responseData
                );
                
                Log::info('WhatsApp message sent successfully', [
                    'notification_id' => $notificationLog->id,
                    'phone' => $phone,
                    'message_id' => $messageId,
                    'cost' => $responseData['cost'] ?? null,
                ]);
            } else {
                $errorMessage = $responseData['msg'] ?? 'Unknown error from Kudisms';
                throw new \Exception($errorMessage);
            }
        } catch (\Exception $e) {
            $notificationLog->update([
                'channel' => NotificationLog::CHANNEL_WHATSAPP,
                'recipient' => $phone,
            ]);
            
            $notificationLog->markAsFailed('WhatsApp send failed: ' . $e->getMessage());
            
            Log::error('WhatsApp message failed', [
                'notification_id' => $notificationLog->id,
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle delivery status webhook from Kudisms.
     */
    public function handleDeliveryStatus(array $data): void
    {
        $messageId = $data['message_id'] ?? null;
        $status = $data['status'] ?? null;
        
        if (!$messageId || !$status) {
            Log::warning('Invalid delivery status data received', $data);
            return;
        }

        $notificationLog = NotificationLog::where('gateway_message_id', $messageId)
            ->where('channel', NotificationLog::CHANNEL_WHATSAPP)
            ->first();

        if (!$notificationLog) {
            Log::warning('Notification log not found for message ID', ['message_id' => $messageId]);
            return;
        }

        switch (strtolower($status)) {
            case 'delivered':
                $notificationLog->markAsDelivered($data);
                Log::info('WhatsApp message delivered', [
                    'notification_id' => $notificationLog->id,
                    'message_id' => $messageId,
                ]);
                break;
                
            case 'failed':
            case 'undelivered':
                $errorMessage = $data['error'] ?? 'Message delivery failed';
                $notificationLog->markAsFailed($errorMessage, $data);
                Log::warning('WhatsApp message delivery failed', [
                    'notification_id' => $notificationLog->id,
                    'message_id' => $messageId,
                    'error' => $errorMessage,
                ]);
                break;
                
            case 'read':
                // Update status to read if we want to track read receipts
                $notificationLog->update([
                    'status' => 'read',
                    'gateway_response' => array_merge(
                        $notificationLog->gateway_response ?? [],
                        $data
                    ),
                ]);
                break;
                
            default:
                Log::info('WhatsApp status update received', [
                    'notification_id' => $notificationLog->id,
                    'message_id' => $messageId,
                    'status' => $status,
                    'data' => $data,
                ]);
        }
    }

    /**
     * Format phone number for WhatsApp.
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If phone starts with 0, replace with country code (assuming Nigeria +234)
        if (str_starts_with($phone, '0')) {
            $phone = '234' . substr($phone, 1);
        }
        
        // If phone doesn't start with country code, add it
        if (!str_starts_with($phone, '234')) {
            $phone = '234' . $phone;
        }
        
        return $phone;
    }

    /**
     * Send WhatsApp message using template.
     */
    public function sendTemplate(string $phone, string $templateCode, array $parameters = [], NotificationLog $notificationLog = null, array $buttonParameters = [], array $headerParameters = []): void
    {
        if (!$notificationLog) {
            $notificationLog = NotificationLog::create([
                'type' => 'whatsapp_template',
                'channel' => NotificationLog::CHANNEL_WHATSAPP,
                'recipient' => $phone,
                'status' => 'pending',
            ]);
        }
        
        $this->send($phone, '', $notificationLog, $templateCode, $parameters, $buttonParameters, $headerParameters);
    }

    /**
     * Refresh settings from database.
     */
    public function refreshSettings(): void
    {
        $this->loadSettings();
    }

    /**
     * Validate phone number format.
     */
    public function isValidPhoneNumber(string $phone): bool
    {
        $formatted = $this->formatPhoneNumber($phone);
        
        // Nigerian phone numbers should be 13 digits (234 + 10 digits)
        return strlen($formatted) === 13 && str_starts_with($formatted, '234');
    }

    /**
     * Send bulk WhatsApp messages.
     */
    public function sendBulk(array $messages): array
    {
        $results = [];
        
        foreach ($messages as $message) {
            try {
                $this->send(
                    $message['phone'],
                    $message['message'],
                    $message['notification_log']
                );
                $results[] = ['success' => true, 'phone' => $message['phone']];
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'phone' => $message['phone'],
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
}