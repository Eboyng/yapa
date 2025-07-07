<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';
    
    const CATEGORY_ADMIN_FUNDING = 'admin_funding';
    const CATEGORY_ADMIN_DEDUCTION = 'admin_deduction';
    const CATEGORY_TASK_PAYMENT = 'task_payment';
    const CATEGORY_WITHDRAWAL = 'withdrawal';
    const CATEGORY_REFUND = 'refund';
    
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'admin_user_id',
        'wallet_type',
        'amount',
        'type',
        'category',
        'status',
        'description',
        'reference',
        'metadata',
        'balance_before',
        'balance_after',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    /**
     * Get the user that owns the wallet transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin user who performed the transaction.
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Generate a unique reference for the transaction.
     */
    public static function generateReference(): string
    {
        return 'WT' . time() . rand(1000, 9999);
    }

    /**
     * Get available wallet types.
     */
    public static function getWalletTypes(): array
    {
        return [
            'credits' => 'Credits',
            'naira' => 'Naira',
            'earnings' => 'Earnings',
        ];
    }

    /**
     * Get available transaction types.
     */
    public static function getTransactionTypes(): array
    {
        return [
            self::TYPE_CREDIT => 'Credit',
            self::TYPE_DEBIT => 'Debit',
        ];
    }

    /**
     * Get available categories.
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_ADMIN_FUNDING => 'Admin Funding',
            self::CATEGORY_ADMIN_DEDUCTION => 'Admin Deduction',
            self::CATEGORY_TASK_PAYMENT => 'Task Payment',
            self::CATEGORY_WITHDRAWAL => 'Withdrawal',
            self::CATEGORY_REFUND => 'Refund',
        ];
    }

    /**
     * Get available statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}