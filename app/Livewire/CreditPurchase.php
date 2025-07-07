<?php

namespace App\Livewire;

use App\Services\PaystackService;
use App\Models\Transaction;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreditPurchase extends Component
{
    public int $selectedPackage = 0;
    public float $customAmount = 0;
    public int $customCredits = 0;
    public bool $isProcessing = false;
    public ?int $retryTransactionId = null;
    public array $packages = [];
    public array $pricingConfig = [];

    protected $rules = [
        'customAmount' => 'nullable|numeric|min:300',
        'customCredits' => 'nullable|integer|min:100',
    ];

    protected $messages = [
        'customAmount.min' => 'Minimum amount is ₦300 (100 credits)',
        'customCredits.min' => 'Minimum purchase is 100 credits',
    ];

    public function mount(?int $retry = null)
    {
        $paystackService = app(PaystackService::class);
        $this->pricingConfig = $paystackService->getPricingConfig();
        $this->packages = $this->pricingConfig['packages'];
        
        if ($retry) {
            $this->retryTransactionId = $retry;
            $this->loadRetryTransaction();
        }
    }

    public function updatedCustomAmount($value)
    {
        if ($value && $value >= 300) {
            $this->customCredits = (int) floor($value / $this->pricingConfig['credit_price']);
            $this->selectedPackage = -1; // Custom package
        }
    }

    public function updatedCustomCredits($value)
    {
        if ($value && $value >= 100) {
            $this->customAmount = $value * $this->pricingConfig['credit_price'];
            $this->selectedPackage = -1; // Custom package
        }
    }

    public function selectPackage($index)
    {
        $this->selectedPackage = $index;
        $this->customAmount = 0;
        $this->customCredits = 0;
        $this->resetValidation();
    }

    public function purchaseCredits()
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            $amount = 0;
            $credits = 0;

            if ($this->selectedPackage >= 0 && isset($this->packages[$this->selectedPackage])) {
                // Predefined package
                $package = $this->packages[$this->selectedPackage];
                $amount = $package['amount'];
                $credits = $package['total_credits'];
            } elseif ($this->selectedPackage === -1) {
                // Custom amount
                $this->validate();
                $amount = $this->customAmount;
                $credits = $this->customCredits;
            } else {
                throw new \InvalidArgumentException('Please select a package or enter a custom amount');
            }

            if ($amount < $this->pricingConfig['minimum_amount']) {
                throw new \InvalidArgumentException('Minimum amount is ₦' . number_format($this->pricingConfig['minimum_amount']));
            }

            $paystackService = app(PaystackService::class);
            $user = Auth::user();

            $result = $paystackService->initializePayment(
                $user->id,
                $amount,
                $user->email,
                route('credits.callback'),
                [
                    'package_index' => $this->selectedPackage,
                    'is_retry' => $this->retryTransactionId ? true : false,
                    'original_transaction_id' => $this->retryTransactionId,
                ]
            );

            if ($result['success']) {
                // Redirect to Paystack payment page
                return redirect()->away($result['authorization_url']);
            } else {
                session()->flash('error', $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('Credit purchase error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'amount' => $amount ?? null,
                'credits' => $credits ?? null,
            ]);

            session()->flash('error', 'Failed to process payment: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    private function loadRetryTransaction()
    {
        $transaction = Transaction::where('id', $this->retryTransactionId)
            ->where('user_id', Auth::id())
            ->where('status', Transaction::STATUS_FAILED)
            ->first();

        if ($transaction && isset($transaction->metadata['naira_amount'])) {
            $this->customAmount = $transaction->metadata['naira_amount'];
            $this->customCredits = $transaction->metadata['credits'] ?? $transaction->amount;
            $this->selectedPackage = -1;
            
            session()->flash('info', 'Retrying failed transaction: ' . $transaction->reference);
        } else {
            $this->retryTransactionId = null;
            session()->flash('error', 'Transaction not found or cannot be retried.');
        }
    }

    public function render()
    {
        return view('livewire.credit-purchase', [
            'user' => Auth::user(),
            'paystackPublicKey' => app(PaystackService::class)->getPublicKey(),
        ]);
    }
}