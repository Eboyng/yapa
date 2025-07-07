<?php

namespace App\Livewire;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;

class TransactionHistory extends Component
{
    use WithPagination;

    public string $categoryFilter = '';
    public string $typeFilter = '';
    public string $statusFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $search = '';
    public int $perPage = 15;

    protected $queryString = [
        'categoryFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        // Set default date range to last 30 days
        if (empty($this->dateFrom)) {
            $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        }
        if (empty($this->dateTo)) {
            $this->dateTo = Carbon::now()->format('Y-m-d');
        }
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->categoryFilter = '';
        $this->typeFilter = '';
        $this->statusFilter = '';
        $this->search = '';
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->resetPage();
    }

    public function render()
    {
        $query = Transaction::where('user_id', auth()->id())
            ->with(['parentTransaction', 'childTransactions'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($this->categoryFilter)) {
            $query->where('category', $this->categoryFilter);
        }

        if (!empty($this->typeFilter)) {
            $query->where('type', $this->typeFilter);
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('reference', 'like', '%' . $this->search . '%')
                  ->orWhere('source', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if (!empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $transactions = $query->paginate($this->perPage);

        // Get summary statistics
        $summaryQuery = Transaction::where('user_id', auth()->id());
        
        if (!empty($this->dateFrom)) {
            $summaryQuery->whereDate('created_at', '>=', $this->dateFrom);
        }
        if (!empty($this->dateTo)) {
            $summaryQuery->whereDate('created_at', '<=', $this->dateTo);
        }

        $summary = [
            'total_credits' => $summaryQuery->clone()->where('type', 'credit')->where('status', 'confirmed')->sum('amount'),
            'total_naira' => $summaryQuery->clone()->where('type', 'naira')->where('status', 'confirmed')->sum('amount'),
            'total_earnings' => $summaryQuery->clone()->where('type', 'earnings')->where('status', 'confirmed')->sum('amount'),
            'pending_transactions' => $summaryQuery->clone()->where('status', 'pending')->count(),
            'failed_transactions' => $summaryQuery->clone()->where('status', 'failed')->count(),
        ];

        return view('livewire.transaction-history', [
            'transactions' => $transactions,
            'summary' => $summary,
            'categories' => $this->getCategories(),
            'types' => $this->getTypes(),
            'statuses' => $this->getStatuses(),
        ]);
    }

    private function getCategories(): array
    {
        return [
            Transaction::CATEGORY_CREDIT_PURCHASE => 'Credit Purchase',
            Transaction::CATEGORY_BATCH_JOIN => 'Batch Join',
            Transaction::CATEGORY_AD_EARNING => 'Ad Earning',
            Transaction::CATEGORY_WITHDRAWAL => 'Withdrawal',
            Transaction::CATEGORY_REFUND => 'Refund',
            Transaction::CATEGORY_WHATSAPP_CHANGE => 'WhatsApp Change',
            Transaction::CATEGORY_MANUAL_ADJUSTMENT => 'Manual Adjustment',
        ];
    }

    private function getTypes(): array
    {
        return [
            Transaction::TYPE_CREDIT => 'Credits',
            Transaction::TYPE_NAIRA => 'Naira',
            Transaction::TYPE_EARNINGS => 'Earnings',
        ];
    }

    private function getStatuses(): array
    {
        return [
            Transaction::STATUS_PENDING => 'Pending',
            Transaction::STATUS_CONFIRMED => 'Confirmed',
            Transaction::STATUS_FAILED => 'Failed',
            Transaction::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function retryTransaction($transactionId)
    {
        $transaction = Transaction::where('id', $transactionId)
            ->where('user_id', auth()->id())
            ->where('status', Transaction::STATUS_FAILED)
            ->first();

        if (!$transaction) {
            session()->flash('error', 'Transaction not found or cannot be retried.');
            return;
        }

        if (!$transaction->canRetry()) {
            session()->flash('error', 'Transaction has exceeded maximum retry attempts.');
            return;
        }

        try {
            // Redirect to payment page for credit purchases
            if ($transaction->category === Transaction::CATEGORY_CREDIT_PURCHASE) {
                return redirect()->route('credits.purchase', [
                    'retry' => $transaction->id
                ]);
            }

            session()->flash('info', 'Retry functionality for this transaction type is not yet available.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to retry transaction: ' . $e->getMessage());
        }
    }

    public function exportTransactions()
    {
        // This would typically generate a CSV or PDF export
        // For now, we'll just show a message
        session()->flash('info', 'Export functionality will be available soon.');
    }
}