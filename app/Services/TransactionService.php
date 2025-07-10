<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\AuditLog;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\OptimisticLockException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class TransactionService
{
    /**
     * Map wallet type to transaction type.
     */
    private function mapWalletTypeToTransactionType(string $walletType, string $operation): string
    {
        $mapping = [
            Wallet::TYPE_CREDITS => $operation === 'credit' ? Transaction::TYPE_CREDIT : Transaction::TYPE_DEBIT,
            Wallet::TYPE_NAIRA => Transaction::TYPE_NAIRA,
            Wallet::TYPE_EARNINGS => Transaction::TYPE_EARNINGS,
        ];

        return $mapping[$walletType] ?? $walletType;
    }

    /**
     * Credit a user's wallet balance.
     */
    public function credit(
        int $userId,
        float $amount,
        string $walletType,
        string $category,
        string $description,
        ?int $relatedId = null,
        ?string $source = null,
        ?array $metadata = null
    ): Transaction {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit amount must be positive');
        }

        return DB::transaction(function () use (
            $userId, $amount, $walletType, $category, $description, $relatedId, $source, $metadata
        ) {
            $user = User::findOrFail($userId);
            $wallet = $user->getWallet($walletType);
            $transactionType = $this->mapWalletTypeToTransactionType($walletType, 'credit');

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $userId,
                'amount' => $amount,
                'type' => $transactionType,
                'category' => $category,
                'description' => $description,
                'status' => Transaction::STATUS_PENDING,
                'related_id' => $relatedId,
                'source' => $source,
                'metadata' => $metadata,
            ]);

            try {
                // Credit the wallet with optimistic locking
                $wallet->credit($amount);
                
                // Mark transaction as confirmed
                $transaction->markAsConfirmed();

                // Log the transaction
                Log::info('Wallet credited', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'wallet_type' => $walletType,
                    'transaction_type' => $transactionType,
                    'category' => $category,
                    'transaction_id' => $transaction->id,
                ]);

                // Send notification if significant amount
                if ($this->shouldNotifyUser($amount, $type)) {
                    $this->notifyUser($user, 'credit', $transaction);
                }

                return $transaction;

            } catch (OptimisticLockException $e) {
                $transaction->markAsFailed('Optimistic lock conflict: ' . $e->getMessage());
                throw $e;
            } catch (\Exception $e) {
                $transaction->markAsFailed('Credit failed: ' . $e->getMessage());
                Log::error('Credit operation failed', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'wallet_type' => $walletType,
                    'error' => $e->getMessage(),
                    'transaction_id' => $transaction->id,
                ]);
                throw $e;
            }
        });
    }

    /**
     * Debit a user's wallet balance.
     */
    public function debit(
        int $userId,
        float $amount,
        string $walletType,
        string $category,
        string $description,
        ?int $relatedId = null,
        ?string $source = null,
        ?array $metadata = null
    ): Transaction {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Debit amount must be positive');
        }

        return DB::transaction(function () use (
            $userId, $amount, $walletType, $category, $description, $relatedId, $source, $metadata
        ) {
            $user = User::findOrFail($userId);
            $wallet = $user->getWallet($walletType);
            $transactionType = $this->mapWalletTypeToTransactionType($walletType, 'debit');

            // Check balance before creating transaction
            if (!$wallet->hasSufficientBalance($amount)) {
                throw new InsufficientBalanceException(
                    "Insufficient {$walletType} balance. Required: {$amount}, Available: {$wallet->balance}",
                    $walletType,
                    $amount,
                    $wallet->balance
                );
            }

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $userId,
                'amount' => $amount,
                'type' => $transactionType,
                'category' => $category,
                'description' => $description,
                'status' => Transaction::STATUS_PENDING,
                'related_id' => $relatedId,
                'source' => $source,
                'metadata' => $metadata,
            ]);

            try {
                // Debit the wallet with optimistic locking
                $wallet->debit($amount);
                
                // Mark transaction as confirmed
                $transaction->markAsConfirmed();

                // Log the transaction
                Log::info('Wallet debited', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'wallet_type' => $walletType,
                    'transaction_type' => $transactionType,
                    'category' => $category,
                    'transaction_id' => $transaction->id,
                ]);

                // Send notification
                $this->notifyUser($user, 'debit', $transaction);

                return $transaction;

            } catch (InsufficientBalanceException $e) {
                $transaction->markAsFailed('Insufficient balance: ' . $e->getMessage());
                throw $e;
            } catch (OptimisticLockException $e) {
                $transaction->markAsFailed('Optimistic lock conflict: ' . $e->getMessage());
                throw $e;
            } catch (\Exception $e) {
                $transaction->markAsFailed('Debit failed: ' . $e->getMessage());
                Log::error('Debit operation failed', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'wallet_type' => $walletType,
                    'error' => $e->getMessage(),
                    'transaction_id' => $transaction->id,
                ]);
                throw $e;
            }
        });
    }

    /**
     * Refund a transaction (full or partial).
     */
    public function refund(
        int $transactionId,
        ?float $amount = null,
        ?string $reason = null,
        ?int $adminUserId = null
    ): Transaction {
        return DB::transaction(function () use ($transactionId, $amount, $reason, $adminUserId) {
            $originalTransaction = Transaction::findOrFail($transactionId);

            if (!$originalTransaction->isConfirmed()) {
                throw new \InvalidArgumentException('Can only refund confirmed transactions');
            }

            // Calculate refund amount
            $refundAmount = $amount ?? $originalTransaction->amount;
            
            // Validate refund amount
            $totalRefunded = $originalTransaction->childTransactions()
                ->where('category', Transaction::CATEGORY_REFUND)
                ->where('status', Transaction::STATUS_CONFIRMED)
                ->sum('amount');

            if (($totalRefunded + $refundAmount) > $originalTransaction->amount) {
                throw new \InvalidArgumentException('Refund amount exceeds original transaction amount');
            }

            // Create refund transaction
            $refundTransaction = $this->credit(
                $originalTransaction->user_id,
                $refundAmount,
                $originalTransaction->type,
                Transaction::CATEGORY_REFUND,
                $reason ?? "Refund for transaction {$originalTransaction->reference}",
                $originalTransaction->related_id,
                'refund',
                [
                    'original_transaction_id' => $originalTransaction->id,
                    'original_reference' => $originalTransaction->reference,
                    'refund_reason' => $reason,
                    'admin_user_id' => $adminUserId,
                ]
            );

            // Link to original transaction
            $refundTransaction->update(['parent_transaction_id' => $originalTransaction->id]);

            // Log audit if admin initiated
            if ($adminUserId) {
                AuditLog::log(
                    $adminUserId,
                    'transaction_refund',
                    $originalTransaction->user_id,
                    ['original_transaction_id' => $originalTransaction->id],
                    ['refund_transaction_id' => $refundTransaction->id, 'amount' => $refundAmount],
                    $reason
                );
            }

            Log::info('Transaction refunded', [
                'original_transaction_id' => $originalTransaction->id,
                'refund_transaction_id' => $refundTransaction->id,
                'refund_amount' => $refundAmount,
                'admin_user_id' => $adminUserId,
            ]);

            return $refundTransaction;
        });
    }

    /**
     * Handle transaction failure and setup retry mechanism.
     */
    public function handleFailure(
        int $transactionId,
        string $reason,
        bool $canRetry = true
    ): bool {
        return DB::transaction(function () use ($transactionId, $reason, $canRetry) {
            $transaction = Transaction::findOrFail($transactionId);

            if (!$transaction->isPending()) {
                throw new \InvalidArgumentException('Can only handle failure for pending transactions');
            }

            // Mark as failed
            $transaction->markAsFailed($reason);

            // Setup retry if applicable
            if ($canRetry && $transaction->retry_count < 3) {
                $transaction->update([
                    'retry_until' => Carbon::now()->addHours(24),
                ]);

                Log::info('Transaction marked for retry', [
                    'transaction_id' => $transaction->id,
                    'retry_count' => $transaction->retry_count,
                    'retry_until' => $transaction->retry_until,
                ]);
            } else {
                Log::warning('Transaction failed permanently', [
                    'transaction_id' => $transaction->id,
                    'reason' => $reason,
                ]);

                // Notify admins if retries exhausted
                if ($transaction->retry_count >= 3) {
                    $this->notifyAdminsOfFailure($transaction, $reason);
                }
            }

            return true;
        });
    }

    /**
     * Retry a failed transaction.
     */
    public function retryTransaction(int $transactionId): Transaction
    {
        return DB::transaction(function () use ($transactionId) {
            $transaction = Transaction::findOrFail($transactionId);

            if (!$transaction->canRetry()) {
                throw new \InvalidArgumentException('Transaction cannot be retried');
            }

            $transaction->incrementRetryCount();

            // Reset status to pending
            $transaction->update(['status' => Transaction::STATUS_PENDING]);

            // Attempt the operation again based on category
            try {
                if (in_array($transaction->category, [
                    Transaction::CATEGORY_CREDIT_PURCHASE,
                    Transaction::CATEGORY_AD_EARNING
                ])) {
                    // Re-credit the wallet
                    $wallet = $transaction->user->getWallet($transaction->type);
                    $wallet->credit($transaction->amount);
                    $transaction->markAsConfirmed();
                } elseif (in_array($transaction->category, [
                    Transaction::CATEGORY_BATCH_JOIN,
                    Transaction::CATEGORY_WITHDRAWAL,
                    Transaction::CATEGORY_WHATSAPP_CHANGE_FEE
                ])) {
                    // Re-debit the wallet
                    $wallet = $transaction->user->getWallet($transaction->type);
                    $wallet->debit($transaction->amount);
                    $transaction->markAsConfirmed();
                }

                Log::info('Transaction retry successful', [
                    'transaction_id' => $transaction->id,
                    'retry_count' => $transaction->retry_count,
                ]);

            } catch (\Exception $e) {
                $this->handleFailure($transaction->id, 'Retry failed: ' . $e->getMessage(), false);
                throw $e;
            }

            return $transaction;
        });
    }

    /**
     * Get transaction history for a user with filters.
     */
    public function getUserTransactionHistory(
        int $userId,
        ?string $category = null,
        ?string $type = null,
        ?string $status = null,
        int $perPage = 15
    ) {
        $query = Transaction::where('user_id', $userId)
            ->with(['parentTransaction', 'childTransactions'])
            ->orderBy('created_at', 'desc');

        if ($category) {
            $query->where('category', $category);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    /**
     * Check if user should be notified for this transaction.
     */
    private function shouldNotifyUser(float $amount, string $type): bool
    {
        // Notify for significant amounts
        return match ($type) {
            Wallet::TYPE_CREDITS => $amount >= 100,
            Wallet::TYPE_NAIRA => $amount >= 1000,
            Wallet::TYPE_EARNINGS => $amount >= 500,
            default => false,
        };
    }

    /**
     * Send notification to user about transaction.
     */
    private function notifyUser(User $user, string $action, Transaction $transaction): void
    {
        // Implementation would depend on notification channels
        // For now, just log
        Log::info('User notification sent', [
            'user_id' => $user->id,
            'action' => $action,
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
            'type' => $transaction->type,
        ]);
    }

    /**
     * Notify admins of transaction failure.
     */
    private function notifyAdminsOfFailure(Transaction $transaction, string $reason): void
    {
        Log::critical('Transaction failed permanently', [
            'transaction_id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'amount' => $transaction->amount,
            'type' => $transaction->type,
            'category' => $transaction->category,
            'reason' => $reason,
            'retry_count' => $transaction->retry_count,
        ]);
    }

    /**
     * Create escrow transaction for channel ad.
     */
    public function createEscrow(
        User $advertiser,
        float $amount,
        int $channelAdApplicationId,
        string $description
    ): Transaction {
        return DB::transaction(function () use ($advertiser, $amount, $channelAdApplicationId, $description) {
            // Debit advertiser's wallet
            $escrowTransaction = $this->debit(
                $advertiser->id,
                $amount,
                Transaction::TYPE_NAIRA,
                Transaction::CATEGORY_CHANNEL_AD_ESCROW,
                $description,
                $channelAdApplicationId,
                'wallet',
                [
                    'channel_ad_application_id' => $channelAdApplicationId,
                    'escrow_status' => 'held',
                ]
            );
            
            Log::info('Escrow created for channel ad', [
                'escrow_transaction_id' => $escrowTransaction->id,
                'advertiser_id' => $advertiser->id,
                'amount' => $amount,
                'application_id' => $channelAdApplicationId,
            ]);
            
            return $escrowTransaction;
        });
    }

    /**
     * Release escrow payment to channel owner with admin fee deduction.
     */
    public function releaseEscrow(
        int $escrowTransactionId,
        User $channelOwner,
        string $description,
        float $adminFeePercentage = 10.0
    ): array {
        return DB::transaction(function () use ($escrowTransactionId, $channelOwner, $description, $adminFeePercentage) {
            $escrowTransaction = Transaction::findOrFail($escrowTransactionId);
            
            if (!in_array($escrowTransaction->category, [Transaction::CATEGORY_CHANNEL_AD_ESCROW, Transaction::CATEGORY_CHANNEL_SALE_ESCROW])) {
                throw new \InvalidArgumentException('Transaction is not an escrow transaction');
            }
            
            if ($escrowTransaction->status !== Transaction::STATUS_CONFIRMED) {
                throw new \InvalidArgumentException('Escrow transaction is not confirmed');
            }
            
            $totalAmount = $escrowTransaction->amount;
            $adminFee = round($totalAmount * ($adminFeePercentage / 100), 2);
            $channelOwnerAmount = $totalAmount - $adminFee;
            
            // Create payment transaction for channel owner
            $channelPaymentTransaction = $this->credit(
                $channelOwner->id,
                $channelOwnerAmount,
                Transaction::TYPE_NAIRA,
                Transaction::CATEGORY_CHANNEL_AD_PAYMENT,
                $description,
                $escrowTransaction->related_id,
                'escrow_release',
                [
                    'escrow_transaction_id' => $escrowTransactionId,
                    'escrow_reference' => $escrowTransaction->reference,
                    'admin_fee_deducted' => $adminFee,
                    'admin_fee_percentage' => $adminFeePercentage,
                ]
            );
            
            // Create admin fee transaction
            $adminFeeTransaction = null;
            if ($adminFee > 0) {
                // Find admin user (you may need to adjust this logic)
                $adminUser = User::where('role', 'admin')->first() ?? User::find(1);
                
                $adminFeeTransaction = $this->credit(
                    $adminUser->id,
                    $adminFee,
                    Transaction::TYPE_NAIRA,
                    Transaction::CATEGORY_MANUAL_ADJUSTMENT,
                    "Admin fee (${adminFeePercentage}%) from channel ad payment",
                    $escrowTransaction->related_id,
                    'admin_fee',
                    [
                        'escrow_transaction_id' => $escrowTransactionId,
                        'channel_payment_transaction_id' => $channelPaymentTransaction->id,
                        'fee_percentage' => $adminFeePercentage,
                        'original_amount' => $totalAmount,
                    ]
                );
            }
            
            // Link transactions to the escrow transaction
            $channelPaymentTransaction->update(['parent_transaction_id' => $escrowTransactionId]);
            if ($adminFeeTransaction) {
                $adminFeeTransaction->update(['parent_transaction_id' => $escrowTransactionId]);
            }
            
            Log::info('Escrow payment released with admin fee', [
                'escrow_transaction_id' => $escrowTransactionId,
                'channel_payment_transaction_id' => $channelPaymentTransaction->id,
                'admin_fee_transaction_id' => $adminFeeTransaction?->id,
                'channel_owner_id' => $channelOwner->id,
                'total_amount' => $totalAmount,
                'channel_owner_amount' => $channelOwnerAmount,
                'admin_fee' => $adminFee,
            ]);
            
            return [
                'channel_payment' => $channelPaymentTransaction,
                'admin_fee_payment' => $adminFeeTransaction,
                'total_amount' => $totalAmount,
                'channel_owner_amount' => $channelOwnerAmount,
                'admin_fee' => $adminFee,
            ];
        });
    }

    /**
     * Refund escrow payment to advertiser.
     */
    public function refundEscrow(
        int $escrowTransactionId,
        string $reason
    ): Transaction {
        return DB::transaction(function () use ($escrowTransactionId, $reason) {
            $escrowTransaction = Transaction::findOrFail($escrowTransactionId);
            
            if (!in_array($escrowTransaction->category, [Transaction::CATEGORY_CHANNEL_AD_ESCROW, Transaction::CATEGORY_CHANNEL_SALE_ESCROW])) {
                throw new \InvalidArgumentException('Transaction is not an escrow transaction');
            }
            
            if ($escrowTransaction->status !== Transaction::STATUS_CONFIRMED) {
                throw new \InvalidArgumentException('Escrow transaction is not confirmed');
            }
            
            // Create refund transaction
            $refundTransaction = $this->credit(
                $escrowTransaction->user_id,
                $escrowTransaction->amount,
                $escrowTransaction->type,
                Transaction::CATEGORY_REFUND,
                "Escrow refund: {$reason}",
                $escrowTransaction->related_id,
                'escrow_refund',
                [
                    'escrow_transaction_id' => $escrowTransactionId,
                    'escrow_reference' => $escrowTransaction->reference,
                    'refund_reason' => $reason,
                ]
            );
            
            // Link to original transaction
            $refundTransaction->update(['parent_transaction_id' => $escrowTransactionId]);
            
            Log::info('Escrow refunded', [
                'escrow_transaction_id' => $escrowTransactionId,
                'refund_transaction_id' => $refundTransaction->id,
                'user_id' => $escrowTransaction->user_id,
                'amount' => $escrowTransaction->amount,
                'reason' => $reason,
            ]);
            
            return $refundTransaction;
        });
    }

    /**
     * Get wallet balance summary for a user.
     */
    public function getUserBalanceSummary(int $userId): array
    {
        $user = User::findOrFail($userId);

        return [
            'credits' => [
                'balance' => $user->getCreditWallet()->balance,
                'formatted' => $user->formatted_credits,
            ],
            'naira' => [
                'balance' => $user->getNairaWallet()->balance,
                'formatted' => $user->formatted_naira,
            ],
            'earnings' => [
                'balance' => $user->getEarningsWallet()->balance,
                'formatted' => $user->formatted_earnings,
            ],
        ];
    }
}