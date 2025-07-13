<?php

namespace App\Livewire;

use App\Models\ChannelAdApplication;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class UserDashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $paymentStatusFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Modal state
    public $showCancelModal = false;
    public $applicationToCancel = null;
    public $cancelReason = '';
    public bool $isProcessing = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'paymentStatusFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $rules = [
        'cancelReason' => 'required|string|min:10|max:500',
    ];

    protected $messages = [
        'cancelReason.required' => 'Please provide a reason for cancellation.',
        'cancelReason.min' => 'Cancellation reason must be at least 10 characters.',
        'cancelReason.max' => 'Cancellation reason cannot exceed 500 characters.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPaymentStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function openCancelModal($applicationId)
    {
        $application = ChannelAdApplication::where('id', $applicationId)
            ->where('advertiser_id', Auth::id())
            ->first();

        if (!$application) {
            session()->flash('error', 'Application not found.');
            return;
        }

        if (!$this->canCancelApplication($application)) {
            session()->flash('error', 'This application cannot be cancelled.');
            return;
        }

        $this->applicationToCancel = $application;
        $this->showCancelModal = true;
        $this->cancelReason = '';
        $this->resetValidation();
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->applicationToCancel = null;
        $this->cancelReason = '';
        $this->resetValidation();
    }

    public function confirmCancel()
    {
        if ($this->isProcessing) {
            return;
        }

        $this->validate();

        if (!$this->applicationToCancel || !$this->canCancelApplication($this->applicationToCancel)) {
            session()->flash('error', 'This application cannot be cancelled.');
            $this->closeCancelModal();
            return;
        }

        $this->isProcessing = true;

        try {
            DB::transaction(function () {
                // Update application with cancellation details
                $this->applicationToCancel->update([
                    'booking_status' => 'canceled',
                    'rejection_reason' => $this->cancelReason,
                    'rejected_at' => now(),
                ]);

                // Process refund through TransactionService
                $transactionService = app(TransactionService::class);
                $transactionService->refundAd($this->applicationToCancel);

                // Trigger events/notifications
                event(new \App\Events\AdCancelled($this->applicationToCancel));

                session()->flash('success', 'Application cancelled successfully. Your refund has been processed.');
            });

        } catch (\Exception $e) {
            Log::error('Application cancellation error', [
                'user_id' => Auth::id(),
                'application_id' => $this->applicationToCancel->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            session()->flash('error', 'Failed to cancel application: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
            $this->closeCancelModal();
        }
    }

    private function canCancelApplication(ChannelAdApplication $application): bool
    {
        // Can only cancel if status is pending or confirmed and hasn't started yet
        return in_array($application->booking_status, ['pending', 'confirmed']) &&
               $application->start_date > now()->toDateString() &&
               in_array($application->payment_status, ['pending', 'held']);
    }

    public function getApplicationsProperty()
    {
        $query = ChannelAdApplication::with(['channelAd', 'escrowTransaction'])
            ->where('advertiser_id', Auth::id());

        // Apply search filter
        if ($this->search) {
            $query->whereHas('channelAd', function ($q) {
                $q->where('channel_name', 'like', '%' . $this->search . '%')
                  ->orWhere('title', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('booking_status', $this->statusFilter);
        }

        // Apply payment status filter
        if ($this->paymentStatusFilter !== 'all') {
            $query->where('payment_status', $this->paymentStatusFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'canceled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPaymentStatusBadgeClass($status)
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'held' => 'bg-blue-100 text-blue-800',
            'released' => 'bg-green-100 text-green-800',
            'refunded' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function render()
    {
        return view('livewire.user-dashboard', [
            'applications' => $this->applications,
        ])->layout('layouts.app');
    }
}