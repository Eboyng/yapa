<?php

namespace App\Livewire;

use App\Models\ChannelAd;
use App\Models\ChannelAdBooking;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Services\PaystackService;
use App\Services\NotificationService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChannelShow extends Component
{
    use WithFileUploads;

    public ChannelAd $channelAd;
    public bool $showBookingModal = false;
    
    // Booking form fields
    public string $title = '';
    public string $description = '';
    public string $url = '';
    public $images = [];
    public int $duration_hours = 24;
    public string $payment_method = 'wallet';
    
    // Calculated fields
    public float $total_amount = 0;
    public bool $isSubmitting = false;
    
    protected $rules = [
        'duration_hours' => 'required|integer|min:24|max:720', // 24 hours to 30 days
        'title' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:1000',
        'url' => 'nullable|url|max:255',
        'images.*' => 'nullable|image|max:2048', // 2MB max per image
        'payment_method' => 'required|in:wallet,paystack',
    ];

    protected $messages = [
        'duration_hours.required' => 'Duration is required.',
        'duration_hours.min' => 'Minimum duration is 24 hours.',
        'duration_hours.max' => 'Maximum duration is 720 hours (30 days).',
        'title.max' => 'Title cannot exceed 255 characters.',
        'description.max' => 'Description cannot exceed 1000 characters.',
        'url.url' => 'Please enter a valid URL.',
        'images.*.image' => 'All files must be images.',
        'images.*.max' => 'Each image cannot exceed 2MB.',
        'payment_method.required' => 'Please select a payment method.',
        'payment_method.in' => 'Invalid payment method selected.',
    ];

    public function mount(ChannelAd $channelAd)
    {
        if (!$channelAd->isActive()) {
            abort(404, 'Channel ad not found or not available for booking.');
        }

        $this->channelAd = $channelAd;
        $this->calculateAmount();
    }

    public function updatedDurationHours()
    {
        $this->calculateAmount();
    }

    public function calculateAmount()
    {
        if ($this->channelAd->payment_per_channel && $this->duration_hours >= 24) {
            // Calculate based on payment per channel and duration
            $this->total_amount = ($this->channelAd->payment_per_channel / 24) * $this->duration_hours;
        } else {
            $this->total_amount = 0;
        }
    }

    public function openBookingModal()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->showBookingModal = true;
    }

    public function closeBookingModal()
    {
        $this->showBookingModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->url = '';
        $this->images = [];
        $this->duration_hours = 24;
        $this->payment_method = 'wallet';
        $this->calculateAmount();
    }

    public function bookAd()
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

            $user = Auth::user();
            
            // Check if user has sufficient wallet balance for wallet payment
            if ($this->payment_method === 'wallet' && $user->wallet_balance < $this->total_amount) {
                // Auto-switch to Paystack if insufficient wallet balance
                $this->payment_method = 'paystack';
            }

            DB::transaction(function () use ($user) {
                // Upload images if any
                $uploadedImages = [];
                if (!empty($this->images)) {
                    foreach ($this->images as $image) {
                        $path = $image->store('channel-ad-bookings', 'public');
                        $uploadedImages[] = $path;
                    }
                }

                // Create the booking
                $booking = ChannelAdBooking::create([
                    'user_id' => $user->id,
                    'channel_ad_id' => $this->channelAd->id,
                    'title' => $this->title ?: null,
                    'description' => $this->description ?: null,
                    'url' => $this->url ?: null,
                    'images' => $uploadedImages,
                    'duration_hours' => $this->duration_hours,
                    'total_amount' => $this->total_amount,
                    'payment_method' => $this->payment_method,
                    'payment_reference' => Str::uuid(),
                ]);

                // Handle payment and escrow
                $transactionService = app(TransactionService::class);
                
                if ($this->payment_method === 'wallet') {
                    // Deduct from wallet and create escrow
                    $escrowTransaction = $transactionService->createChannelAdBookingEscrow(
                        $user,
                        $this->total_amount,
                        $booking->id,
                        "Escrow for channel ad booking: {$this->channelAd->title}"
                    );
                    
                    $booking->update([
                        'escrow_transaction_id' => $escrowTransaction->id,
                        'escrow_status' => ChannelAdBooking::ESCROW_STATUS_HELD,
                    ]);
                } else {
                    // For Paystack, we'll handle escrow after successful payment
                    // This will be handled in the payment callback
                }

                // Send notification to admin (channel ad creator)
                $notificationService = app(NotificationService::class);
                $notificationService->sendChannelAdBookingNotification(
                    $this->channelAd->adminUser,
                    $booking
                );

                // Send confirmation to advertiser
                $notificationService->sendChannelAdBookingConfirmation(
                    $user,
                    $booking
                );

                if ($this->payment_method === 'paystack') {
                    // Redirect to Paystack payment
                    $this->redirectToPaystack($booking);
                } else {
                    // Wallet payment successful
                    session()->flash('success', 'Ad booking successful! The channel owner will review your request within 48 hours.');
                    $this->closeBookingModal();
                }
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

    private function redirectToPaystack(ChannelAdBooking $booking)
    {
        try {
            $paystackService = app(PaystackService::class);
            
            $result = $paystackService->initializeChannelAdBookingPayment(
                $booking->user_id,
                $this->total_amount,
                $booking->user->email,
                route('paystack.callback'),
                [
                    'booking_id' => $booking->id,
                    'channel_ad_id' => $this->channelAd->id,
                    'source' => 'channel_ad_booking',
                ]
            );

            if ($result['success']) {
                $booking->update([
                    'payment_reference' => $result['reference'],
                ]);
                
                return redirect()->away($result['authorization_url']);
            } else {
                throw new \Exception($result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Paystack initialization error for channel booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            
            // Delete the booking if payment initialization failed
            $booking->delete();
            
            session()->flash('error', 'Payment initialization failed: ' . $e->getMessage());
            $this->closeBookingModal();
        }
    }

    public function render()
    {
        return view('livewire.channel-show', [
            'userWalletBalance' => Auth::check() ? Auth::user()->wallet_balance : 0,
        ])->layout('layouts.app');
    }
}