<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    /**
     * The wallet type that has insufficient balance.
     */
    protected string $walletType;

    /**
     * The required amount.
     */
    protected float $requiredAmount;

    /**
     * The available amount.
     */
    protected float $availableAmount;

    /**
     * Create a new exception instance.
     */
    public function __construct(
        string $message = 'Insufficient balance',
        string $walletType = '',
        float $requiredAmount = 0,
        float $availableAmount = 0,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->walletType = $walletType;
        $this->requiredAmount = $requiredAmount;
        $this->availableAmount = $availableAmount;
    }

    /**
     * Get the wallet type.
     */
    public function getWalletType(): string
    {
        return $this->walletType;
    }

    /**
     * Get the required amount.
     */
    public function getRequiredAmount(): float
    {
        return $this->requiredAmount;
    }

    /**
     * Get the available amount.
     */
    public function getAvailableAmount(): float
    {
        return $this->availableAmount;
    }

    /**
     * Get the shortage amount.
     */
    public function getShortageAmount(): float
    {
        return max(0, $this->requiredAmount - $this->availableAmount);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Insufficient balance',
                'message' => $this->getMessage(),
                'wallet_type' => $this->walletType,
                'required_amount' => $this->requiredAmount,
                'available_amount' => $this->availableAmount,
                'shortage_amount' => $this->getShortageAmount(),
            ], 422);
        }

        return back()->withErrors([
            'balance' => $this->getMessage()
        ])->withInput();
    }
}