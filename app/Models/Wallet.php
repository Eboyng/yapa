<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\OptimisticLockException;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'balance',
        'currency',
        'is_active',
        'version',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'version' => 'integer',
    ];

    /**
     * Wallet types.
     */
    const TYPE_CREDITS = 'credits';
    const TYPE_NAIRA = 'naira';
    const TYPE_EARNINGS = 'earnings';

    // Currency types
    const CURRENCY_NGN = 'NGN';
    const CURRENCY_CREDITS = 'CREDITS';

    /**
     * Get the user that owns the wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all transactions for this wallet.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Credit the wallet balance with optimistic locking.
     */
    public function credit(float $amount): bool
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit amount must be positive');
        }

        $currentVersion = $this->version;
        
        $updated = static::where('id', $this->id)
            ->where('version', $currentVersion)
            ->update([
                'balance' => $this->balance + $amount,
                'version' => $currentVersion + 1,
                'updated_at' => now(),
            ]);

        if (!$updated) {
            throw new OptimisticLockException('Wallet was modified by another process');
        }

        $this->refresh();
        return true;
    }

    /**
     * Debit the wallet balance with optimistic locking.
     */
    public function debit(float $amount): bool
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Debit amount must be positive');
        }

        if ($this->balance < $amount) {
            throw new InsufficientBalanceException(
                "Insufficient {$this->type} balance. Required: {$amount}, Available: {$this->balance}"
            );
        }

        $currentVersion = $this->version;
        
        $updated = static::where('id', $this->id)
            ->where('version', $currentVersion)
            ->where('balance', '>=', $amount) // Double-check balance
            ->update([
                'balance' => $this->balance - $amount,
                'version' => $currentVersion + 1,
                'updated_at' => now(),
            ]);

        if (!$updated) {
            // Check if it's a version conflict or insufficient balance
            $current = static::find($this->id);
            if ($current->version !== $currentVersion) {
                throw new OptimisticLockException('Wallet was modified by another process');
            } else {
                throw new InsufficientBalanceException(
                    "Insufficient {$this->type} balance. Required: {$amount}, Available: {$current->balance}"
                );
            }
        }

        $this->refresh();
        return true;
    }

    /**
     * Get or create wallet for user and type.
     */
    public static function getOrCreate(int $userId, string $type): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId, 'type' => $type],
            ['balance' => 0, 'version' => 1]
        );
    }

    /**
     * Check if wallet has sufficient balance.
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Get formatted balance.
     */
    public function getFormattedBalanceAttribute(): string
    {
        switch ($this->type) {
            case self::TYPE_CREDITS:
                return number_format($this->balance, 0) . ' credits';
            case self::TYPE_NAIRA:
            case self::TYPE_EARNINGS:
                return '₦' . number_format($this->balance, 2);
            default:
                return (string) $this->balance;
        }
    }

    /**
     * Scope to get wallet by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter active wallets.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get wallet balance summary.
     */
    public function getBalanceSummary(): array
    {
        return [
            'type' => $this->type,
            'balance' => $this->balance,
            'formatted_balance' => $this->formatted_balance,
            'currency' => $this->currency ?? ($this->type === self::TYPE_CREDITS ? self::CURRENCY_CREDITS : self::CURRENCY_NGN),
            'is_active' => $this->is_active ?? true,
            'last_updated' => $this->updated_at,
        ];
    }

    /**
     * Create default wallets for a user.
     */
    public static function createDefaultWallets(User $user): array
    {
        $wallets = [];

        // Create credits wallet
        $wallets['credits'] = static::firstOrCreate(
            ['user_id' => $user->id, 'type' => self::TYPE_CREDITS],
            [
                'balance' => config('app.free_credits_on_registration', 100),
                'currency' => self::CURRENCY_CREDITS,
                'is_active' => true,
                'version' => 1,
            ]
        );

        // Create naira wallet
        $wallets['naira'] = static::firstOrCreate(
            ['user_id' => $user->id, 'type' => self::TYPE_NAIRA],
            [
                'balance' => 0,
                'currency' => self::CURRENCY_NGN,
                'is_active' => true,
                'version' => 1,
            ]
        );

        // Create earnings wallet
        $wallets['earnings'] = static::firstOrCreate(
            ['user_id' => $user->id, 'type' => self::TYPE_EARNINGS],
            [
                'balance' => 0,
                'currency' => self::CURRENCY_NGN,
                'is_active' => true,
                'version' => 1,
            ]
        );

        return $wallets;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($wallet) {
            if (is_null($wallet->version)) {
                $wallet->version = 1;
            }
            if (is_null($wallet->is_active)) {
                $wallet->is_active = true;
            }
        });
    }
}