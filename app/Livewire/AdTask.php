<?php

namespace App\Livewire;

use App\Models\Ad;
use App\Models\Transaction;
use App\Services\NotificationService;
use App\Services\SettingService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdTask extends Component
{
    use WithFileUploads;

    public \App\Models\AdTask $adTask;
    public Ad $ad;
    public $screenshot = null;
    public int $viewCount = 0;
    public bool $isProcessing = false;
    public bool $showCopySuccess = false;
    public string $copyContent = '';
    public bool $canUploadScreenshot = false;
    public ?Carbon $timeRemaining = null;
    public int $hoursRemaining = 0;

    protected $rules = [
        'screenshot' => 'required|image|mimes:jpg,jpeg,png|max:2048', // 2MB max
        'viewCount' => 'required|integer|min:1|max:999999',
    ];

    protected $messages = [
        'screenshot.required' => 'Please upload a screenshot of your WhatsApp Status.',
        'screenshot.image' => 'The file must be an image.',
        'screenshot.mimes' => 'Only JPG, JPEG, and PNG files are allowed.',
        'screenshot.max' => 'The image size must not exceed 2MB.',
        'viewCount.required' => 'Please enter the view count.',
        'viewCount.integer' => 'View count must be a number.',
        'viewCount.min' => 'View count must be at least 1.',
        'viewCount.max' => 'View count seems too high. Please verify.',
    ];

    public function mount(\App\Models\AdTask $adTask)
    {
        // Ensure the task belongs to the authenticated user
        if ($adTask->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this task');
        }
        
        $this->adTask = $adTask->load('ad', 'user');
        
        if (!$this->adTask->ad) {
            abort(404, 'Ad not found');
        }
        
        $this->ad = $this->adTask->ad;
        $this->copyContent = $this->ad->copy_content ?? '';
        $this->updateTaskStatus();
    }

    public function updateTaskStatus()
    {
        // Check if task is expired
        if ($this->adTask->isExpired() && $this->adTask->status === \App\Models\AdTask::STATUS_ACTIVE) {
            $this->adTask->markAsExpired();
            $this->adTask->refresh();
        }

        // Update component state
        $this->canUploadScreenshot = $this->adTask->canUploadScreenshot();
        $this->timeRemaining = $this->adTask->time_remaining;
        $this->hoursRemaining = $this->adTask->hours_remaining;
    }

    public function copyToClipboard()
    {
        $this->showCopySuccess = true;
        $this->dispatch('copy-to-clipboard', $this->copyContent);
        
        // Hide success message after 3 seconds
        $this->dispatch('hide-copy-success');
    }

    public function submitTask()
    {
        if ($this->isProcessing) {
            return;
        }

        $this->validate();

        // Check if user can still upload screenshot
        if (!$this->adTask->canUploadScreenshot()) {
            session()->flash('error', 'You can no longer upload a screenshot for this task.');
            return;
        }

        $this->isProcessing = true;

        try {
            DB::transaction(function () {
                // Store screenshot
                $screenshotPath = $this->screenshot->store('ad-screenshots', 'public');

                // Update ad task
                $this->adTask->update([
                    'screenshot_path' => $screenshotPath,
                    'screenshot_uploaded_at' => now(),
                    'view_count' => $this->viewCount,
                    'status' => \App\Models\AdTask::STATUS_PENDING_REVIEW,
                ]);

                // Create pending transaction
                $settingService = app(SettingService::class);
                $earningsPerView = $settingService->get('ad_earnings_per_view', 0.3);
                $estimatedEarnings = $this->viewCount * $earningsPerView;

                $transaction = Transaction::create([
                    'user_id' => $this->adTask->user_id,
                    'type' => Transaction::TYPE_CREDIT,
                    'category' => Transaction::CATEGORY_AD_EARNING,
                    'amount' => $estimatedEarnings,
                    'description' => "Ad earnings for: {$this->ad->title}",
                    'status' => Transaction::STATUS_PENDING,
                    'reference' => 'AD_TASK_' . $this->adTask->id . '_' . time(),
                    'related_id' => $this->adTask->id,
                    'source' => 'ad_task',
                    'metadata' => [
                        'ad_id' => $this->ad->id,
                        'ad_task_id' => $this->adTask->id,
                        'view_count' => $this->viewCount,
                        'earnings_per_view' => $earningsPerView,
                        'screenshot_path' => $screenshotPath,
                    ],
                ]);

                // Send notification to user
                try {
                    $notificationService = app(NotificationService::class);
                    $notificationService->sendAdTaskSubmittedNotification(
                        $this->adTask->user,
                        $this->adTask,
                        $estimatedEarnings
                    );
                } catch (\Exception $e) {
                    Log::warning('Failed to send ad task submission notification', [
                        'user_id' => $this->adTask->user_id,
                        'ad_task_id' => $this->adTask->id,
                        'error' => $e->getMessage()
                    ]);
                }

                Log::info('Ad task submitted for review', [
                    'user_id' => $this->adTask->user_id,
                    'ad_task_id' => $this->adTask->id,
                    'ad_id' => $this->ad->id,
                    'view_count' => $this->viewCount,
                    'estimated_earnings' => $estimatedEarnings,
                    'transaction_id' => $transaction->id
                ]);

                session()->flash('success', 'Task submitted successfully! Your submission is now under review.');
            });

            // Redirect to task history
            return redirect()->route('ads.tasks');

        } catch (\Exception $e) {
            Log::error('Failed to submit ad task', [
                'user_id' => Auth::id(),
                'ad_task_id' => $this->adTask->id,
                'error' => $e->getMessage()
            ]);

            session()->flash('error', 'Failed to submit task. Please try again.');
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        $this->updateTaskStatus();
        
        // Check if user can submit (24 hours have passed since task started)
        $canSubmit = $this->adTask->status === \App\Models\AdTask::STATUS_ACTIVE && 
                    $this->adTask->created_at->addDay()->isPast();
        
        // Get ad settings for earnings calculation
        $settingService = app(SettingService::class);
        $adSettings = [
            'share_per_view_rate' => $settingService->get('ad_earnings_per_view', 0.3)
        ];
        
        return view('livewire.ad-task', [
            'adTask' => $this->adTask,
            'ad' => $this->ad,
            'canUploadScreenshot' => $this->canUploadScreenshot,
            'timeRemaining' => $this->timeRemaining,
            'hoursRemaining' => $this->hoursRemaining,
            'canSubmit' => $canSubmit,
            'adSettings' => $adSettings,
        ]);
    }
}