<?php

namespace App\Livewire;

use App\Models\ChannelAdApplication;
use App\Models\ChannelAd;
use App\Services\TransactionService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class ChannelAdminDashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $paymentStatusFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Modal state
    public $showRejectModal = false;
    public $applicationToReject = null;
    public $rejectionReason = '';
    public bool $isProcessing = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'paymentStatusFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $rules = [
        'rejectionReason' => 'required|string|min:10|max:500',
    ];

    protected $messages = [
        'rejectionReason.required' => 'Please provide a reason for rejection.',
        'rejectionReason.min' => 'Rejection reason must be at least 10 characters.',
        'rejectionReason.max' => 'Rejection reason cannot exceed 500 characters.',
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

    public function approveApplication($applicationId)
    {
        if ($this->isProcessing) {
            return;
        }

        $application = ChannelAdApplication::with(['channelAd', 'advertiser'])
            ->whereHas('channelAd', function ($query) {
                $query->where('owner_id', Auth::id());
            })
            ->where('id', $applicationId)
            ->where('booking_status', 'pending')
            ->first();

        if (!$application) {
            session()->flash('error', 'Application not found or cannot be approved.');
            return;
        }

        $this->isProcessing = true;

        try {
            DB::transaction(function () use ($application) {
                // Update application status
                $application->update([
                    'booking_status' => 'confirmed',
                    'approved_at' => now(),
                ]);

                // Release funds through TransactionService
                $transactionService = app(TransactionService::class);
                $transactionService->releaseAdFunds($application);

                // Send notifications
                $notificationService = app(NotificationService::class);
                $notificationService->sendAdApprovedNotification($application);

                session()->flash('success', 'Application approved successfully! Funds have been released.');
            });

        } catch (\Exception $e) {
            Log::error('Application approval error', [
                'user_id' => Auth::id(),
                'application_id' => $application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            session()->flash('error', 'Failed to approve application: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    public function openRejectModal($applicationId)
    {
        $application = ChannelAdApplication::with(['channelAd', 'advertiser'])
            ->whereHas('channelAd', function ($query) {
                $query->where('owner_id', Auth::id());
            })
            ->where('id', $applicationId)
            ->where('booking_status', 'pending')
            ->first();

        if (!$application) {
            session()->flash('error', 'Application not found.');
            return;
        }

        $this->applicationToReject = $application;
        $this->showRejectModal = true;
        $this->rejectionReason = '';
        $this->resetValidation();
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->applicationToReject = null;
        $this->rejectionReason = '';
        $this->resetValidation();
    }

    public function confirmReject()
    {
        if ($this->isProcessing) {
            return;
        }

        $this->validate();

        if (!$this->applicationToReject) {
            session()->flash('error', 'No application selected for rejection.');
            $this->closeRejectModal();
            return;
        }

        $this->isProcessing = true;

        try {
            DB::transaction(function () {
                // Update application with rejection details
                $this->applicationToReject->update([
                    'booking_status' => 'canceled',
                    'rejection_reason' => $this->rejectionReason,
                    'rejected_at' => now(),
                ]);

                // Process refund through TransactionService
                $transactionService = app(TransactionService::class);
                $transactionService->refundAd($this->applicationToReject);

                // Send notifications
                $notificationService = app(NotificationService::class);
                $notificationService->sendAdRejectedNotification($this->applicationToReject);

                session()->flash('success', 'Application rejected successfully. Refund has been processed.');
            });

        } catch (\Exception $e) {
            Log::error('Application rejection error', [
                'user_id' => Auth::id(),
                'application_id' => $this->applicationToReject->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            session()->flash('error', 'Failed to reject application: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
            $this->closeRejectModal();
        }
    }

    public function getApplicationsProperty()
    {
        $query = ChannelAdApplication::with(['channelAd', 'advertiser', 'escrowTransaction'])
            ->whereHas('channelAd', function ($q) {
                $q->where('owner_id', Auth::id());
            });

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('advertiser', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('channelAd', function ($query) {
                    $query->where('channel_name', 'like', '%' . $this->search . '%');
                });
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

    public function getStatsProperty()
    {
        $baseQuery = ChannelAdApplication::whereHas('channelAd', function ($q) {
            $q->where('owner_id', Auth::id());
        });

        return [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('booking_status', 'pending')->count(),
            'confirmed' => (clone $baseQuery)->where('booking_status', 'confirmed')->count(),
            'completed' => (clone $baseQuery)->where('booking_status', 'completed')->count(),
            'total_revenue' => (clone $baseQuery)
                ->where('payment_status', 'released')
                ->sum('amount'),
        ];
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
        return view('livewire.channel-admin-dashboard', [
            'applications' => $this->applications,
            'stats' => $this->stats,
        ])->layout('layouts.app');
    }
}