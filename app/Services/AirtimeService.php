<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AirtimeService
{
    private string $apiUrl;
    private string $apiToken;
    private bool $enabled;
    private array $networks;
    private SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
        $this->loadConfiguration();
    }

    private function loadConfiguration(): void
    {
        $this->apiUrl = $this->settingService->get('airtime_api_url', 'https://wazobianet.com/api');
        $this->apiToken = $this->settingService->get('airtime_api_token', '');
        $this->enabled = $this->settingService->get('airtime_api_enabled', true);
        $this->networks = $this->settingService->get('airtime_networks', $this->getDefaultNetworks());
    }

    private function getDefaultNetworks(): array
    {
        return [
            [
                'name' => 'MTN',
                'network_id' => 1,
                'prefix' => '0803,0806,0813,0816,0903,0906,0913,0916',
                'enabled' => true
            ],
            [
                'name' => 'GLO',
                'network_id' => 2,
                'prefix' => '0805,0807,0815,0811,0905,0915',
                'enabled' => true
            ],
            [
                'name' => '9MOBILE',
                'network_id' => 3,
                'prefix' => '0809,0817,0818,0908,0909',
                'enabled' => true
            ],
            [
                'name' => 'AIRTEL',
                'network_id' => 4,
                'prefix' => '0802,0808,0812,0701,0902,0907,0901',
                'enabled' => true
            ]
        ];
    }

    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiToken);
    }

    public function detectNetwork(string $phoneNumber): ?array
    {
        // Remove any non-numeric characters and ensure it starts with 0
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        if (strlen($cleanNumber) === 11 && substr($cleanNumber, 0, 1) === '0') {
            $prefix = substr($cleanNumber, 0, 4);
            
            foreach ($this->networks as $network) {
                if (!$network['enabled']) {
                    continue;
                }
                
                $prefixes = explode(',', $network['prefix']);
                if (in_array($prefix, $prefixes)) {
                    return $network;
                }
            }
        }
        
        return null;
    }

    public function getEnabledNetworks(): array
    {
        return array_filter($this->networks, fn($network) => $network['enabled']);
    }

    public function validateAmount(float $amount): array
    {
        $minAmount = (float) $this->settingService->get('airtime_minimum_amount', 100);
        $maxAmount = (float) $this->settingService->get('airtime_maximum_amount', 10000);

        if ($amount < $minAmount) {
            return [
                'valid' => false,
                'message' => "Minimum airtime amount is ₦{$minAmount}"
            ];
        }

        if ($amount > $maxAmount) {
            return [
                'valid' => false,
                'message' => "Maximum airtime amount is ₦{$maxAmount}"
            ];
        }

        return ['valid' => true];
    }

    public function purchaseAirtime(string $phoneNumber, float $amount, int $networkId, string $transactionId): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'message' => 'Airtime service is currently disabled'
            ];
        }

        // Validate amount
        $validation = $this->validateAmount($amount);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Token ' . $this->apiToken
            ])->withoutVerifying() // Disable SSL verification for local development
            ->timeout(30)->post($this->apiUrl . '/airtime/', [
                'network_id' => $networkId,
                'amount' => $amount,
                'airtime_type' => 'VTU',
                'phone_number' => $phoneNumber,
                'ported' => false
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Airtime purchase successful', [
                    'transaction_id' => $transactionId,
                    'phone_number' => $phoneNumber,
                    'amount' => $amount,
                    'network_id' => $networkId,
                    'api_response' => $data
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                    'message' => 'Airtime purchased successfully'
                ];
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'Unknown API error';
                
                Log::error('Airtime purchase failed - API error', [
                    'transaction_id' => $transactionId,
                    'phone_number' => $phoneNumber,
                    'amount' => $amount,
                    'network_id' => $networkId,
                    'status_code' => $response->status(),
                    'error_response' => $errorData
                ]);

                return [
                    'success' => false,
                    'message' => 'Airtime purchase failed: ' . $errorMessage
                ];
            }
        } catch (\Exception $e) {
            Log::error('Airtime purchase failed - Exception', [
                'transaction_id' => $transactionId,
                'phone_number' => $phoneNumber,
                'amount' => $amount,
                'network_id' => $networkId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Airtime purchase failed: ' . $e->getMessage()
            ];
        }
    }

    public function getNetworkByPhoneNumber(string $phoneNumber): ?array
    {
        return $this->detectNetwork($phoneNumber);
    }

    public function getNetworkById(int $networkId): ?array
    {
        foreach ($this->networks as $network) {
            if ($network['network_id'] === $networkId && $network['enabled']) {
                return $network;
            }
        }
        
        return null;
    }

    public function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-numeric characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Ensure it's 11 digits starting with 0
        if (strlen($cleanNumber) === 10) {
            $cleanNumber = '0' . $cleanNumber;
        }
        
        return $cleanNumber;
    }

    public function getTransactionFee(float $amount): float
    {
        // 2% fee for airtime transactions
        return ($amount * 2.0) / 100;
    }

    public function getMinimumAmount(): float
    {
        return (float) $this->settingService->get('airtime_minimum_amount', 100);
    }

    public function getMaximumAmount(): float
    {
        return (float) $this->settingService->get('airtime_maximum_amount', 10000);
    }
}
