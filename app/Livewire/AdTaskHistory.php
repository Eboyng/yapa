<?php

namespace App\Livewire;

use App\Models\AdTask;
use App\Models\Transaction;
use App\Models\User;
use App\Services\NotificationService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdTaskHistory extends Component
{
    use WithPagination;

    public string $activeTab = 'active';
    public bool $showAppealModal = false;
    public ?AdTask $appealTask = null;
    public string $appealMessage = '';
    public bool $isProcessing = false;

    protected $rules = [
        'appealMessage' => 'required|string|min:10|max:500',
    ];

    protected $messages = [
        'appealMessage.required' => 'Please provide a reason for your appeal.',
        'appealMessage.min' => 'Appeal message must be at least 10 characters.',
        'appealMessage.max' => 'Appeal message cannot exceed 500 characters.',
    ];

    protected $listeners = ['refreshHistory' => '$refresh'];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openAppealModal($adTaskId)
    {
        $this->appealTask = AdTask::with('ad')
            ->where('id', $adTaskId)
            ->where('user_id', Auth::id())
            ->where('status', AdTask::STATUS_REJECTED)
            ->whereNull('appeal_submitted_at')
            ->first();

        if (!$this->appealTask) {
            session()->flash('error', 'Appeal not available for this task.');
            return;
        }

        $this->showAppealModal = true;
        $this->appealMessage = '';
    }

    public function closeAppealModal()
    {
        $this->showAppealModal = false;
        $this->appealTask = null;
        $this->appealMessage = '';
        $this->resetValidation();
    }

    public function submitAppeal()
    {
        if ($this->isProcessing) {
            return;
        }

        $this->validate();

        if (!$this->appealTask || !$this->appealTask->canSubmitAppeal()) {
            session()->flash('error', 'Appeal cannot be submitted for this task.');
            return;
        }

        $this->isProcessing = true;

        try {
            // Submit appeal
            $this->appealTask->submitAppeal($this->appealMessage);

            // Check rejection count and flag user if necessary
            $user = Auth::user();
            $rejectionCount = AdTask::where('user_id', $user->id)
                ->where('status', AdTask::STATUS_REJECTED)
                ->count();

            if ($rejectionCount >= 3 && !$user->isFlaggedForAds()) {
                $user->update(['ad_rejection_count' => $rejectionCount]);
                
                // Send notification about account flagging
                try {
                    $notificationService = app(NotificationService::class);
                    $notificationService->sendAccountFlaggedNotification($user, $rejectionCount);
                } catch (\Exception $e) {
                    Log::warning('Failed to send account flagged notification', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }

                Log::info('User flagged for ad rejections', [
                    'user_id' => $user->id,
                    'rejection_count' => $rejectionCount
                ]);
            }

            Log::info('Ad task appeal submitted', [
                'user_id' => $user->id,
                'ad_task_id' => $this->appealTask->id,
                'appeal_message' => $this->appealMessage
            ]);

            session()->flash('success', 'Appeal submitted successfully. We will review it within 48 hours.');
            $this->closeAppealModal();

        } catch (\Exception $e) {
            Log::error('Failed to submit appeal', [
                'user_id' => Auth::id(),
                'ad_task_id' => $this->appealTask->id,
                'error' => $e->getMessage()
            ]);

            session()->flash('error', 'Failed to submit appeal. Please try again.');
        } finally {
            $this->isProcessing = false;
        }
    }

    public function sendAppealEmail($adTaskId)
    {
        try {
            $adTask = AdTask::with('ad')
                ->where('id', $adTaskId)
                ->where('user_id', Auth::id())
                ->where('status', AdTask::STATUS_REJECTED)
                ->first();

            if (!$adTask) {
                session()->flash('error', 'Task not found or appeal not available.');
                return;
            }

            $user = Auth::user();
            $subject = "Ad Task Appeal Request - Task #{$adTask->id}";
            $body = "Dear Admin,\n\nI would like to appeal the rejection of my ad task.\n\nTask Details:\n";
            $body .= "- Ad: {$adTask->ad->title}\n";
            $body .= "- Task ID: {$adTask->id}\n";
            $body .= "- Rejection Reason: {$adTask->rejection_reason}\n\n";
            $body .= "Please review my submission again.\n\nThank you.";

            $mailtoLink = "mailto:admin@yapa.com?subject=" . urlencode($subject) . "&body=" . urlencode($body);
            
            $this->dispatch('open-mailto', $mailtoLink);
            session()->flash('info', 'Email client opened. Please send the email to complete your appeal.');

        } catch (\Exception $e) {
            Log::error('Failed to generate appeal email', [
                'user_id' => Auth::id(),
                'ad_task_id' => $adTaskId,
                'error' => $e->getMessage()
            ]);

            session()->flash('error', 'Failed to generate appeal email.');
        }
    }

    public function render()
    {
        $user = Auth::user();
        
        // Get separate collections for each tab
        $activeTasks = AdTask::with(['ad', 'reviewedByAdmin'])
            ->where('user_id', $user->id)
            ->where('status', AdTask::STATUS_ACTIVE)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $pendingTasks = AdTask::with(['ad', 'reviewedByAdmin'])
            ->where('user_id', $user->id)
            ->whereIn('status', [
                AdTask::STATUS_SCREENSHOT_UPLOADED,
                AdTask::STATUS_PENDING_REVIEW
            ])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get transactions for history tab
        $transactions = Transaction::with(['adTask.ad'])
            ->where('user_id', $user->id)
            ->where('category', Transaction::CATEGORY_AD_EARNING)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get user stats
        $stats = [
            'total_tasks' => AdTask::where('user_id', $user->id)->count(),
            'approved_tasks' => AdTask::where('user_id', $user->id)->where('status', AdTask::STATUS_APPROVED)->count(),
            'rejected_tasks' => AdTask::where('user_id', $user->id)->where('status', AdTask::STATUS_REJECTED)->count(),
            'total_earnings' => Transaction::where('user_id', $user->id)
                ->where('category', Transaction::CATEGORY_AD_EARNING)
                ->where('status', Transaction::STATUS_COMPLETED)
                ->sum('amount'),
        ];

        return view('livewire.ad-task-history', [
            'activeTasks' => $activeTasks,
            'pendingTasks' => $pendingTasks,
            'transactions' => $transactions,
            'stats' => $stats,
            'isFlagged' => $user->isFlaggedForAds(),
        ]);
    }
}