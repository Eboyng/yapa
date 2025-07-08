<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\WebhookLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaystackService
{
    private string $secretKey;
    private string $publicKey;
    private string $baseUrl;
    private TransactionService $transactionService;
    private ReferralService $referralService;

    public function __construct(TransactionService $transactionService, ReferralService $referralService)
    {
        $this->secretKey = config('services.paystack.secret_key');
        $this->publicKey = config('services.paystack.public_key');
        $this->baseUrl = config('services.paystack.base_url', 'https://api.paystack.co');
        $this->transactionService = $transactionService;
        $this->referralService = $referralService;
    }

    /**
     * Initialize a payment transaction.
     */
    public function initializePayment(
        int $userId,
        float $amount,
        string $email,
        ?string $callbackUrl = null,
        ?array $metadata = null
    ): array {
        $user = User::findOrFail($userId);
        
        // Calculate credits based on amount (100 credits = NGN300)
        $credits = $this->calculateCreditsFromAmount($amount);
        
        if ($credits < 100) {
            throw new \InvalidArgumentException('Minimum purchase is 100 credits (NGN300)');
        }

        // Create pending transaction
        $transaction = Transaction::create([
            'user_id' => $userId,
            'amount' => $credits,
            'type' => 'credit',
            'category' => Transaction::CATEGORY_CREDIT_PURCHASE,
            'description' => "Purchase of {$credits} credits",
            'status' => Transaction::STATUS_PENDING,
            'source' => 'paystack',
            'metadata' => array_merge($metadata ?? [], [
                'naira_amount' => $amount,
                'credits' => $credits,
                'email' => $email,
            ]),
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', [
                'email' => $email,
                'amount' => $amount * 100, // Convert to kobo
                'reference' => $transaction->reference,
                'callback_url' => $callbackUrl ?? route('paystack.callback'),
                'metadata' => [
                    'user_id' => $userId,
                    'transaction_id' => $transaction->id,
                    'credits' => $credits,
                    'custom_fields' => [
                        [
                            'display_name' => 'User ID',
                            'variable_name' => 'user_id',
                            'value' => $userId,
                        ],
                        [
                            'display_name' => 'Credits',
                            'variable_name' => 'credits',
                            'value' => $credits,
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'];
                
                // Update transaction with Paystack reference
                $transaction->update([
                    'metadata' => array_merge($transaction->metadata, [
                        'paystack_reference' => $data['reference'],
                        'paystack_access_code' => $data['access_code'],
                        'authorization_url' => $data['authorization_url'],
                    ]),
                ]);

                Log::info('Paystack payment initialized', [
                    'user_id' => $userId,
                    'transaction_id' => $transaction->id,
                    'amount' => $amount,
                    'credits' => $credits,
                    'paystack_reference' => $data['reference'],
                ]);

                return [
                    'success' => true,
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'paystack_reference' => $data['reference'],
                    'authorization_url' => $data['authorization_url'],
                    'access_code' => $data['access_code'],
                    'amount' => $amount,
                    'credits' => $credits,
                ];
            }

            $error = $response->json()['message'] ?? 'Payment initialization failed';
            $transaction->markAsFailed($error);

            return [
                'success' => false,
                'message' => $error,
                'transaction_id' => $transaction->id,
            ];

        } catch (\Exception $e) {
            $transaction->markAsFailed('Payment initialization error: ' . $e->getMessage());
            
            Log::error('Paystack initialization error', [
                'user_id' => $userId,
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment service error: ' . $e->getMessage(),
                'transaction_id' => $transaction->id,
            ];
        }
    }

    /**
     * Verify a payment transaction.
     */
    public function verifyPayment(string $reference): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            if ($response->successful()) {
                $data = $response->json()['data'];
                
                Log::info('Paystack payment verified', [
                    'reference' => $reference,
                    'status' => $data['status'],
                    'amount' => $data['amount'],
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Verification failed',
            ];

        } catch (\Exception $e) {
            Log::error('Paystack verification error', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Verification service error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Handle Paystack webhook.
     */
    public function handleWebhook(array $payload, string $signature): array
    {
        // Verify webhook signature
        if (!$this->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Invalid Paystack webhook signature', [
                'payload' => $payload,
                'signature' => $signature,
            ]);
            
            return [
                'success' => false,
                'message' => 'Invalid webhook signature',
            ];
        }

        // Log webhook
        $webhookLog = WebhookLog::log(
            WebhookLog::SOURCE_PAYSTACK,
            $payload['event'],
            $payload,
            $payload['data']['reference'] ?? null,
            request()->headers->all(),
            $signature
        );
        $webhookLog->markAsVerified();

        try {
            $result = $this->processWebhookEvent($payload);
            $webhookLog->markAsProcessed($result['message'] ?? 'Processed successfully');
            return $result;

        } catch (\Exception $e) {
            $webhookLog->markAsFailed($e->getMessage());
            
            Log::error('Paystack webhook processing error', [
                'event' => $payload['event'],
                'reference' => $payload['data']['reference'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Webhook processing error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process specific webhook events.
     */
    private function processWebhookEvent(array $payload): array
    {
        $event = $payload['event'];
        $data = $payload['data'];

        return match ($event) {
            'charge.success' => $this->handleChargeSuccess($data),
            'charge.failed' => $this->handleChargeFailed($data),
            'transfer.success' => $this->handleTransferSuccess($data),
            'transfer.failed' => $this->handleTransferFailed($data),
            default => [
                'success' => true,
                'message' => "Event {$event} acknowledged but not processed",
            ],
        };
    }

    /**
     * Handle successful charge.
     */
    private function handleChargeSuccess(array $data): array
    {
        $reference = $data['reference'];
        
        // Find transaction by reference (check for idempotency)
        $transaction = Transaction::where('reference', $reference)
            ->orWhere('metadata->paystack_reference', $reference)
            ->first();

        if (!$transaction) {
            return [
                'success' => false,
                'message' => "Transaction not found for reference: {$reference}",
            ];
        }

        if ($transaction->isConfirmed()) {
            return [
                'success' => true,
                'message' => 'Transaction already confirmed (idempotency check)',
            ];
        }

        return DB::transaction(function () use ($transaction, $data) {
            // Credit user's wallet
            $this->transactionService->credit(
                $transaction->user_id,
                $transaction->amount,
                $transaction->type,
                $transaction->category,
                $transaction->description,
                $transaction->related_id,
                'paystack_confirmed',
                array_merge($transaction->metadata, [
                    'paystack_confirmation' => $data,
                    'confirmed_at' => now(),
                ])
            );

            // Process referral reward for credit purchases
            if ($transaction->category === Transaction::CATEGORY_CREDIT_PURCHASE) {
                $user = User::find($transaction->user_id);
                $nairaAmount = $transaction->metadata['naira_amount'] ?? 0;
                
                if ($user && $nairaAmount > 0) {
                    $this->referralService->processDepositReferral($user, $nairaAmount);
                }
            }

            Log::info('Paystack charge success processed', [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'amount' => $transaction->amount,
                'reference' => $data['reference'],
            ]);

            return [
                'success' => true,
                'message' => 'Charge success processed',
                'transaction_id' => $transaction->id,
            ];
        });
    }

    /**
     * Handle failed charge.
     */
    private function handleChargeFailed(array $data): array
    {
        $reference = $data['reference'];
        
        $transaction = Transaction::where('reference', $reference)
            ->orWhere('metadata->paystack_reference', $reference)
            ->first();

        if (!$transaction) {
            return [
                'success' => false,
                'message' => "Transaction not found for reference: {$reference}",
            ];
        }

        $failureReason = $data['gateway_response'] ?? 'Payment failed';
        $this->transactionService->handleFailure(
            $transaction->id,
            "Paystack charge failed: {$failureReason}",
            true // Allow retry
        );

        Log::warning('Paystack charge failed', [
            'transaction_id' => $transaction->id,
            'reference' => $reference,
            'reason' => $failureReason,
        ]);

        return [
            'success' => true,
            'message' => 'Charge failure processed',
            'transaction_id' => $transaction->id,
        ];
    }

    /**
     * Handle successful transfer (withdrawal).
     */
    private function handleTransferSuccess(array $data): array
    {
        // Implementation for withdrawal confirmations
        Log::info('Transfer success received', ['data' => $data]);
        
        return [
            'success' => true,
            'message' => 'Transfer success acknowledged',
        ];
    }

    /**
     * Handle failed transfer (withdrawal).
     */
    private function handleTransferFailed(array $data): array
    {
        // Implementation for withdrawal failures
        Log::warning('Transfer failed received', ['data' => $data]);
        
        return [
            'success' => true,
            'message' => 'Transfer failure acknowledged',
        ];
    }

    /**
     * Verify webhook signature.
     */
    private function verifyWebhookSignature(array $payload, string $signature): bool
    {
        $computedSignature = hash_hmac('sha512', json_encode($payload), $this->secretKey);
        return hash_equals($signature, $computedSignature);
    }

    /**
     * Calculate credits from Naira amount.
     */
    private function calculateCreditsFromAmount(float $amount): int
    {
        // 100 credits = NGN300, so 1 credit = NGN3
        return (int) floor($amount / 3);
    }

    /**
     * Calculate Naira amount from credits.
     */
    public function calculateAmountFromCredits(int $credits): float
    {
        // 1 credit = NGN3
        return $credits * 3;
    }

    /**
     * Get pricing configuration.
     */
    public function getPricingConfig(): array
    {
        return [
            'credit_price' => 3.0, // NGN per credit
            'minimum_credits' => 100,
            'minimum_amount' => 300.0, // NGN
            'currency' => 'NGN',
            'packages' => [
                [
                    'credits' => 100,
                    'amount' => 300,
                    'bonus' => 0,
                    'total_credits' => 100,
                ],
                [
                    'credits' => 500,
                    'amount' => 1500,
                    'bonus' => 50,
                    'total_credits' => 550,
                ],
                [
                    'credits' => 1000,
                    'amount' => 3000,
                    'bonus' => 150,
                    'total_credits' => 1150,
                ],
                [
                    'credits' => 2000,
                    'amount' => 6000,
                    'bonus' => 400,
                    'total_credits' => 2400,
                ],
            ],
        ];
    }

    /**
     * Get public key for frontend.
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Validate BVN with Paystack.
     */
    public function validateBvn(string $bvn): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/bvn/match', [
                'bvn' => $bvn,
                'account_number' => '', // Optional for basic validation
                'bank_code' => '', // Optional for basic validation
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('BVN validation successful', [
                    'bvn' => substr($bvn, 0, 3) . '********', // Log only first 3 digits
                    'status' => $data['status'] ?? 'unknown'
                ]);

                return [
                    'success' => true,
                    'data' => $data['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'BVN validation failed',
            ];

        } catch (\Exception $e) {
            Log::error('BVN validation error', [
                'bvn' => substr($bvn, 0, 3) . '********',
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'BVN validation service error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get list of banks from Paystack.
     */
    public function getBanks(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/bank');

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                
                // Format banks for easier use
                $banks = [];
                foreach ($data as $bank) {
                    $banks[] = [
                        'code' => $bank['code'],
                        'name' => $bank['name'],
                        'slug' => $bank['slug'] ?? null,
                    ];
                }

                return $banks;
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Failed to fetch banks', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Resolve account number to get account name.
     */
    public function resolveAccountNumber(string $accountNumber, string $bankCode): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/bank/resolve', [
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'];
                
                Log::info('Account resolution successful', [
                    'account_number' => $accountNumber,
                    'bank_code' => $bankCode,
                    'account_name' => $data['account_name'] ?? 'Unknown'
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Account resolution failed',
            ];

        } catch (\Exception $e) {
            Log::error('Account resolution error', [
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Account resolution service error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create transfer recipient for withdrawals.
     */
    public function createTransferRecipient(string $accountNumber, string $bankCode, string $name): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transferrecipient', [
                'type' => 'nuban',
                'name' => $name,
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'currency' => 'NGN',
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'];
                
                Log::info('Transfer recipient created', [
                    'recipient_code' => $data['recipient_code'],
                    'account_number' => $accountNumber,
                    'bank_code' => $bankCode
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to create transfer recipient',
            ];

        } catch (\Exception $e) {
            Log::error('Transfer recipient creation error', [
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Transfer recipient service error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Initiate transfer for withdrawals.
     */
    public function initiateTransfer(string $recipientCode, float $amount, string $reason = 'Withdrawal'): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transfer', [
                'source' => 'balance',
                'amount' => $amount * 100, // Convert to kobo
                'recipient' => $recipientCode,
                'reason' => $reason,
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'];
                
                Log::info('Transfer initiated', [
                    'transfer_code' => $data['transfer_code'],
                    'amount' => $amount,
                    'recipient' => $recipientCode
                ]);

                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to initiate transfer',
            ];

        } catch (\Exception $e) {
            Log::error('Transfer initiation error', [
                'recipient' => $recipientCode,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Transfer service error: ' . $e->getMessage(),
            ];
        }
    }
}