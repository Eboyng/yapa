<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SufficientWalletBalance implements ValidationRule
{
    protected User $user;
    protected string $currency;

    public function __construct(User $user, string $currency = 'naira')
    {
        $this->user = $user;
        $this->currency = $currency;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $amount = (float) $value;
        
        // Get the user's wallet balance for the specified currency
        $walletBalance = $this->getUserWalletBalance();
        
        if ($walletBalance < $amount) {
            $fail(sprintf(
                'Insufficient wallet balance. Required: ₦%s, Available: ₦%s',
                number_format($amount, 2),
                number_format($walletBalance, 2)
            ));
        }
    }

    /**
     * Get the user's wallet balance for the specified currency.
     */
    protected function getUserWalletBalance(): float
    {
        // Assuming the user has a wallet relationship or method
        // This should match your existing wallet implementation
        if (method_exists($this->user, 'getWalletBalance')) {
            return $this->user->getWalletBalance($this->currency);
        }
        
        // Fallback: check if user has a wallet relationship
        if ($this->user->relationLoaded('wallets') || $this->user->wallets()) {
            $wallet = $this->user->wallets()->where('currency', $this->currency)->first();
            return $wallet ? $wallet->balance : 0;
        }
        
        // If no wallet system is implemented, return 0
        return 0;
    }
}