<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ReferralService;

class Referrals extends Component
{
    public $user;
    public $referralCode;
    public $referralLink;
    public $totalReferrals = 0;
    public $totalRewards = 0;
    public $referredUsers = [];
    public $isLoadingReferrals = false;

    public function mount()
    {
        $this->user = Auth::user();
        $this->referralCode = $this->user->getReferralCode();
        $this->referralLink = $this->user->getReferralLink();
        $this->totalReferrals = $this->user->referredUsers()->count();
        $this->totalRewards = $this->user->getTotalReferralRewards();
        
        // Load initial data
        $this->loadReferredUsers();
    }

    public function loadReferredUsers()
    {
        $this->isLoadingReferrals = true;
        
        try {
            $paginatedUsers = $this->user->getReferredUsersWithRewards(10);
            
            // Extract the actual user data and calculate reward amounts
            $this->referredUsers = $paginatedUsers->map(function ($user) {
                $rewardAmount = $user->transactions->sum('amount');
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                    'reward_amount' => $rewardAmount,
                    'reward_status' => $rewardAmount > 0 ? 'Completed' : 'Pending',
                    'total_deposits' => $user->total_deposits ?? 0,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to load referred users', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);
            $this->referredUsers = []; // Ensure it's always an array
            session()->flash('error', 'Failed to load referral data.');
        } finally {
            $this->isLoadingReferrals = false;
        }
    }

    public function copyReferralLink()
    {
        // This will be handled by JavaScript in the frontend
        session()->flash('success', 'Referral link copied to clipboard!');
    }

    public function refreshReferralData()
    {
        $this->totalReferrals = $this->user->referredUsers()->count();
        $this->totalRewards = $this->user->getTotalReferralRewards();
        $this->loadReferredUsers();
        
        session()->flash('success', 'Referral data refreshed successfully!');
    }

    public function render()
    {
        return view('livewire.referrals');
    }
}