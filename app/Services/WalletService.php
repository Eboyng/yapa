<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class WalletService
{
    /**
     * Fund a user's wallet.
     */
    public function fundWallet(User $user, string $walletType, float $amount, string $description = null, User $adminUser = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $walletType, $amount, $description, $adminUser) {
            $adminUser = $adminUser ?? Auth::user();
            
            // Validate wallet type
            if (!in_array($walletType, ['credits', 'naira', 'earnings'])) {
                throw new Exception('Invalid wallet type');
            }
            
            // Validate amount
            if ($amount <= 0) {
                throw new Exception('Amount must be greater than zero');
            }
            
            // Get current balance using wallet system
            $wallet = $user->getWallet($walletType);
            $balanceBefore = $wallet->balance;
            $balanceAfter = $balanceBefore + $amount;
            
            // Update user balance using wallet system
            $wallet->deposit($amount);
            
            // Create wallet transaction
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'admin_user_id' => $adminUser->id,
                'wallet_type' => $walletType,
                'amount' => $amount,
                'type' => WalletTransaction::TYPE_CREDIT,
                'category' => WalletTransaction::CATEGORY_ADMIN_FUNDING,
                'status' => WalletTransaction::STATUS_COMPLETED,
                'description' => $description ?? "Admin funding of {$amount} {$walletType}",
                'reference' => WalletTransaction::generateReference(),
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'metadata' => [
                    'admin_user_name' => $adminUser->name,
                    'admin_user_email' => $adminUser->email,
                ],
            ]);
            
            // Create audit log
            AuditLog::create([
                'admin_user_id' => $adminUser->id,
                'target_user_id' => $user->id,
                'action' => 'wallet_funded',
                'description' => "Funded {$user->name}'s {$walletType} wallet with {$amount}",
                'metadata' => [
                    'wallet_type' => $walletType,
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'transaction_reference' => $transaction->reference,
                ],
            ]);
            
            return $transaction;
        });
    }
    
    /**
     * Deduct from a user's wallet.
     */
    public function deductWallet(User $user, string $walletType, float $amount, string $description = null, User $adminUser = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $walletType, $amount, $description, $adminUser) {
            $adminUser = $adminUser ?? Auth::user();
            
            // Validate wallet type
            if (!in_array($walletType, ['credits', 'naira', 'earnings'])) {
                throw new Exception('Invalid wallet type');
            }
            
            // Validate amount
            if ($amount <= 0) {
                throw new Exception('Amount must be greater than zero');
            }
            
            // Get current balance using wallet system
            $wallet = $user->getWallet($walletType);
            $balanceBefore = $wallet->balance;
            
            // Check if sufficient balance
            if ($balanceBefore < $amount) {
                throw new Exception('Insufficient balance');
            }
            
            $balanceAfter = $balanceBefore - $amount;
            
            // Update user balance using wallet system
            $wallet->withdraw($amount);
            
            // Create wallet transaction
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'admin_user_id' => $adminUser->id,
                'wallet_type' => $walletType,
                'amount' => $amount,
                'type' => WalletTransaction::TYPE_DEBIT,
                'category' => WalletTransaction::CATEGORY_ADMIN_DEDUCTION,
                'status' => WalletTransaction::STATUS_COMPLETED,
                'description' => $description ?? "Admin deduction of {$amount} {$walletType}",
                'reference' => WalletTransaction::generateReference(),
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'metadata' => [
                    'admin_user_name' => $adminUser->name,
                    'admin_user_email' => $adminUser->email,
                ],
            ]);
            
            // Create audit log
            AuditLog::create([
                'admin_user_id' => $adminUser->id,
                'target_user_id' => $user->id,
                'action' => 'wallet_deducted',
                'description' => "Deducted {$amount} from {$user->name}'s {$walletType} wallet",
                'metadata' => [
                    'wallet_type' => $walletType,
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'transaction_reference' => $transaction->reference,
                ],
            ]);
            
            return $transaction;
        });
    }
    
    /**
     * Get wallet balance for a user.
     */
    public function getWalletBalance(User $user, string $walletType): float
    {
        return $user->getWallet($walletType)->balance;
    }
    
    /**
     * Get wallet transaction history for a user.
     */
    public function getWalletTransactions(User $user, string $walletType = null, int $limit = 50)
    {
        $query = WalletTransaction::where('user_id', $user->id)
            ->with(['adminUser'])
            ->orderBy('created_at', 'desc');
            
        if ($walletType) {
            $query->where('wallet_type', $walletType);
        }
        
        return $query->limit($limit)->get();
    }
}