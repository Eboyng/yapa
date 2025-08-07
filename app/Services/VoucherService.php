<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class VoucherService
{
    protected $walletService;
    protected $transactionService;

    public function __construct(
        WalletService $walletService,
        TransactionService $transactionService
    ) {
        $this->walletService = $walletService;
        $this->transactionService = $transactionService;
    }

    /**
     * Validate a voucher code and return voucher details
     */
    public function validateVoucher(string $code): array
    {
        try {
            $voucher = Voucher::where('code', $code)->first();

            if (!$voucher) {
                return [
                    'valid' => false,
                    'message' => 'Voucher code not found.',
                    'voucher' => null
                ];
            }

            if (!$voucher->canBeRedeemed()) {
                $message = match ($voucher->status) {
                    Voucher::STATUS_REDEEMED => 'This voucher has already been redeemed.',
                    Voucher::STATUS_EXPIRED => 'This voucher has expired.',
                    Voucher::STATUS_CANCELLED => 'This voucher has been cancelled.',
                    default => 'This voucher is not available for redemption.'
                };

                return [
                    'valid' => false,
                    'message' => $message,
                    'voucher' => $voucher
                ];
            }

            return [
                'valid' => true,
                'message' => 'Voucher is valid and ready for redemption.',
                'voucher' => $voucher
            ];
        } catch (Exception $e) {
            Log::error('Error validating voucher', [
                'code' => $code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'valid' => false,
                'message' => 'An error occurred while validating the voucher.',
                'voucher' => null
            ];
        }
    }

    /**
     * Redeem a voucher for a user
     */
    public function redeemVoucher(string $code, User $user): array
    {
        try {
            return DB::transaction(function () use ($code, $user) {
                $validation = $this->validateVoucher($code);
                
                if (!$validation['valid']) {
                    return $validation;
                }

                $voucher = $validation['voucher'];

                // Redeem the voucher
                $voucher->redeem($user);

                // Add funds to user's wallet based on voucher currency
                if ($voucher->currency === Voucher::CURRENCY_NGN) {
                    $this->walletService->fundWallet($user, 'naira', $voucher->amount, "Voucher redemption: {$voucher->code}");
                    
                    // Log transaction
                    $this->transactionService->credit(
                        $user->id,
                        $voucher->amount,
                        'naira',
                        'naira_funding',
                        "Voucher redemption: {$voucher->code}",
                        $voucher->id,
                        'voucher',
                        [
                            'voucher_id' => $voucher->id,
                            'voucher_code' => $voucher->code,
                            'redemption_method' => 'voucher'
                        ]
                    );
                } else {
                    // Handle credits currency if needed
                    $this->walletService->fundWallet($user, 'credits', $voucher->amount, "Credit voucher redemption: {$voucher->code}");
                    
                    $this->transactionService->credit(
                        $user->id,
                        $voucher->amount,
                        'credits',
                        'credit_purchase',
                        "Credit voucher redemption: {$voucher->code}",
                        $voucher->id,
                        'voucher',
                        [
                            'voucher_id' => $voucher->id,
                            'voucher_code' => $voucher->code,
                            'redemption_method' => 'voucher'
                        ]
                    );
                }

                Log::info('Voucher redeemed successfully', [
                    'voucher_id' => $voucher->id,
                    'voucher_code' => $voucher->code,
                    'user_id' => $user->id,
                    'amount' => $voucher->amount,
                    'currency' => $voucher->currency
                ]);

                return [
                    'success' => true,
                    'message' => "Voucher redeemed successfully! {$voucher->formattedAmount} has been added to your wallet.",
                    'voucher' => $voucher,
                    'amount_added' => $voucher->amount,
                    'currency' => $voucher->currency
                ];
            });
        } catch (Exception $e) {
            Log::error('Error redeeming voucher', [
                'code' => $code,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while redeeming the voucher. Please try again.',
                'voucher' => null
            ];
        }
    }

    /**
     * Generate a batch of vouchers
     */
    public function generateBatch(
        int $count,
        float $amount,
        string $currency = Voucher::CURRENCY_NGN,
        ?\Carbon\Carbon $expiresAt = null,
        ?string $description = null,
        ?string $batchId = null,
        ?int $createdBy = null
    ): array {
        try {
            $batchId = $batchId ?? Str::uuid();
            $vouchers = [];
            $errors = [];

            DB::transaction(function () use (
                $count, $amount, $currency, $expiresAt, $description, $batchId, $createdBy, &$vouchers, &$errors
            ) {
                for ($i = 0; $i < $count; $i++) {
                    try {
                        $voucher = Voucher::create([
                            'code' => $this->generateUniqueCode(),
                            'amount' => $amount,
                            'currency' => $currency,
                            'status' => Voucher::STATUS_ACTIVE,
                            'expires_at' => $expiresAt,
                            'description' => $description,
                            'created_by' => $createdBy,
                            'batch_id' => $batchId,
                            'metadata' => [
                                'batch_id' => $batchId,
                                'batch_size' => $count,
                                'batch_created_at' => now()->toISOString(),
                            ]
                        ]);

                        $vouchers[] = $voucher;
                    } catch (Exception $e) {
                        $errors[] = "Failed to create voucher #" . ($i + 1) . ": " . $e->getMessage();
                        Log::error('Error creating voucher in batch', [
                            'batch_id' => $batchId,
                            'voucher_index' => $i,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            });

            Log::info('Voucher batch generated', [
                'batch_id' => $batchId,
                'requested_count' => $count,
                'created_count' => count($vouchers),
                'amount' => $amount,
                'currency' => $currency,
                'created_by' => $createdBy,
                'errors_count' => count($errors)
            ]);

            return [
                'success' => true,
                'vouchers' => $vouchers,
                'batch_id' => $batchId,
                'errors' => $errors,
                'created_count' => count($vouchers),
                'requested_count' => $count
            ];
        } catch (Exception $e) {
            Log::error('Failed to generate voucher batch', [
                'count' => $count,
                'amount' => $amount,
                'currency' => $currency,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'vouchers' => [],
                'batch_id' => null,
                'errors' => ['Failed to generate voucher batch: ' . $e->getMessage()],
                'created_count' => 0,
                'requested_count' => $count
            ];
        }
    }

    /**
     * Generate a batch of vouchers (alternative method)
     */
    public function generateVoucherBatch(
        int $count,
        float $amount,
        string $currency = Voucher::CURRENCY_NGN,
        User $createdBy = null,
        array $options = []
    ): array {
        try {
            $batchId = Str::uuid();
            $vouchers = [];
            $errors = [];

            DB::transaction(function () use (
                $count, $amount, $currency, $createdBy, $options, $batchId, &$vouchers, &$errors
            ) {
                for ($i = 0; $i < $count; $i++) {
                    try {
                        $voucher = Voucher::create([
                            'code' => $this->generateUniqueCode(),
                            'amount' => $amount,
                            'currency' => $currency,
                            'status' => Voucher::STATUS_ACTIVE,
                            'expires_at' => $options['expires_at'] ?? null,
                            'description' => $options['description'] ?? null,
                            'created_by' => $createdBy ? $createdBy->id : null,
                            'batch_id' => $batchId,
                            'metadata' => array_merge($options['metadata'] ?? [], [
                                'batch_id' => $batchId,
                                'batch_size' => $count,
                                'batch_created_at' => now()->toISOString(),
                            ])
                        ]);

                        $vouchers[] = $voucher;
                    } catch (Exception $e) {
                        $errors[] = "Failed to create voucher #" . ($i + 1) . ": " . $e->getMessage();
                        Log::error('Error creating voucher in batch', [
                            'batch_id' => $batchId,
                            'voucher_index' => $i,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            });

            Log::info('Voucher batch generated', [
                'batch_id' => $batchId,
                'requested_count' => $count,
                'created_count' => count($vouchers),
                'amount' => $amount,
                'currency' => $currency,
                'created_by' => $createdBy ? $createdBy->id : null,
                'errors_count' => count($errors)
            ]);

            return [
                'success' => true,
                'batch_id' => $batchId,
                'vouchers' => $vouchers,
                'created_count' => count($vouchers),
                'requested_count' => $count,
                'errors' => $errors
            ];
        } catch (Exception $e) {
            Log::error('Error generating voucher batch', [
                'count' => $count,
                'amount' => $amount,
                'currency' => $currency,
                'created_by' => $createdBy?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to generate voucher batch.',
                'vouchers' => [],
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Generate a unique voucher code
     */
    protected function generateUniqueCode(): string
    {
        do {
            $code = 'VCH-' . strtoupper(Str::random(8));
        } while (Voucher::where('code', $code)->exists());

        return $code;
    }

    /**
     * Get voucher statistics
     */
    public function getVoucherStats(): array
    {
        try {
            return [
                'total' => Voucher::count(),
                'active' => Voucher::active()->count(),
                'redeemed' => Voucher::redeemed()->count(),
                'expired' => Voucher::expired()->count(),
                'total_value_ngn' => Voucher::where('currency', Voucher::CURRENCY_NGN)
                    ->where('status', Voucher::STATUS_ACTIVE)
                    ->sum('amount'),
                'total_value_credits' => Voucher::where('currency', Voucher::CURRENCY_CREDITS)
                    ->where('status', Voucher::STATUS_ACTIVE)
                    ->sum('amount'),
                'redeemed_value_ngn' => Voucher::where('currency', Voucher::CURRENCY_NGN)
                    ->where('status', Voucher::STATUS_REDEEMED)
                    ->sum('amount'),
                'redeemed_value_credits' => Voucher::where('currency', Voucher::CURRENCY_CREDITS)
                    ->where('status', Voucher::STATUS_REDEEMED)
                    ->sum('amount'),
            ];
        } catch (Exception $e) {
            Log::error('Error getting voucher statistics', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Cancel a voucher
     */
    public function cancelVoucher(Voucher $voucher, User $cancelledBy = null): bool
    {
        try {
            if ($voucher->status === Voucher::STATUS_REDEEMED) {
                throw new Exception('Cannot cancel a redeemed voucher.');
            }

            $voucher->update([
                'status' => Voucher::STATUS_CANCELLED,
                'metadata' => array_merge($voucher->metadata ?? [], [
                    'cancelled_at' => now()->toISOString(),
                    'cancelled_by' => $cancelledBy?->id,
                ])
            ]);

            Log::info('Voucher cancelled', [
                'voucher_id' => $voucher->id,
                'voucher_code' => $voucher->code,
                'cancelled_by' => $cancelledBy?->id
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Error cancelling voucher', [
                'voucher_id' => $voucher->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}