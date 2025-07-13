<?php

namespace App\Livewire;

use App\Models\ChannelAd;
use App\Models\ChannelAdApplication;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class BookingModal extends Component
{
    use WithFileUploads;

    public ChannelAd $channelAd;
    public bool $showModal = false;
    
    // Booking form fields
    public $start_date = '';
    public $end_date = '';
    public $ad_content;
    public $ad_description = '';
    
    // Calculated fields
    public float $total_amount = 0;
    public int $duration_days = 0;
    public bool $isSubmitting = false;
    
    protected $rules = [
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after:start_date',
        'ad_content' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240', // 10MB max
        'ad_description' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'start_date.required' => 'Start date is required.',
        'start_date.after_or_equal' => 'Start date must be today or later.',
        'end_date.required' => 'End date is required.',
        'end_date.after' => 'End date must be after start date.',
        'ad_content.mimes' => 'Ad content must be an image or video file.',
        'ad_content.max' => 'Ad content cannot exceed 10MB.',
        'ad_description.max' => 'Description cannot exceed 1000 characters.',
    ];

    public function mount(ChannelAd $channelAd)
    {
        $this->channelAd = $channelAd;
    }

    public function updatedStartDate()
    {
        $this->calculateAmount();
    }

    public function updatedEndDate()
    {
        $this->calculateAmount();
    }

    public function calculateAmount()
    {
        if ($this->start_date && $this->end_date) {
            $startDate = \Carbon\Carbon::parse($this->start_date);
            $endDate = \Carbon\Carbon::parse($this->end_date);
            
            if ($endDate->gt($startDate)) {
                $this->duration_days = $startDate->diffInDays($endDate) + 1;
                $this->total_amount = $this->channelAd->price_per_ad * $this->duration_days;
            } else {
                $this->duration_days = 0;
                $this->total_amount = 0;
            }
        } else {
            $this->duration_days = 0;
            $this->total_amount = 0;
        }
    }

    public function openModal()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->start_date = '';
        $this->end_date = '';
        $this->ad_content = null;
        $this->ad_description = '';
        $this->total_amount = 0;
        $this->duration_days = 0;
        $this->resetValidation();
    }

    public function submitBooking()
    {
        if ($this->isSubmitting) {
            return;
        }

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->isSubmitting = true;

        try {
            $this->validate();

            // Additional validation for date overlaps
            $this->validateDateOverlaps();

            $user = Auth::user();
            
            // Check wallet balance
            $nairaWallet = $user->getNairaWallet();
            if ($nairaWallet->balance < $this->total_amount) {
                throw new \Exception("Insufficient wallet balance. Required: ₦{$this->total_amount}, Available: ₦{$nairaWallet->balance}");
            }

            DB::transaction(function () use ($user) {
                // Upload ad content if provided
                $contentPath = null;
                if ($this->ad_content) {
                    $contentPath = $this->ad_content->store('channel-ad-content', 'public');
                }

                // Create the application
                $application = ChannelAdApplication::create([
                    'channel_id' => $this->channelAd->id, // Using channel_ad as channel for marketplace
                    'channel_ad_id' => $this->channelAd->id,
                    'advertiser_id' => $user->id,
                    'booking_status' => 'pending',
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'amount' => $this->total_amount,
                    'payment_status' => 'pending',
                    'status' => 'pending',
                    'applied_at' => now(),
                    'admin_notes' => $this->ad_description,
                    'proof_screenshot' => $contentPath,
                ]);

                // Process payment through TransactionService
                $transactionService = app(TransactionService::class);
                $transactionService->bookAd($application);

                // Trigger events/notifications here
                event(new \App\Events\AdBooked($application));

                session()->flash('success', 'Ad booking submitted successfully! The channel owner will review your request.');
                $this->closeModal();
                
                // Redirect to user dashboard
                return redirect()->route('dashboard.my-ads');
            });

        } catch (\Exception $e) {
            Log::error('Channel ad booking error', [
                'user_id' => Auth::id(),
                'channel_ad_id' => $this->channelAd->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            session()->flash('error', 'Failed to book ad: ' . $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    private function validateDateOverlaps()
    {
        $overlappingBookings = ChannelAdApplication::where('channel_ad_id', $this->channelAd->id)
            ->where('booking_status', 'confirmed')
            ->where(function ($query) {
                $query->whereBetween('start_date', [$this->start_date, $this->end_date])
                      ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                      ->orWhere(function ($q) {
                          $q->where('start_date', '<=', $this->start_date)
                            ->where('end_date', '>=', $this->end_date);
                      });
            })
            ->exists();

        if ($overlappingBookings) {
            throw new \Exception('The selected dates overlap with existing confirmed bookings.');
        }
    }

    public function render()
    {
        return view('livewire.booking-modal');
    }
}