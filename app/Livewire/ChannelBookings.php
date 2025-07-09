<?php

namespace App\Livewire;

use App\Models\ChannelAdBooking;
use App\Models\Channel;
use App\Services\TransactionService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ChannelBookings extends Component
{
    use WithPagination, WithFileUploads;

    public $selectedBooking = null;
    public $showProofModal = false;
    public $proof_screenshot;
    public $proof_description = '';
    public $filter = 'all'; // all, pending, accepted, running, completed
    
    // Channel creation modal properties
    public $showCreateModal = false;
    public $name = '';
    public $niche = '';
    public $follower_count = '';
    public $whatsapp_link = '';
    public $description = '';
    public $sample_screenshot;

    protected $rules = [
        'proof_screenshot' => 'required|image|max:2048',
        'proof_description' => 'required|string|max:500',
        'name' => 'required|string|max:255',
        'niche' => 'required|string',
        'follower_count' => 'required|integer|min:1',
        'whatsapp_link' => 'required|url',
        'description' => 'nullable|string|max:1000',
        'sample_screenshot' => 'required|image|max:10240',
    ];

    public function mount()
    {
        // We'll handle the case where user has no channels in the view
        // No need to redirect, just show appropriate message
    }

    public function acceptBooking($bookingId)
    {
        try {
            $booking = ChannelAdBooking::where('id', $bookingId)
                ->whereHas('channel', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->where('status', ChannelAdBooking::STATUS_PENDING)
                ->firstOrFail();

            $booking->acceptBooking();

            // Send notification to advertiser
            app(NotificationService::class)->sendBookingAcceptedNotification($booking);

            session()->flash('success', 'Booking accepted successfully!');
        } catch (\Exception $e) {
            Log::error('Error accepting booking: ' . $e->getMessage());
            session()->flash('error', 'Failed to accept booking. Please try again.');
        }
    }

    public function rejectBooking($bookingId)
    {
        try {
            $booking = ChannelAdBooking::where('id', $bookingId)
                ->whereHas('channel', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->where('status', ChannelAdBooking::STATUS_PENDING)
                ->firstOrFail();

            $booking->rejectBooking();

            // Send notification to advertiser
            app(NotificationService::class)->sendBookingRejectedNotification($booking);

            session()->flash('success', 'Booking rejected and refund processed.');
        } catch (\Exception $e) {
            Log::error('Error rejecting booking: ' . $e->getMessage());
            session()->flash('error', 'Failed to reject booking. Please try again.');
        }
    }

    public function startBooking($bookingId)
    {
        try {
            $booking = ChannelAdBooking::where('id', $bookingId)
                ->whereHas('channel', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->where('status', ChannelAdBooking::STATUS_ACCEPTED)
                ->firstOrFail();

            $booking->startBooking();

            // Send notification to advertiser
            app(NotificationService::class)->sendBookingStartedNotification($booking);

            session()->flash('success', 'Booking started successfully!');
        } catch (\Exception $e) {
            Log::error('Error starting booking: ' . $e->getMessage());
            session()->flash('error', 'Failed to start booking. Please try again.');
        }
    }

    public function openProofModal($bookingId)
    {
        $this->selectedBooking = ChannelAdBooking::where('id', $bookingId)
            ->whereHas('channel', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('status', ChannelAdBooking::STATUS_RUNNING)
            ->firstOrFail();

        $this->showProofModal = true;
        $this->proof_description = '';
        $this->proof_screenshot = null;
    }

    public function closeProofModal()
    {
        $this->showProofModal = false;
        $this->selectedBooking = null;
        $this->proof_description = '';
        $this->proof_screenshot = null;
        $this->resetValidation();
    }

    public function submitProof()
    {
        $this->validate();

        try {
            if (!$this->selectedBooking) {
                throw new \Exception('No booking selected');
            }

            // Upload proof screenshot
            $proofPath = $this->proof_screenshot->store('booking-proofs', 'public');

            $this->selectedBooking->submitProof($proofPath, $this->proof_description);

            // Send notification to admin and advertiser
            app(NotificationService::class)->sendProofSubmittedNotification($this->selectedBooking);

            $this->closeProofModal();
            session()->flash('success', 'Proof submitted successfully! Waiting for admin approval.');
        } catch (\Exception $e) {
            Log::error('Error submitting proof: ' . $e->getMessage());
            session()->flash('error', 'Failed to submit proof. Please try again.');
        }
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->resetChannelForm();
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetChannelForm();
        $this->resetValidation();
    }

    private function resetChannelForm()
    {
        $this->name = '';
        $this->niche = '';
        $this->follower_count = '';
        $this->whatsapp_link = '';
        $this->description = '';
        $this->sample_screenshot = null;
    }

    public function createChannel()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'niche' => 'required|string',
            'follower_count' => 'required|integer|min:1',
            'whatsapp_link' => 'required|url',
            'description' => 'nullable|string|max:1000',
            'sample_screenshot' => 'required|image|max:10240',
        ]);

        try {
            // Upload sample screenshot
            $screenshotPath = $this->sample_screenshot->store('channel-screenshots', 'public');

            // Create the channel
            Channel::create([
                'user_id' => Auth::id(),
                'name' => $this->name,
                'niche' => $this->niche,
                'follower_count' => $this->follower_count,
                'whatsapp_link' => $this->whatsapp_link,
                'description' => $this->description,
                'sample_screenshot' => $screenshotPath,
                'status' => 'pending', // Default status
            ]);

            $this->closeCreateModal();
            session()->flash('success', 'Channel created successfully! It will be reviewed by admin.');
        } catch (\Exception $e) {
            Log::error('Error creating channel: ' . $e->getMessage());
            session()->flash('error', 'Failed to create channel. Please try again.');
        }
    }

    public function render()
    {
        $query = ChannelAdBooking::with(['channel', 'user'])
            ->whereHas('channel', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc');

        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        $bookings = $query->paginate(10);

        // Get summary stats
        $stats = [
            'total' => ChannelAdBooking::whereHas('channel', function ($query) {
                $query->where('user_id', Auth::id());
            })->count(),
            'pending' => ChannelAdBooking::whereHas('channel', function ($query) {
                $query->where('user_id', Auth::id());
            })->where('status', ChannelAdBooking::STATUS_PENDING)->count(),
            'running' => ChannelAdBooking::whereHas('channel', function ($query) {
                $query->where('user_id', Auth::id());
            })->where('status', ChannelAdBooking::STATUS_RUNNING)->count(),
            'completed' => ChannelAdBooking::whereHas('channel', function ($query) {
                $query->where('user_id', Auth::id());
            })->where('status', ChannelAdBooking::STATUS_COMPLETED)->count(),
        ];

        return view('livewire.channel-bookings', [
            'bookings' => $bookings,
            'stats' => $stats,
        ]);
    }
}