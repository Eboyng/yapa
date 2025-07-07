<?php

namespace App\Livewire;

use App\Services\TransactionService;
use App\Services\PaystackService;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Withdrawal extends Component
{
    public string $withdrawalMethod = 'bank_account';
    public float $amount = 0;
    public string $bankCode = '';
    public string $accountNumber = '';
    public string $accountName = '';
    public string $opayNumber = '';
    public string $palmpayNumber = '';
    public string $airtimeNumber = '';
    public string $airtimeNetwork = '';
    public bool $isProcessing = false;
    public bool $showBvnModal = false;
    public string $bvn = '';
    public array $fees = [];
    public float $netAmount = 0;
    public array $banks = [];
    public array $networks = [
        'mtn' => 'MTN',
        'glo' => 'Glo',
        'airtel' => 'Airtel',
        '9mobile' => '9Mobile'
    ];
    
    public const MINIMUM_WITHDRAWAL = 1000;
    public const BANK_TRANSFER_FEE_PERCENTAGE = 1.5;
    public const BANK_TRANSFER_FEE_FIXED = 100;
    public const OPAY_FEE_PERCENTAGE = 1.0;
    public const PALMPAY_FEE_PERCENTAGE = 1.0;
    public const AIRTIME_FEE_PERCENTAGE = 2.0;

    protected $rules = [
        'amount' => 'required|numeric|min:1000',
        'bankCode' => 'required_if:withdrawalMethod,bank_account',
        'accountNumber' => 'required_if:withdrawalMethod,bank_account|digits:10',
        'opayNumber' => 'required_if:withdrawalMethod,opay|digits:11',
        'palmpayNumber' => 'required_if:withdrawalMethod,palmpay|digits:11',
        'airtimeNumber' => 'required_if:withdrawalMethod,airtime|digits:11',
        'airtimeNetwork' => 'required_if:withdrawalMethod,airtime',
        'bvn' => 'required_if:showBvnModal,true|digits:11'
    ];

    protected $messages = [
        'amount.min' => 'Minimum withdrawal amount is ₦1,000',
        'accountNumber.digits' => 'Account number must be exactly 10 digits',
        'opayNumber.digits' => 'Opay number must be exactly 11 digits',
        'palmpayNumber.digits' => 'PalmPay number must be exactly 11 digits',
        'airtimeNumber.digits' => 'Phone number must be exactly 11 digits',
        'bvn.digits' => 'BVN must be exactly 11 digits'
    ];

    public function mount()
    {
        $this->loadBanks();
    }

    public function updatedAmount($value)
    {
        if ($value >= self::MINIMUM_WITHDRAWAL) {
            $this->calculateFees();
        } else {
            $this->fees = [];
            $this->netAmount = 0;
        }
    }

    public function updatedWithdrawalMethod()
    {
        $this->resetValidation();
        $this->calculateFees();
    }

    public function updatedAccountNumber()
    {
        if (strlen($this->accountNumber) === 10 && !empty($this->bankCode)) {
            $this->resolveAccountName();
        }
    }

    public function calculateFees()
    {
        if ($this->amount < self::MINIMUM_WITHDRAWAL) {
            return;
        }

        $fees = [];
        $totalFees = 0;

        switch ($this->withdrawalMethod) {
            case 'bank_account':
                $percentageFee = ($this->amount * self::BANK_TRANSFER_FEE_PERCENTAGE) / 100;
                $totalFees = $percentageFee + self::BANK_TRANSFER_FEE_FIXED;
                $fees = [
                    'percentage' => $percentageFee,
                    'fixed' => self::BANK_TRANSFER_FEE_FIXED,
                    'total' => $totalFees
                ];
                break;
                
            case 'opay':
                $totalFees = ($this->amount * self::OPAY_FEE_PERCENTAGE) / 100;
                $fees = [
                    'percentage' => $totalFees,
                    'total' => $totalFees
                ];
                break;
                
            case 'palmpay':
                $totalFees = ($this->amount * self::PALMPAY_FEE_PERCENTAGE) / 100;
                $fees = [
                    'percentage' => $totalFees,
                    'total' => $totalFees
                ];
                break;
                
            case 'airtime':
                $totalFees = ($this->amount * self::AIRTIME_FEE_PERCENTAGE) / 100;
                $fees = [
                    'percentage' => $totalFees,
                    'total' => $totalFees
                ];
                break;
        }

        $this->fees = $fees;
        $this->netAmount = $this->amount - $totalFees;
    }

    public function processWithdrawal()
    {
        if ($this->isProcessing) {
            return;
        }

        $this->validate();

        $user = Auth::user();
        
        // Check if user has sufficient earnings balance
        if ($user->getEarningsWallet()->balance < $this->amount) {
            session()->flash('error', 'Insufficient earnings balance.');
            return;
        }

        // Check if BVN is required for bank transfers
        if ($this->withdrawalMethod === 'bank_account' && !$user->bvn) {
            $this->showBvnModal = true;
            return;
        }

        $this->isProcessing = true;

        try {
            DB::transaction(function () use ($user) {
                $transactionService = app(TransactionService::class);
                
                // Create withdrawal transaction
                $metadata = [
                    'withdrawal_method' => $this->withdrawalMethod,
                    'fees' => $this->fees,
                    'net_amount' => $this->netAmount,
                ];

                switch ($this->withdrawalMethod) {
                    case 'bank_account':
                        $metadata['bank_code'] = $this->bankCode;
                        $metadata['account_number'] = $this->accountNumber;
                        $metadata['account_name'] = $this->accountName;
                        break;
                    case 'opay':
                        $metadata['opay_number'] = $this->opayNumber;
                        break;
                    case 'palmpay':
                        $metadata['palmpay_number'] = $this->palmpayNumber;
                        break;
                    case 'airtime':
                        $metadata['airtime_number'] = $this->airtimeNumber;
                        $metadata['airtime_network'] = $this->airtimeNetwork;
                        break;
                }

                $transaction = $transactionService->debit(
                    $user->id,
                    $this->amount,
                    'earnings',
                    Transaction::CATEGORY_WITHDRAWAL,
                    "Withdrawal request via {$this->withdrawalMethod}",
                    null,
                    'withdrawal_request',
                    $metadata
                );

                // Mark transaction as pending admin approval
                $transaction->update(['status' => Transaction::STATUS_PENDING]);

                Log::info('Withdrawal request created', [
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id,
                    'amount' => $this->amount,
                    'method' => $this->withdrawalMethod,
                    'net_amount' => $this->netAmount
                ]);

                session()->flash('success', 'Withdrawal request submitted successfully. It will be processed within 24 hours.');
                
                // Reset form
                $this->reset(['amount', 'accountNumber', 'accountName', 'opayNumber', 'palmpayNumber', 'airtimeNumber']);
                $this->fees = [];
                $this->netAmount = 0;
            });

        } catch (\Exception $e) {
            Log::error('Withdrawal request failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'amount' => $this->amount,
                'method' => $this->withdrawalMethod
            ]);

            session()->flash('error', 'Failed to process withdrawal request: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    public function saveBvnAndContinue()
    {
        $this->validate(['bvn' => 'required|digits:11']);

        try {
            $user = Auth::user();
            
            // Validate BVN with Paystack
            $paystackService = app(PaystackService::class);
            $bvnValidation = $paystackService->validateBvn($this->bvn);
            
            if (!$bvnValidation['success']) {
                session()->flash('error', 'BVN validation failed: ' . $bvnValidation['message']);
                return;
            }

            // Save encrypted BVN
            $user->update(['bvn' => $this->bvn]);
            
            $this->showBvnModal = false;
            $this->bvn = '';
            
            // Continue with withdrawal
            $this->processWithdrawal();
            
        } catch (\Exception $e) {
            Log::error('BVN validation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'BVN validation failed. Please try again.');
        }
    }

    private function loadBanks()
    {
        try {
            $paystackService = app(PaystackService::class);
            $this->banks = $paystackService->getBanks();
        } catch (\Exception $e) {
            Log::error('Failed to load banks', ['error' => $e->getMessage()]);
            $this->banks = [];
        }
    }

    private function resolveAccountName()
    {
        try {
            $paystackService = app(PaystackService::class);
            $result = $paystackService->resolveAccountNumber($this->accountNumber, $this->bankCode);
            
            if ($result['success']) {
                $this->accountName = $result['data']['account_name'];
            } else {
                $this->accountName = '';
                session()->flash('error', 'Could not resolve account name. Please verify account details.');
            }
        } catch (\Exception $e) {
            Log::error('Account resolution failed', [
                'account_number' => $this->accountNumber,
                'bank_code' => $this->bankCode,
                'error' => $e->getMessage()
            ]);
            $this->accountName = '';
        }
    }

    public function render()
    {
        return view('livewire.withdrawal', [
            'user' => Auth::user(),
            'earningsBalance' => Auth::user()->getEarningsWallet()->balance
        ]);
    }
}