<?php

namespace App\Livewire;

use App\Services\PaystackService;
use App\Services\TransactionService;
use App\Services\AirtimeService;
use App\Services\DataService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class CreditPurchase extends Component
{
    // Processing state
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
    public string $creditPaymentMethod = 'naira'; // 'naira' or 'earnings'

    // Airtime Properties
    public string $airtimePaymentMethod = 'naira'; // 'naira' or 'earnings'
    public string $airtimePhoneNumber = '';
    public float $airtimeAmount = 0;
    public string $detectedAirtimeNetwork = '';
    public int $airtimeNetworkId = 0;

    // Data Properties
    public string $dataPaymentMethod = 'naira'; // 'naira' or 'earnings'
    public string $dataPhoneNumber = '';
    public string $selectedDataPlan = '';
    public string $detectedDataNetwork = '';
    public int $dataNetworkId = 0;
    public array $availableDataPlans = [];

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
    public string $detectedNetwork = '';
    public array $fees = [];
    public float $netAmount = 0;
    public array $banks = [];
    public array $networks = [];
    public bool $airtimeApiEnabled = false;

    // Network mapping
    private array $networkMapping = [
        'MTN' => 1,
        'GLO' => 2,
        '9MOBILE' => 3,
        'AIRTEL' => 4,
    ];

    // Data plans (CG type only)
    private array $dataPlans = [
        // MTN CG Plans
        ['plan_id' => 64, 'network_id' => 1, 'network' => 'MTN', 'size' => '50MB', 'validity' => '1 Month', 'type' => 'CG', 'price' => 45],
        ['plan_id' => 84, 'network_id' => 1, 'network' => 'MTN', 'size' => '100MB', 'validity' => '1 Month', 'type' => 'CG', 'price' => 75],
        ['plan_id' => 65, 'network_id' => 1, 'network' => 'MTN', 'size' => '150MB', 'validity' => '1 Month', 'type' => 'CG', 'price' => 110],
        ['plan_id' => 66, 'network_id' => 1, 'network' => 'MTN', 'size' => '250MB', 'validity' => '1 Month', 'type' => 'CG', 'price' => 180],
        ['plan_id' => 69, 'network_id' => 1, 'network' => 'MTN', 'size' => '1GB', 'validity' => '30 Days', 'type' => 'CG', 'price' => 320],
        ['plan_id' => 70, 'network_id' => 1, 'network' => 'MTN', 'size' => '2GB', 'validity' => '30 Days', 'type' => 'CG', 'price' => 640],
        ['plan_id' => 71, 'network_id' => 1, 'network' => 'MTN', 'size' => '3GB', 'validity' => '30 Days', 'type' => 'CG', 'price' => 960],
        
        // GLO CG Plans  
        ['plan_id' => 67, 'network_id' => 2, 'network' => 'GLO', 'size' => '200MB', 'validity' => '30 Days', 'type' => 'CG', 'price' => 140],
        ['plan_id' => 68, 'network_id' => 2, 'network' => 'GLO', 'size' => '500MB', 'validity' => '30 Days', 'type' => 'CG', 'price' => 280],
        ['plan_id' => 69, 'network_id' => 2, 'network' => 'GLO', 'size' => '1GB', 'validity' => '30 Days', 'type' => 'CG', 'price' => 320],
        ['plan_id' => 70, 'network_id' => 2, 'network' => 'GLO', 'size' => '2GB', 'validity' => '30 Days', 'type' => 'CG', 'price' => 640],
        ['plan_id' => 71, 'network_id' => 2, 'network' => 'GLO', 'size' => '3GB', 'validity' => '30 Days', 'type' => 'CG', 'price' => 960],
        
        // AIRTEL CG Plans
        ['plan_id' => 24, 'network_id' => 4, 'network' => 'AIRTEL', 'size' => '100MB', 'validity' => '1 Day', 'type' => 'CG', 'price' => 35],
        ['plan_id' => 25, 'network_id' => 4, 'network' => 'AIRTEL', 'size' => '300MB', 'validity' => '1 Day', 'type' => 'CG', 'price' => 105],
        ['plan_id' => 26, 'network_id' => 4, 'network' => 'AIRTEL', 'size' => '500MB', 'validity' => '1 Week', 'type' => 'CG', 'price' => 280],
    ];

    // Constants
    public const MINIMUM_WITHDRAWAL = 1000;
    public const BANK_TRANSFER_FEE_PERCENTAGE = 1.5;
    public const PALMPAY_FEE_PERCENTAGE = 1.0;
    public const AIRTIME_FEE_PERCENTAGE = 2.0;

    protected $rules = [
        'fundAmount' => 'required|numeric|min:300',
        'amount' => 'required|numeric|min:1000',
        'withdrawalMethod' => 'required|string|in:bank_account,palmpay,airtime',
        'accountNumber' => 'required_if:withdrawalMethod,bank_account|string|size:10',
        'bankCode' => 'required_if:withdrawalMethod,bank_account|string',
        'palmpayNumber' => 'required_if:withdrawalMethod,palmpay|string|size:11',
        'airtimeNumber' => 'required_if:withdrawalMethod,airtime|string|size:11',
        'airtimeNetwork' => 'required_if:withdrawalMethod,airtime|string',
        'airtimePhoneNumber' => 'required|string|size:10',
        'airtimeAmount' => 'required|numeric|min:50|max:5000',
        'dataPhoneNumber' => 'required|string|size:10',
        'selectedDataPlan' => 'required|string',
    ];

    protected $messages = [
        'fundAmount.min' => 'Minimum funding amount is ₦300',
        'amount.min' => 'Minimum withdrawal amount is ₦1,000',
        'accountNumber.size' => 'Account number must be exactly 10 digits',
        'palmpayNumber.size' => 'PalmPay number must be exactly 11 digits',
        'airtimeNumber.size' => 'Phone number must be exactly 11 digits',
        'airtimePhoneNumber.size' => 'Phone number must be exactly 10 digits',
        'airtimeAmount.min' => 'Minimum airtime amount is ₦50',
        'airtimeAmount.max' => 'Maximum airtime amount is ₦5,000',
        'dataPhoneNumber.size' => 'Phone number must be exactly 10 digits',
        'selectedDataPlan.required' => 'Please select a data plan',
    ];

    public function mount(?int $retry = null): void
    {
        $this->initializeComponent();

        if ($retry) {
            $this->retryTransactionId = $retry;
            $this->loadRetryTransaction();
        }
    }

    private function initializeComponent(): void
    {
        try {
            $paystackService = app(PaystackService::class);
            $this->pricingConfig = $paystackService->getPricingConfig();

            $this->loadBanks();
            $this->loadAirtimeNetworks();
            $this->initializeCreditPackages();
            $this->loadDataPlans();
        } catch (\Exception $e) {
            Log::error('Failed to initialize CreditPurchase component', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            session()->flash('error', 'Failed to load component. Please refresh the page.');
        }
    }

    // Network detection methods
    public function updatedAirtimePhoneNumber($value): void
    {
        if (strlen($value) === 10) {
            $this->detectAirtimeNetwork($value);
        } else {
            $this->resetAirtimeNetworkDetection();
        }
    }

    public function updatedDataPhoneNumber($value): void
    {
        if (strlen($value) === 10) {
            $this->detectDataNetwork($value);
            $this->loadDataPlans();
        } else {
            $this->resetDataNetworkDetection();
        }
    }

    private function detectAirtimeNetwork($phoneNumber): void
    {
        $fullNumber = '+234' . $phoneNumber;
        $network = $this->detectNetworkFromNumber($fullNumber);
        
        if ($network) {
            $this->detectedAirtimeNetwork = $network['name'];
            $this->airtimeNetworkId = $network['id'];
        } else {
            $this->resetAirtimeNetworkDetection();
        }
    }

    private function detectDataNetwork($phoneNumber): void
    {
        $fullNumber = '+234' . $phoneNumber;
        $network = $this->detectNetworkFromNumber($fullNumber);
        
        if ($network) {
            $this->detectedDataNetwork = $network['name'];
            $this->dataNetworkId = $network['id'];
        } else {
            $this->resetDataNetworkDetection();
        }
    }

    private function detectNetworkFromNumber($fullNumber): ?array
    {
        // Remove +234 prefix
        $number = substr($fullNumber, 4);
        
        // MTN prefixes
        $mtnPrefixes = ['703', '706', '803', '806', '813', '816', '903', '906', '913', '916'];
        // GLO prefixes
        $gloPrefixes = ['705', '805', '807', '811', '815', '905', '915'];
        // Airtel prefixes
        $airtelPrefixes = ['701', '708', '802', '808', '812', '901', '904', '912'];
        // 9Mobile prefixes
        $nineMobilePrefixes = ['809', '817', '818', '909', '999'];

        $prefix = substr($number, 0, 3);

        if (in_array($prefix, $mtnPrefixes)) {
            return ['name' => 'MTN', 'id' => 1];
        } elseif (in_array($prefix, $gloPrefixes)) {
            return ['name' => 'GLO', 'id' => 2];
        } elseif (in_array($prefix, $airtelPrefixes)) {
            return ['name' => 'AIRTEL', 'id' => 4];
        } elseif (in_array($prefix, $nineMobilePrefixes)) {
            return ['name' => '9MOBILE', 'id' => 3];
        }

        return null;
    }

    private function resetAirtimeNetworkDetection(): void
    {
        $this->detectedAirtimeNetwork = '';
        $this->airtimeNetworkId = 0;
    }

    private function resetDataNetworkDetection(): void
    {
        $this->detectedDataNetwork = '';
        $this->dataNetworkId = 0;
        $this->availableDataPlans = [];
    }

    private function loadDataPlans(): void
    {
        if ($this->dataNetworkId > 0) {
            $this->availableDataPlans = collect($this->dataPlans)
                ->filter(function ($plan) {
                    return $plan['network_id'] === $this->dataNetworkId;
                })
                ->values()
                ->toArray();
        } else {
            $this->availableDataPlans = [];
        }
    }

    // Purchase methods
    public function purchaseCredits(): void
    {
        if ($this->creditPaymentMethod === 'naira') {
            $this->purchaseCreditsWithNaira();
        } else {
            $this->purchaseCreditsWithEarnings();
        }
    }

    public function purchaseAirtime(): void
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            $this->validate([
                'airtimePhoneNumber' => 'required|string|size:10',
                'airtimeAmount' => 'required|numeric|min:50|max:5000',
            ]);

            if (empty($this->detectedAirtimeNetwork)) {
                throw new ValidationException(
                    validator([], []),
                    ['airtimePhoneNumber' => 'Unable to detect network. Please check phone number.']
                );
            }

            $user = Auth::user();
            $wallet = $this->airtimePaymentMethod === 'naira' ? $user->getNairaWallet() : $user->getEarningsWallet();

            if ($wallet->balance < $this->airtimeAmount) {
                throw new ValidationException(
                    validator([], []),
                    ['airtimeAmount' => 'Insufficient balance in ' . ($this->airtimePaymentMethod === 'naira' ? 'Naira' : 'Earnings') . ' wallet.']
                );
            }

            DB::beginTransaction();

            try {
                // Purchase airtime via API
                $result = $this->purchaseAirtimeViaAPI();

                if ($result['success']) {
                    // Record transaction
                    $this->recordAirtimeTransaction($user, $result['data']);

                    DB::commit();
                    session()->flash('success', 'Airtime purchase successful! Transaction ID: ' . $result['data']['transaction_id']);
                    $this->resetAirtimeForm();
                } else {
                    DB::rollBack();
                    session()->flash('error', $result['message']);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
        } catch (\Exception $e) {
            Log::error('Airtime purchase error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'amount' => $this->airtimeAmount,
                'phone' => $this->airtimePhoneNumber,
            ]);

            session()->flash('error', 'Airtime purchase failed: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    public function purchaseData(): void
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            $this->validate([
                'dataPhoneNumber' => 'required|string|size:10',
                'selectedDataPlan' => 'required|string',
            ]);

            if (empty($this->detectedDataNetwork)) {
                throw new ValidationException(
                    validator([], []),
                    ['dataPhoneNumber' => 'Unable to detect network. Please check phone number.']
                );
            }

            $plan = collect($this->availableDataPlans)->firstWhere('plan_id', $this->selectedDataPlan);
            if (!$plan) {
                throw new ValidationException(
                    validator([], []),
                    ['selectedDataPlan' => 'Invalid data plan selected.']
                );
            }

            $user = Auth::user();
            $wallet = $this->dataPaymentMethod === 'naira' ? $user->getNairaWallet() : $user->getEarningsWallet();

            if ($wallet->balance < $plan['price']) {
                throw new ValidationException(
                    validator([], []),
                    ['selectedDataPlan' => 'Insufficient balance in ' . ($this->dataPaymentMethod === 'naira' ? 'Naira' : 'Earnings') . ' wallet.']
                );
            }

            DB::beginTransaction();

            try {
                // Purchase data via API
                $result = $this->purchaseDataViaAPI($plan);

                if ($result['success']) {
                    // Record transaction
                    $this->recordDataTransaction($user, $result['data'], $plan);

                    DB::commit();
                    session()->flash('success', 'Data purchase successful! Transaction ID: ' . $result['data']['transaction_id']);
                    $this->resetDataForm();
                } else {
                    DB::rollBack();
                    session()->flash('error', $result['message']);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
        } catch (\Exception $e) {
            Log::error('Data purchase error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'plan' => $this->selectedDataPlan,
                'phone' => $this->dataPhoneNumber,
            ]);

            session()->flash('error', 'Data purchase failed: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    private function purchaseAirtimeViaAPI(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.wazobianet.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://wazobianet.com/api/airtime/', [
                'network_id' => $this->airtimeNetworkId,
                'amount' => $this->airtimeAmount,
                'airtime_type' => 'VTU',
                'phone_number' => '+234' . $this->airtimePhoneNumber,
                'ported' => false,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            } else {
                return ['success' => false, 'message' => $response->json()['message'] ?? 'Airtime purchase failed'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'API request failed: ' . $e->getMessage()];
        }
    }

    private function purchaseDataViaAPI(array $plan): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.wazobianet.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://wazobianet.com/api/data/', [
                'network_id' => $plan['network_id'],
                'plan_id' => $plan['plan_id'],
                'phone_number' => '+234' . $this->dataPhoneNumber,
                'ported' => false,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            } else {
                return ['success' => false, 'message' => $response->json()['message'] ?? 'Data purchase failed'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'API request failed: ' . $e->getMessage()];
        }
    }

    private function recordAirtimeTransaction(User $user, array $apiResponse): void
    {
        $transactionService = app(TransactionService::class);
        $walletType = $this->airtimePaymentMethod === 'naira' ? Wallet::TYPE_NAIRA : Wallet::TYPE_EARNINGS;

        $transactionService->debit(
            $user->id,
            $this->airtimeAmount,
            $walletType,
            Transaction::CATEGORY_AIRTIME_PURCHASE,
            'Airtime purchase for +234' . $this->airtimePhoneNumber,
            null,
            'airtime_api',
            [
                'phone_number' => '+234' . $this->airtimePhoneNumber,
                'network' => $this->detectedAirtimeNetwork,
                'network_id' => $this->airtimeNetworkId,
                'amount' => $this->airtimeAmount,
                'api_transaction_id' => $apiResponse['transaction_id'],
                'api_response' => $apiResponse,
                'payment_method' => $this->airtimePaymentMethod,
            ]
        );
    }

    private function recordDataTransaction(User $user, array $apiResponse, array $plan): void
    {
        $transactionService = app(TransactionService::class);
        $walletType = $this->dataPaymentMethod === 'naira' ? Wallet::TYPE_NAIRA : Wallet::TYPE_EARNINGS;

        $transactionService->debit(
            $user->id,
            $plan['price'],
            $walletType,
            Transaction::CATEGORY_DATA_PURCHASE,
            'Data purchase: ' . $plan['size'] . ' for +234' . $this->dataPhoneNumber,
            null,
            'data_api',
            [
                'phone_number' => '+234' . $this->dataPhoneNumber,
                'network' => $this->detectedDataNetwork,
                'network_id' => $this->dataNetworkId,
                'plan' => $plan,
                'api_transaction_id' => $apiResponse['transaction_id'],
                'api_response' => $apiResponse,
                'payment_method' => $this->dataPaymentMethod,
            ]
        );
    }

    private function resetAirtimeForm(): void
    {
        $this->airtimePhoneNumber = '';
        $this->airtimeAmount = 0;
        $this->resetAirtimeNetworkDetection();
        $this->resetValidation();
    }

    private function resetDataForm(): void
    {
        $this->dataPhoneNumber = '';
        $this->selectedDataPlan = '';
        $this->resetDataNetworkDetection();
        $this->resetValidation();
    }

    // Existing methods remain the same...
    public function updatedFundAmount($value): void
    {
        if ($value && $value < ($this->pricingConfig['minimum_amount'] ?? 300)) {
            $this->addError('fundAmount', 'Minimum funding amount is ₦' . number_format($this->pricingConfig['minimum_amount'] ?? 300));
        }
    }

    public function updatedCustomCreditAmount($value): void
    {
        if ($value && $value >= $this->minCreditAmount) {
            $this->selectedCreditPackage = null;
            $this->resetValidation(['customCreditAmount']);
        }
    }

    public function selectCreditPackage($index): void
    {
        if (!isset($this->creditPackages[$index])) {
            return;
        }

        $this->selectedCreditPackage = $index;
        $this->customCreditAmount = null;
        $this->resetValidation();
    }

    private function initializeCreditPackages(): void
    {
        $creditPrice = $this->pricingConfig['credit_price'] ?? 1;

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
    public function openFundModal(): void
    {
        $this->showFundModal = true;
        $this->fundAmount = 0;
        $this->resetValidation();
    }

    public function closeFundModal(): void
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

            $minimumAmount = $this->pricingConfig['minimum_amount'] ?? 300;
            if ($this->fundAmount < $minimumAmount) {
                throw new ValidationException(
                    validator([], []),
                    ['fundAmount' => 'Minimum funding amount is ₦' . number_format($minimumAmount)]
                );
            }

            $paystackService = app(PaystackService::class);
            $user = Auth::user();

            $result = $paystackService->initializePayment(
                $user->id,
                $this->fundAmount,
                $user->email,
                route('paystack.callback'),
                [
                    'source' => 'fund_wallet_modal',
                    'user_agent' => request()->userAgent(),
                    'ip_address' => request()->ip(),
                ],
                Wallet::TYPE_NAIRA,
                Transaction::CATEGORY_NAIRA_FUNDING
            );

            if ($result['success']) {
                return redirect()->away($result['authorization_url']);
            } else {
                session()->flash('error', $result['message'] ?? 'Failed to initialize payment');
            }
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->errors());
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

    public function purchaseCreditsWithNaira()
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            [$amount, $credits] = $this->calculatePurchaseAmount();

            $user = Auth::user();

            DB::beginTransaction();

            try {
                $this->createPurchaseTransactions($user, $amount, $credits, 'naira');

                DB::commit();

                session()->flash('success', 'Successfully purchased ' . number_format($credits) . ' credits using ₦' . number_format($amount, 2) . ' from your Naira wallet!');

                $this->selectedCreditPackage = null;
                $this->customCreditAmount = null;
                $this->resetValidation();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\App\Exceptions\InsufficientBalanceException $e) {
            session()->flash('error', 'Insufficient Naira balance. Please fund your Naira wallet first.');
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

    public function purchaseCreditsWithEarnings()
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            [$amount, $credits] = $this->calculatePurchaseAmount();

            $user = Auth::user();

            DB::beginTransaction();

            try {
                $this->createPurchaseTransactions($user, $amount, $credits, 'earnings');

                DB::commit();

                session()->flash('success', 'Successfully purchased ' . number_format($credits) . ' credits using ₦' . number_format($amount, 2) . ' from your earnings!');

                $this->selectedCreditPackage = null;
                $this->customCreditAmount = null;
                $this->resetValidation();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\App\Exceptions\InsufficientBalanceException $e) {
            session()->flash('error', 'Insufficient earnings balance.');
        } catch (\Exception $e) {
            Log::error('Earnings to Credits purchase error', [
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

    private function calculatePurchaseAmount(): array
    {
        if ($this->selectedCreditPackage !== null && isset($this->creditPackages[$this->selectedCreditPackage])) {
            $package = $this->creditPackages[$this->selectedCreditPackage];
            return [$package['amount'], $package['total_credits']];
        }

        if ($this->customCreditAmount && $this->customCreditAmount >= $this->minCreditAmount) {
            $this->validate(['customCreditAmount' => 'required|numeric|min:' . $this->minCreditAmount]);
            $credits = $this->customCreditAmount;
            $amount = $credits * ($this->pricingConfig['credit_price'] ?? 1);
            return [$amount, $credits];
        }

        throw new \InvalidArgumentException('Please select a credit package or enter a custom amount');
    }

    private function createPurchaseTransactions($user, $amount, $credits, $paymentMethod): void
    {
        $transactionService = app(TransactionService::class);
        $walletType = $paymentMethod === 'naira' ? Wallet::TYPE_NAIRA : Wallet::TYPE_EARNINGS;

        // Record debit transaction
        $transactionService->debit(
            $user->id,
            $amount,
            $walletType,
            Transaction::CATEGORY_CREDIT_PURCHASE,
            'Credit purchase using ' . ($paymentMethod === 'naira' ? 'Naira' : 'Earnings') . ' wallet',
            null,
            $paymentMethod . '_wallet',
            [
                'credits_purchased' => $credits,
                'package_index' => $this->selectedCreditPackage,
                'purchase_method' => $paymentMethod . '_wallet',
                'credit_price' => $this->pricingConfig['credit_price'] ?? 1,
            ]
        );

        // Record Credits credit transaction
        $transactionService->credit(
            $user->id,
            $credits,
            'credits',
            Transaction::CATEGORY_CREDIT_PURCHASE,
            'Credits purchased with ' . ($paymentMethod === 'naira' ? 'Naira' : 'Earnings') . ' wallet',
            null,
            $paymentMethod . '_wallet',
            [
                'naira_amount' => $amount,
                'package_index' => $this->selectedCreditPackage,
                'purchase_method' => $paymentMethod . '_wallet',
                'credit_price' => $this->pricingConfig['credit_price'] ?? 1,
            ]
        );
    }

    private function loadRetryTransaction(): void
    {
        try {
            $transaction = Transaction::where('id', $this->retryTransactionId)
                ->where('user_id', Auth::id())
                ->where('status', Transaction::STATUS_FAILED)
                ->first();

            if ($transaction && isset($transaction->metadata['credits'])) {
                $this->customCreditAmount = $transaction->metadata['credits'];
                $this->selectedCreditPackage = null;

                session()->flash('info', 'Retrying failed transaction: ' . $transaction->reference);
            } else {
                $this->retryTransactionId = null;
                session()->flash('error', 'Transaction not found or cannot be retried.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to load retry transaction', [
                'transaction_id' => $this->retryTransactionId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            $this->retryTransactionId = null;
            session()->flash('error', 'Failed to load transaction details.');
        }
    }

    // Withdrawal Modal Methods
    public function openWithdrawModal(): void
    {
        $this->showWithdrawModal = true;
        $this->resetWithdrawalForm();
    }

    public function closeWithdrawModal(): void
    {
        $this->showWithdrawModal = false;
        $this->resetWithdrawalForm();
    }

    private function resetWithdrawalForm(): void
    {
        $this->withdrawalMethod = '';
        $this->amount = 0;
        $this->accountNumber = '';
        $this->accountName = '';
        $this->bankCode = '';
        $this->palmpayNumber = '';
        $this->airtimeNumber = '';
        $this->airtimeNetwork = '';
        $this->detectedNetwork = '';
        $this->fees = [];
        $this->netAmount = 0;
        $this->resetValidation();
    }

    // Withdrawal Amount Updates
    public function updatedAmount($value): void
    {
        $this->calculateFees();
    }

    public function updatedWithdrawalMethod(): void
    {
        $this->calculateFees();
        $this->resetValidation();
    }

    public function updatedAccountNumber(): void
    {
        if (strlen($this->accountNumber) === 10 && !empty($this->bankCode)) {
            $this->resolveAccountName();
        }
    }

    public function updatedAirtimeNumber(): void
    {
        if (strlen($this->airtimeNumber) === 11) {
            $this->detectNetworkFromNumber();
        } else {
            $this->resetNetworkDetection();
        }
    }

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
                $result = $this->processWithdrawalByMethod($transactionService, $user);

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

    private function processWithdrawalByMethod($transactionService, $user)
    {
        return match ($this->withdrawalMethod) {
            'bank_account' => $this->processBankWithdrawal($transactionService, $user),
            'palmpay' => $this->processPalmpayWithdrawal($transactionService, $user),
            'airtime' => $this->processAirtimeWithdrawal($transactionService, $user),
            default => ['success' => false, 'message' => 'Invalid withdrawal method']
        };
    }

    private function processBankWithdrawal($transactionService, $user): array
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

    private function processPalmpayWithdrawal($transactionService, $user): array
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

    private function processAirtimeWithdrawal($transactionService, $user): array
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

    private function calculateFees(): void
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

    private function loadBanks(): void
    {
        try {
            $paystack = app(PaystackService::class);
            $this->banks = $paystack->getBanks();
        } catch (\Exception $e) {
            Log::error('Failed to load banks', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            $this->banks = [];
        }
    }

    private function loadAirtimeNetworks(): void
    {
        try {
            $airtimeService = app(AirtimeService::class);
            $this->airtimeApiEnabled = $airtimeService->isEnabled();

            if ($this->airtimeApiEnabled) {
                $enabledNetworks = $airtimeService->getEnabledNetworks();
                $this->networks = collect($enabledNetworks)
                    ->pluck('name', 'network_id')
                    ->toArray();
            }
        } catch (\Exception $e) {
            Log::error('Failed to load airtime networks', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            $this->networks = [];
            $this->airtimeApiEnabled = false;
        }
    }

   

    private function resetNetworkDetection(): void
    {
        $this->airtimeNetwork = '';
        $this->airtimeNetworkId = 0;
        $this->detectedNetwork = '';
    }

    private function resolveAccountName(): void
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
            Log::error('Account resolution failed', [
                'error' => $e->getMessage(),
                'account_number' => $this->accountNumber,
                'bank_code' => $this->bankCode,
                'user_id' => Auth::id(),
            ]);
            $this->accountName = '';
        }
    }

    public function render()
    {
        return view('livewire.credit-purchase', [
            'user' => Auth::user(),
            'paystackPublicKey' => app(PaystackService::class)->getPublicKey(),
            'nairaFundingCategory' => Transaction::CATEGORY_NAIRA_FUNDING,
        ]);
    }
}