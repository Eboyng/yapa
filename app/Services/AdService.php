<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\AdTask;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

class AdService
{
    const MAX_REJECTION_COUNT = 3;
    const EARNINGS_PER_VIEW = 0.3; // ₦0.3 per view
    const SCREENSHOT_WAIT_HOURS = 24;

    /**
     * Create a new ad campaign.
     */
    public function createAd(array $data, User $admin): Ad
    {
        $adData = [
            'title' => $data['title'],
            'description' => $data['description'],
            'url' => $data['url'] ?? null,
            'banner' => $data['banner'] ?? null,
            'status' => $data['status'] ?? Ad::STATUS_DRAFT,
            'earnings_per_view' => $data['earnings_per_view'] ?? self::EARNINGS_PER_VIEW,
            'max_participants' => $data['max_participants'] ?? null,
            'start_date' => $data['start_date'] ?? now(),
            'end_date' => $data['end_date'] ?? null,
            'created_by_admin_id' => $admin->id,
            'instructions' => $data['instructions'] ?? null,
            'terms_and_conditions' => $data['terms_and_conditions'] ?? null,
        ];

        return Ad::create($adData);
    }

    /**
     * Start an ad task for a user.
     */
    public function startAdTask(User $user, Ad $ad): array
    {
        if (!$ad->canUserParticipate($user)) {
            return [
                'success' => false,
                'message' => $this->getParticipationErrorMessage($user, $ad),
            ];
        }

        try {
            $adTask = AdTask::create([
                'ad_id' => $ad->id,
                'user_id' => $user->id,
                'status' => AdTask::STATUS_ACTIVE,
                'started_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Ad task started successfully! You can now copy the content and post to WhatsApp Status.',
                'ad_task' => $adTask,
                'copy_content' => $ad->copyContent,
                'wait_until' => now()->addHours(self::SCREENSHOT_WAIT_HOURS),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to start ad task', [
                'user_id' => $user->id,
                'ad_id' => $ad->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while starting the ad task.',
            ];
        }
    }

    /**
     * Get error message for participation failure.
     */
    protected function getParticipationErrorMessage(User $user, Ad $ad): string
    {
        if (!$ad->isActive()) {
            return 'This ad campaign is not currently active.';
        }

        if ($ad->isExpired()) {
            return 'This ad campaign has expired.';
        }

        if ($ad->hasReachedMaxParticipants()) {
            return 'This ad campaign has reached its maximum number of participants.';
        }

        if ($user->is_flagged_for_ads) {
            return 'You are currently flagged and cannot participate in ad campaigns. Please submit an appeal if you believe this is an error.';
        }

        if ($user->adTasks()->where('ad_id', $ad->id)->exists()) {
            return 'You have already participated in this ad campaign.';
        }

        return 'You cannot participate in this ad campaign at the moment.';
    }

    /**
     * Upload screenshot for ad task.
     */
    public function uploadScreenshot(AdTask $adTask, UploadedFile $screenshot, int $viewCount): array
    {
        if (!$adTask->canUploadScreenshot()) {
            return [
                'success' => false,
                'message' => $this->getUploadErrorMessage($adTask),
            ];
        }

        try {
            // Validate screenshot
            $validationResult = $this->validateScreenshot($screenshot);
            if (!$validationResult['valid']) {
                return [
                    'success' => false,
                    'message' => $validationResult['message'],
                ];
            }

            // Store screenshot
            $path = $screenshot->store('ad_screenshots', 'public');

            // Update ad task
            $adTask->update([
                'screenshot_path' => $path,
                'screenshot_uploaded_at' => now(),
                'view_count' => $viewCount,
                'status' => AdTask::STATUS_PENDING_REVIEW,
            ]);

            return [
                'success' => true,
                'message' => 'Screenshot uploaded successfully! Your submission is now pending admin review.',
                'ad_task' => $adTask->fresh(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to upload screenshot', [
                'ad_task_id' => $adTask->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while uploading the screenshot.',
            ];
        }
    }

    /**
     * Get error message for screenshot upload failure.
     */
    protected function getUploadErrorMessage(AdTask $adTask): string
    {
        if ($adTask->status !== AdTask::STATUS_ACTIVE) {
            return 'This ad task is not active.';
        }

        if ($adTask->isExpired()) {
            return 'This ad task has expired.';
        }

        $waitUntil = $adTask->started_at->addHours(self::SCREENSHOT_WAIT_HOURS);
        if (now()->lt($waitUntil)) {
            return "You must wait until {$waitUntil->format('M d, Y H:i')} before uploading a screenshot.";
        }

        if ($adTask->screenshot_path) {
            return 'You have already uploaded a screenshot for this task.';
        }

        return 'You cannot upload a screenshot for this task at the moment.';
    }

    /**
     * Validate uploaded screenshot.
     */
    protected function validateScreenshot(UploadedFile $screenshot): array
    {
        // Check file type
        if (!in_array($screenshot->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
            return [
                'valid' => false,
                'message' => 'Screenshot must be a JPG or PNG image.',
            ];
        }

        // Check file size (max 5MB)
        if ($screenshot->getSize() > 5 * 1024 * 1024) {
            return [
                'valid' => false,
                'message' => 'Screenshot file size must be less than 5MB.',
            ];
        }

        // Additional validation could include:
        // - Image dimension checks
        // - OCR to verify WhatsApp Status interface
        // - Timestamp validation

        return [
            'valid' => true,
            'message' => 'Screenshot is valid.',
        ];
    }

    /**
     * Review ad task submission.
     */
    public function reviewAdTask(AdTask $adTask, string $decision, ?string $reason = null, ?User $reviewer = null): array
    {
        if ($adTask->status !== AdTask::STATUS_PENDING_REVIEW) {
            return [
                'success' => false,
                'message' => 'This ad task is not pending review.',
            ];
        }

        try {
            if ($decision === 'approve') {
                return $this->approveAdTask($adTask, $reviewer);
            } else {
                return $this->rejectAdTask($adTask, $reason, $reviewer);
            }
        } catch (\Exception $e) {
            Log::error('Failed to review ad task', [
                'ad_task_id' => $adTask->id,
                'decision' => $decision,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while reviewing the ad task.',
            ];
        }
    }

    /**
     * Approve ad task and process earnings.
     */
    protected function approveAdTask(AdTask $adTask, ?User $reviewer): array
    {
        $earnings = $adTask->calculateEarnings();

        $adTask->update([
            'status' => AdTask::STATUS_APPROVED,
            'earnings_amount' => $earnings,
            'reviewed_at' => now(),
            'reviewed_by_admin_id' => $reviewer?->id,
        ]);

        // Earnings are automatically added in the model's boot method

        return [
            'success' => true,
            'message' => "Ad task approved! User earned ₦{$earnings}.",
            'earnings' => $earnings,
        ];
    }

    /**
     * Reject ad task and handle user flagging.
     */
    protected function rejectAdTask(AdTask $adTask, ?string $reason, ?User $reviewer): array
    {
        $adTask->update([
            'status' => AdTask::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'reviewed_at' => now(),
            'reviewed_by_admin_id' => $reviewer?->id,
        ]);

        // Increment user's rejection count
        $user = $adTask->user;
        $user->increment('ad_rejection_count');

        // Check if user should be flagged
        if ($user->ad_rejection_count >= self::MAX_REJECTION_COUNT && !$user->is_flagged_for_ads) {
            $this->flagUserForAds($user);
        }

        // Send rejection notification
        $this->sendRejectionNotification($adTask, $reason);

        return [
            'success' => true,
            'message' => 'Ad task rejected and user notified.',
            'user_flagged' => $user->is_flagged_for_ads,
        ];
    }

    /**
     * Flag user for ad violations.
     */
    protected function flagUserForAds(User $user): void
    {
        $user->update([
            'is_flagged_for_ads' => true,
            'flagged_at' => now(),
        ]);

        // Send flagging notification
        $this->sendFlaggingNotification($user);

        Log::info('User flagged for ad violations', [
            'user_id' => $user->id,
            'rejection_count' => $user->ad_rejection_count,
        ]);
    }

    /**
     * Send rejection notification to user.
     */
    protected function sendRejectionNotification(AdTask $adTask, ?string $reason): void
    {
        if (!$adTask->user->email) {
            return;
        }

        try {
            Mail::send('emails.ad-task-rejected', [
                'user' => $adTask->user,
                'ad' => $adTask->ad,
                'reason' => $reason,
                'rejection_count' => $adTask->user->ad_rejection_count,
                'max_rejections' => self::MAX_REJECTION_COUNT,
            ], function ($message) use ($adTask) {
                $message->to($adTask->user->email, $adTask->user->name)
                       ->subject('Ad Task Submission Rejected');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send rejection notification', [
                'ad_task_id' => $adTask->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send flagging notification to user.
     */
    protected function sendFlaggingNotification(User $user): void
    {
        if (!$user->email) {
            return;
        }

        try {
            Mail::send('emails.user-flagged-ads', [
                'user' => $user,
                'rejection_count' => $user->ad_rejection_count,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                       ->subject('Account Flagged - Ad Campaign Participation Suspended');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send flagging notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Submit appeal for flagged user.
     */
    public function submitAppeal(User $user, string $appealMessage): array
    {
        if (!$user->is_flagged_for_ads) {
            return [
                'success' => false,
                'message' => 'You are not currently flagged for ad campaigns.',
            ];
        }

        if ($user->appeal_submitted_at && $user->appeal_submitted_at->gt(now()->subDays(7))) {
            return [
                'success' => false,
                'message' => 'You can only submit one appeal per week.',
            ];
        }

        try {
            $user->update([
                'appeal_message' => $appealMessage,
                'appeal_submitted_at' => now(),
            ]);

            // Notify admins about the appeal
            $this->notifyAdminsAboutAppeal($user);

            return [
                'success' => true,
                'message' => 'Your appeal has been submitted successfully. We will review it and get back to you.',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to submit appeal', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while submitting your appeal.',
            ];
        }
    }

    /**
     * Notify admins about user appeal.
     */
    protected function notifyAdminsAboutAppeal(User $user): void
    {
        // Get admin emails from settings or admin users
        $adminEmails = User::role('admin')->pluck('email')->filter();

        foreach ($adminEmails as $adminEmail) {
            try {
                Mail::send('emails.admin-user-appeal', [
                    'user' => $user,
                    'appeal_message' => $user->appeal_message,
                ], function ($message) use ($adminEmail, $user) {
                    $message->to($adminEmail)
                           ->subject("User Appeal: {$user->name} ({$user->email})");
                });
            } catch (\Exception $e) {
                Log::error('Failed to notify admin about appeal', [
                    'user_id' => $user->id,
                    'admin_email' => $adminEmail,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Process appeal decision.
     */
    public function processAppeal(User $user, string $decision, ?string $adminNotes = null, ?User $admin = null): array
    {
        if (!$user->is_flagged_for_ads || !$user->appeal_submitted_at) {
            return [
                'success' => false,
                'message' => 'No valid appeal found for this user.',
            ];
        }

        try {
            if ($decision === 'approve') {
                $user->update([
                    'is_flagged_for_ads' => false,
                    'flagged_at' => null,
                    'ad_rejection_count' => 0,
                    'appeal_message' => null,
                    'appeal_submitted_at' => null,
                ]);

                $message = 'Appeal approved. User has been unflagged and can participate in ad campaigns again.';
            } else {
                $user->update([
                    'appeal_message' => null,
                    'appeal_submitted_at' => null,
                ]);

                $message = 'Appeal rejected. User remains flagged.';
            }

            // Send notification to user
            $this->sendAppealDecisionNotification($user, $decision, $adminNotes);

            Log::info('Appeal processed', [
                'user_id' => $user->id,
                'decision' => $decision,
                'admin_id' => $admin?->id,
            ]);

            return [
                'success' => true,
                'message' => $message,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to process appeal', [
                'user_id' => $user->id,
                'decision' => $decision,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while processing the appeal.',
            ];
        }
    }

    /**
     * Send appeal decision notification to user.
     */
    protected function sendAppealDecisionNotification(User $user, string $decision, ?string $adminNotes): void
    {
        if (!$user->email) {
            return;
        }

        try {
            Mail::send('emails.appeal-decision', [
                'user' => $user,
                'decision' => $decision,
                'admin_notes' => $adminNotes,
            ], function ($message) use ($user, $decision) {
                $message->to($user->email, $user->name)
                       ->subject("Appeal Decision: " . ucfirst($decision));
            });
        } catch (\Exception $e) {
            Log::error('Failed to send appeal decision notification', [
                'user_id' => $user->id,
                'decision' => $decision,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get ad campaign statistics.
     */
    public function getAdStatistics(): array
    {
        return [
            'total_ads' => Ad::count(),
            'active_ads' => Ad::active()->count(),
            'total_tasks' => AdTask::count(),
            'pending_reviews' => AdTask::pendingReview()->count(),
            'approved_tasks' => AdTask::approved()->count(),
            'rejected_tasks' => AdTask::rejected()->count(),
            'total_earnings' => AdTask::approved()->sum('earnings_amount'),
            'flagged_users' => User::flaggedForAds()->count(),
            'pending_appeals' => User::pendingAdAppeals()->count(),
        ];
    }

    /**
     * Get user's ad task history.
     */
    public function getUserAdTasks(User $user, ?string $status = null): array
    {
        $query = $user->adTasks()->with('ad');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    /**
     * Upload banner for ad.
     */
    public function uploadAdBanner(Ad $ad, UploadedFile $banner): array
    {
        try {
            // Validate banner
            if (!in_array($banner->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
                return [
                    'success' => false,
                    'message' => 'Banner must be a JPG or PNG image.',
                ];
            }

            if ($banner->getSize() > 2 * 1024 * 1024) {
                return [
                    'success' => false,
                    'message' => 'Banner file size must be less than 2MB.',
                ];
            }

            // Delete old banner if exists
            if ($ad->banner) {
                Storage::disk('public')->delete($ad->banner);
            }

            // Store new banner
            $path = $banner->store('ad_banners', 'public');
            $ad->update(['banner' => $path]);

            return [
                'success' => true,
                'message' => 'Banner uploaded successfully.',
                'banner_url' => $ad->bannerUrl,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to upload ad banner', [
                'ad_id' => $ad->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while uploading the banner.',
            ];
        }
    }
}