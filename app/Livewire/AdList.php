<?php

namespace App\Livewire;

use App\Models\Ad;
use App\Models\AdTask;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdList extends Component
{
    public bool $showGuideModal = false;
    public ?Ad $selectedAd = null;
    public bool $isProcessing = false;
    public int $page = 1;
    public int $perPage = 10;
    public bool $hasMorePages = true;

    protected $listeners = ['refreshAdList' => '$refresh'];

    public function mount()
    {
        // Check if user is flagged for ads
        $user = Auth::user();
        if ($user->isFlaggedForAds()) {
            session()->flash('warning', 'Your account has been flagged due to multiple ad task rejections. You cannot participate in ads at this time.');
        }
    }

    public function openGuideModal()
    {
        $this->showGuideModal = true;
    }

    public function closeGuideModal()
    {
        $this->showGuideModal = false;
    }

    public function showGuide()
    {
        $this->showGuideModal = true;
    }

    public function closeGuide()
    {
        $this->showGuideModal = false;
    }

    public function startAdTask($adId)
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            $user = Auth::user();
            $ad = Ad::findOrFail($adId);

            // Check if user can participate
            if (!$ad->canUserParticipate($user)) {
                $message = 'You cannot participate in this ad. ';
                
                if (!$ad->isActive()) {
                    $message .= 'The ad is not active.';
                } elseif ($ad->hasReachedMaxParticipants()) {
                    $message .= 'The ad has reached maximum participants.';
                } elseif ($user->isFlaggedForAds()) {
                    $message .= 'Your account has been flagged.';
                } elseif ($ad->adTasks()->where('user_id', $user->id)->exists()) {
                    $message .= 'You have already participated in this ad.';
                }
                
                session()->flash('error', $message);
                return;
            }

            // Create ad task
            $adTask = AdTask::create([
                'ad_id' => $ad->id,
                'user_id' => $user->id,
                'status' => AdTask::STATUS_ACTIVE,
                'started_at' => now(),
            ]);

            Log::info('Ad task started', [
                'user_id' => $user->id,
                'ad_id' => $ad->id,
                'ad_task_id' => $adTask->id
            ]);

            session()->flash('success', 'Ad task started successfully! You have 24 hours to complete it.');
            
            // Redirect to ad task page
            return redirect()->route('ads.task', $adTask);

        } catch (\Exception $e) {
            Log::error('Failed to start ad task', [
                'user_id' => Auth::id(),
                'ad_id' => $adId,
                'error' => $e->getMessage()
            ]);

            session()->flash('error', 'Failed to start ad task. Please try again.');
        } finally {
            $this->isProcessing = false;
        }
    }

    public function loadMore()
    {
        if ($this->hasMorePages) {
            $this->page++;
        }
    }

    public function resetPagination()
    {
        $this->page = 1;
        $this->hasMorePages = true;
    }

    public function render()
    {
        $user = Auth::user();
        $isFlagged = $user->isFlaggedForAds();
        
        // Get all ads up to current page
        $allAds = collect();
        for ($currentPage = 1; $currentPage <= $this->page; $currentPage++) {
            $pageAds = Ad::available()
                ->where('status', '!=', Ad::STATUS_EXPIRED)
                ->where(function ($q) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
                })
                ->whereDoesntHave('adTasks', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['adTasks' => function($query) {
                    $query->select('ad_id', 'id');
                }])
                ->orderBy('created_at', 'desc')
                ->skip(($currentPage - 1) * $this->perPage)
                ->take($this->perPage)
                ->get();
            
            $allAds = $allAds->concat($pageAds);
        }
        
        // Check if there are more pages
        $nextPageAds = Ad::available()
            ->where('status', '!=', Ad::STATUS_EXPIRED)
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->whereDoesntHave('adTasks', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->skip($this->page * $this->perPage)
            ->take(1)
            ->exists();
        
        $this->hasMorePages = $nextPageAds;
        
        $ads = $allAds;

        // Get ad settings for earnings display
        $adSettings = [
            'share_per_view_rate' => 0.30 // Default rate from AdService
        ];

        return view('livewire.ad-list', [
            'ads' => $ads,
            'isFlagged' => $isFlagged,
            'adSettings' => $adSettings,
        ]);
    }
}