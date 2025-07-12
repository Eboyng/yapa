<?php

namespace App\Livewire;

use App\Services\PaystackService;
use App\Services\TransactionService;
use App\Services\AirtimeService;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CreditPurchase extends Component
{
    // Credit Purchase Properties
    public bool $isProcessing = false;
    public ?int $retryTransactionId = null;
    public array $pricingConfig = [];
    
    // Fund Wallet Modal Properties
    public bool $showFundModal = false;
    public float $fundAmount = 0;
    
    // Credit package properties
    public $selectedCreditPackage = null;
    public $customCreditAmount = null;
    public $minCreditAmount = 100;
    public $creditPackages = [];
    
    // Withdrawal Properties
    public bool $showWithdrawModal = false;
    public string $withdrawalMethod = '';
    public float $amount = 0;
    public string $accountNumber = '';
    public string $accountName = '';
    public string $bankCode = '';
    public string $palmpayNumber = '';
    public string $airtimeNumber = '';
    public string $airtimeNetwork = '';
    public int $airtimeNetworkId = 0;
    public string $detectedNetwork = '';
    public array $fees = [];
    public float $netAmount = 0;
    public array $banks = [];
    public array $networks = [];
    public bool $airtimeApiEnabled = false;
    
    public const MINIMUM_WITHDRAWAL = 1000;
    public const BANK_TRANSFER_FEE_PERCENTAGE = 1.5;
    public const PALMPAY_FEE_PERCENTAGE = 1.0;
    public const AIRTIME_FEE_PERCENTAGE = 2.0;

    protected $rules = [
        // Removed old validation rules - now using credit packages
        'fundAmount' => 'required|numeric|min:300',
        'amount' => 'required|numeric|min:1000',
        'withdrawalMethod' => 'required|string|in:bank_account,palmpay,airtime',
        'accountNumber' => 'required_if:withdrawalMethod,bank_account|string|size:10',
        'bankCode' => 'required_if:withdrawalMethod,bank_account|string',
        'palmpayNumber' => 'required_if:withdrawalMethod,palmpay|string|size:11',
        'airtimeNumber' => 'required_if:withdrawalMethod,airtime|string|size:11',
        'airtimeNetwork' => 'required_if:withdrawalMethod,airtime|string',
    ];

    protected $messages = [
        // Removed old validation messages - now using credit packages
        'fundAmount.min' => 'Minimum funding amount is ₦300',
        'amount.min' => 'Minimum withdrawal amount is ₦1,000',
        'accountNumber.size' => 'Account number must be exactly 10 digits',
        'palmpayNumber.size' => 'PalmPay number must be exactly 11 digits',
        'airtimeNumber.size' => 'Phone number must be exactly 11 digits',
    ];

    public function mount(?int $retry = null)
    {
        $paystackService = app(PaystackService::class);
        $this->pricingConfig = $paystackService->getPricingConfig();
        
        // Load withdrawal data
        $this->loadBanks();
        $this->loadAirtimeNetworks();
        $this->initializeCreditPackages();
        
        if ($retry) {
            $this->retryTransactionId = $retry;
            $this->loadRetryTransaction();
        }
    }

    public function updatedFundAmount($value)
    {
        // Validate minimum amount
        if ($value && $value < $this->pricingConfig['minimum_amount']) {
            $this->addError('fundAmount', 'Minimum funding amount is ₦' . number_format($this->pricingConfig['minimum_amount']));
        }
    }

    public function updatedCustomCreditAmount($value)
    {
        if ($value && $value >= $this->minCreditAmount) {
            $this->selectedCreditPackage = null;
            $this->resetValidation(['customCreditAmount']);
        }
    }
    
    public function selectCreditPackage($index)
    {
        $this->selectedCreditPackage = $index;
        $this->customCreditAmount = null;
        $this->resetValidation();
    }
    
    private function initializeCreditPackages()
    {
        $creditPrice = $this->pricingConfig['credit_price'];
        
        $this->creditPackages = [
            [
                'credits' => 100,
                'amount' => 100 * $creditPrice,
                'bonus' => 0,
                'total_credits' => 100,
                'label' => 'Starter'
            ],
            [
                'credits' => 500,
                'amount' => 500 * $creditPrice,
                'bonus' => 50,
                'total_credits' => 550,
                'label' => 'Basic'
            ],
            [
                'credits' => 1000,
                'amount' => 1000 * $creditPrice,
                'bonus' => 150,
                'total_credits' => 1150,
                'label' => 'Standard'
            ],
            [
                'credits' => 2000,
                'amount' => 2000 * $creditPrice,
                'bonus' => 400,
                'total_credits' => 2400,
                'label' => 'Premium'
            ],
            [
                'credits' => 5000,
                'amount' => 5000 * $creditPrice,
                'bonus' => 1250,
                'total_credits' => 6250,
                'label' => 'Ultimate'
            ],
            [
                'credits' => 10000,
                'amount' => 10000 * $creditPrice,
                'bonus' => 3000,
                'total_credits' => 13000,
                'label' => 'Enterprise'
            ]
        ];
    }
    


    // Fund Wallet Modal Methods
    public function openFundModal()
    {
        $this->showFundModal = true;
        $this->fundAmount = 0;
        $this->resetValidation();
    }

    public function closeFundModal()
    {
        $this->showFundModal = false;
        $this->fundAmount = 0;
        $this->resetValidation();
    }

    public function fundWallet()
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            $this->validate(['fundAmount' => 'required|numeric|min:300']);

            if ($this->fundAmount < $this->pricingConfig['minimum_amount']) {
                throw new \InvalidArgumentException('Minimum funding amount is ₦' . number_format($this->pricingConfig['minimum_amount']));
            }

            $paystackService = app(PaystackService::class);
            $user = Auth::user();

            $result = $paystackService->initializePayment(
                $user->id,
                $this->fundAmount,
                $user->email,
                route('paystack.callback'),
                [
                    'wallet_type' => 'naira',
                    'purchase_type' => 'naira_funding',
                    'amount' => $this->fundAmount,
                ]
            );

            if ($result['success']) {
                // Redirect to Paystack payment page
                return redirect()->away($result['authorization_url']);
            } else {
                session()->flash('error', $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('Naira wallet funding error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'amount' => $this->fundAmount,
            ]);

            session()->flash('error', 'Failed to process payment: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    public function purchaseCredits()
    {
        // This method now opens the fund wallet modal
        $this->openFundModal();
    }

    // Removed old purchaseCreditsViaPaystack method - now using fundWallet for Paystack payments

    private function loadRetryTransaction()
    {
        $transaction = Transaction::where('id', $this->retryTransactionId)
            ->where('user_id', Auth::id())
            ->where('status', Transaction::STATUS_FAILED)
            ->first();

        if ($transaction && isset($transaction->metadata['naira_amount'])) {
            // For retry transactions, set custom credit amount
            $this->customCreditAmount = $transaction->metadata['credits'] ?? $transaction->amount;
            $this->selectedCreditPackage = null;
            
            session()->flash('info', 'Retrying failed transaction: ' . $transaction->reference);
        } else {
            $this->retryTransactionId = null;
            session()->flash('error', 'Transaction not found or cannot be retried.');
        }
    }

  
    // Withdrawal Modal Methods
    public function openWithdrawModal()
    {
        $this->showWithdrawModal = true;
        $this->resetWithdrawalForm();
    }

    public function closeWithdrawModal()
    {
        $this->showWithdrawModal = false;
        $this->resetWithdrawalForm();
    }

    private function resetWithdrawalForm()
    {
        $this->withdrawalMethod = '';
        $this->amount = 0;
        $this->accountNumber = '';
        $this->accountName = '';
        $this->bankCode = '';
        $this->palmpayNumber = '';
        $this->airtimeNumber = '';
        $this->airtimeNetwork = '';
        $this->airtimeNetworkId = 0;
        $this->detectedNetwork = '';
        $this->fees = [];
        $this->netAmount = 0;
        $this->resetValidation();
    }

    // Withdrawal Amount Updates
    public function updatedAmount($value)
    {
        $this->calculateFees();
    }

    public function updatedWithdrawalMethod()
    {
        $this->calculateFees();
        $this->resetValidation();
    }

    public function updatedAccountNumber()
    {
        if (strlen($this->accountNumber) === 10 && !empty($this->bankCode)) {
            $this->resolveAccountName();
        }
    }

    public function updatedAirtimeNumber()
    {
        if (strlen($this->airtimeNumber) === 11) {
            $this->detectNetworkFromNumber();
        } else {
            $this->resetNetworkDetection();
        }
    }

    // Withdrawal Processing
    public function processWithdrawal()
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            $this->validate([
                'amount' => 'required|numeric|min:' . self::MINIMUM_WITHDRAWAL,
                'withdrawalMethod' => 'required|string|in:bank_account,palmpay,airtime',
            ]);

            $user = Auth::user();
            $earningsWallet = $user->getEarningsWallet();

            if ($this->amount > $earningsWallet->balance) {
                session()->flash('error', 'Insufficient balance for withdrawal.');
                return;
            }

            if ($this->netAmount <= 0) {
                session()->flash('error', 'Invalid withdrawal amount after fees.');
                return;
            }

            DB::beginTransaction();

            try {
                $transactionService = app(TransactionService::class);
                $result = null;

                switch ($this->withdrawalMethod) {
                    case 'bank_account':
                        $result = $this->processBankWithdrawal($transactionService, $user);
                        break;
                    case 'palmpay':
                        $result = $this->processPalmpayWithdrawal($transactionService, $user);
                        break;
                    case 'airtime':
                        $result = $this->processAirtimeWithdrawal($transactionService, $user);
                        break;
                }

                if ($result && $result['success']) {
                    DB::commit();
                    session()->flash('success', $result['message']);
                    $this->closeWithdrawModal();
                } else {
                    DB::rollBack();
                    session()->flash('error', $result['message'] ?? 'Withdrawal failed.');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Withdrawal processing error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'amount' => $this->amount,
                'method' => $this->withdrawalMethod,
            ]);

            session()->flash('error', 'Withdrawal failed: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    private function processBankWithdrawal($transactionService, $user)
    {
        $this->validate([
            'accountNumber' => 'required|string|size:10',
            'bankCode' => 'required|string',
        ]);

        return $transactionService->processWithdrawal(
            $user,
            $this->amount,
            'bank_account',
            [
                'account_number' => $this->accountNumber,
                'account_name' => $this->accountName,
                'bank_code' => $this->bankCode,
            ]
        );
    }

    private function processPalmpayWithdrawal($transactionService, $user)
    {
        $this->validate([
            'palmpayNumber' => 'required|string|size:11',
        ]);

        return $transactionService->processWithdrawal(
            $user,
            $this->amount,
            'palmpay',
            [
                'palmpay_number' => $this->palmpayNumber,
            ]
        );
    }

    private function processAirtimeWithdrawal($transactionService, $user)
    {
        $this->validate([
            'airtimeNumber' => 'required|string|size:11',
            'airtimeNetwork' => 'required|string',
        ]);

        $airtimeService = app(AirtimeService::class);
        
        if (!$airtimeService->isEnabled()) {
            return ['success' => false, 'message' => 'Airtime service is currently disabled'];
        }

        $validation = $airtimeService->validateAmount($this->amount);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }

        return $transactionService->processWithdrawal(
            $user,
            $this->amount,
            'airtime',
            [
                'airtime_number' => $this->airtimeNumber,
                'network_id' => $this->airtimeNetworkId,
                'network_name' => $this->detectedNetwork ?: $this->networks[$this->airtimeNetwork] ?? 'Unknown',
            ]
        );
    }

    private function calculateFees()
    {
        if ($this->amount < self::MINIMUM_WITHDRAWAL || empty($this->withdrawalMethod)) {
            $this->fees = [];
            $this->netAmount = 0;
            return;
        }

        $feePercentage = match ($this->withdrawalMethod) {
            'bank_account' => self::BANK_TRANSFER_FEE_PERCENTAGE,
            'palmpay' => self::PALMPAY_FEE_PERCENTAGE,
            'airtime' => self::AIRTIME_FEE_PERCENTAGE,
            default => 0
        };

        $percentageFee = ($this->amount * $feePercentage) / 100;
        $this->netAmount = $this->amount - $percentageFee;

        $this->fees = [
            'percentage' => $percentageFee,
            'total' => $percentageFee,
        ];
    }

    private function loadBanks()
    {
        try {
            $paystack = app(PaystackService::class);
            $this->banks = $paystack->getBanks();
        } catch (\Exception $e) {
            Log::error('Failed to load banks: ' . $e->getMessage());
            $this->banks = [];
        }
    }

    private function loadAirtimeNetworks()
    {
        try {
            $airtimeService = app(AirtimeService::class);
            $this->airtimeApiEnabled = $airtimeService->isEnabled();
            
            if ($this->airtimeApiEnabled) {
                $enabledNetworks = $airtimeService->getEnabledNetworks();
                $this->networks = [];
                
                foreach ($enabledNetworks as $network) {
                    $this->networks[$network['network_id']] = $network['name'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to load airtime networks: ' . $e->getMessage());
            $this->networks = [];
            $this->airtimeApiEnabled = false;
        }
    }

    private function detectNetworkFromNumber()
    {
        try {
            $airtimeService = app(AirtimeService::class);
            $network = $airtimeService->detectNetwork($this->airtimeNumber);
            
            if ($network) {
                $this->airtimeNetwork = (string) $network['network_id'];
                $this->airtimeNetworkId = $network['network_id'];
                $this->detectedNetwork = $network['name'];
                
                $this->dispatch('network-detected', [
                    'network' => $network['name'],
                    'networkId' => $network['network_id']
                ]);
            } else {
                $this->resetNetworkDetection();
            }
        } catch (\Exception $e) {
            Log::error('Network detection failed: ' . $e->getMessage());
            $this->resetNetworkDetection();
        }
    }

    private function resetNetworkDetection()
    {
        $this->airtimeNetwork = '';
        $this->airtimeNetworkId = 0;
        $this->detectedNetwork = '';
    }

    private function resolveAccountName()
    {
        try {
            $paystack = app(PaystackService::class);
            $result = $paystack->resolveAccountNumber($this->accountNumber, $this->bankCode);
            
            if ($result['success']) {
                $this->accountName = $result['data']['account_name'];
            } else {
                $this->accountName = '';
                session()->flash('error', 'Could not resolve account name: ' . $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Account resolution failed: ' . $e->getMessage());
            $this->accountName = '';
        }
    }

    public function purchaseCreditsWithNaira()
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            $amount = 0;
            $credits = 0;

            if ($this->selectedCreditPackage !== null && isset($this->creditPackages[$this->selectedCreditPackage])) {
                // Predefined credit package
                $package = $this->creditPackages[$this->selectedCreditPackage];
                $amount = $package['amount'];
                $credits = $package['total_credits'];
            } elseif ($this->customCreditAmount && $this->customCreditAmount >= $this->minCreditAmount) {
                // Custom credit amount
                $this->validate(['customCreditAmount' => 'required|numeric|min:' . $this->minCreditAmount]);
                $credits = $this->customCreditAmount;
                $amount = $credits * $this->pricingConfig['credit_price'];
            } else {
                throw new \InvalidArgumentException('Please select a credit package or enter a custom amount');
            }

            if ($amount < $this->pricingConfig['minimum_amount']) {
                throw new \InvalidArgumentException('Minimum amount is ₦' . number_format($this->pricingConfig['minimum_amount']));
            }

            $user = Auth::user();
            $nairaWallet = $user->getNairaWallet();
            $creditsWallet = $user->getCreditWallet();

            // Check if user has sufficient Naira balance
            if ($nairaWallet->balance < $amount) {
                session()->flash('error', 'Insufficient Naira balance. Please fund your Naira wallet first.');
                return;
            }

            DB::beginTransaction();

            try {
                // Debit Naira wallet
                $nairaWallet->debit($amount);

                // Credit Credits wallet
                $creditsWallet->credit($credits);

                // Create transaction records
                $transactionService = app(TransactionService::class);
                
                // Record Naira debit transaction
                $transactionService->debit(
                    $user->id,
                    $amount,
                    'naira',
                    Transaction::CATEGORY_CREDIT_PURCHASE,
                    'Credit purchase using Naira wallet',
                    [
                        'credits_purchased' => $credits,
                        'package_index' => $this->selectedCreditPackage,
                        'purchase_method' => 'naira_wallet',
                        'credit_price' => $this->pricingConfig['credit_price'],
                    ]
                );

                // Record Credits credit transaction
                $transactionService->credit(
                    $user->id,
                    $credits,
                    'credits',
                    Transaction::CATEGORY_CREDIT_PURCHASE,
                    'Credits purchased with Naira wallet',
                    [
                        'naira_amount' => $amount,
                        'package_index' => $this->selectedCreditPackage,
                        'purchase_method' => 'naira_wallet',
                        'credit_price' => $this->pricingConfig['credit_price'],
                    ]
                );

                DB::commit();

                session()->flash('success', 'Successfully purchased ' . number_format($credits) . ' credits using ₦' . number_format($amount, 2) . ' from your Naira wallet!');
                
                // Reset form
                $this->selectedCreditPackage = null;
                $this->customCreditAmount = null;
                $this->resetValidation();

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Naira to Credits purchase error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'amount' => $amount ?? null,
                'credits' => $credits ?? null,
            ]);

            session()->flash('error', 'Failed to purchase credits: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
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