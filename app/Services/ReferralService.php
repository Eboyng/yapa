<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use App\Models\BatchShare;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    protected TransactionService $transactionService;
    protected NotificationService $notificationService;
    protected SettingService $settingService;

    public function __construct(
        TransactionService $transactionService,
        NotificationService $notificationService,
        SettingService $settingService
    ) {
        $this->transactionService = $transactionService;
        $this->notificationService = $notificationService;
        $this->settingService = $settingService;
    }

    /**
     * Process referral on user registration.
     */
    public function processRegistrationReferral(User $user, ?string $referralCode = null): void
    {
        if (!$referralCode || !$this->settingService->get('referral_enabled', false)) {
            return;
        }

        $referrer = User::withReferralCode($referralCode)->first();
        if (!$referrer || $referrer->id === $user->id) {
            return;
        }

        // Set referral relationship
        $user->update([
            'referred_by' => $referrer->id,
            'referred_at' => now(),
        ]);

        // Give registration reward to referrer
        $registrationReward = $this->settingService->get('referral_registration_reward', 0);
        if ($registrationReward > 0) {
            $this->giveReferralReward(
                $referrer,
                $registrationReward,
                'earnings',
                "Registration referral reward for {$user->name}",
                $user->id
            );
        }

        Log::info('Referral processed', [
            'referrer_id' => $referrer->id,
            'referred_user_id' => $user->id,
            'registration_reward' => $registrationReward,
        ]);
    }

    /**
     * Process referral on deposit.
     */
    public function processDepositReferral(User $user, float $depositAmount): void
    {
        if (!$user->wasReferred() || !$this->settingService->get('referral_enabled', false)) {
            return;
        }

        $referrer = $user->referrer;
        if (!$referrer) {
            return;
        }

        $fixedReward = $this->settingService->get('referral_deposit_fixed_reward', 0);
        $percentageReward = $this->settingService->get('referral_deposit_percentage', 20);

        if ($fixedReward > 0) {
            $rewardAmount = $fixedReward;
        } else {
            $rewardAmount = ($depositAmount * $percentageReward) / 100;
        }

        if ($rewardAmount > 0) {
            $this->giveReferralReward(
                $referrer,
                $rewardAmount,
                'earnings',
                "Deposit referral reward from {$user->name} (₦{$depositAmount})",
                $user->id
            );
        }

        Log::info('Deposit referral processed', [
            'referrer_id' => $referrer->id,
            'referred_user_id' => $user->id,
            'deposit_amount' => $depositAmount,
            'reward_amount' => $rewardAmount,
            'reward_type' => $fixedReward > 0 ? 'fixed' : 'percentage',
        ]);
    }

    /**
     * Process batch sharing reward.
     */
    public function processBatchSharingReward(BatchShare $batchShare): void
    {
        if ($batchShare->rewarded || !$batchShare->canClaimReward()) {
            return;
        }

        $rewardAmount = $this->settingService->get('batch_share_reward', 100);
        
        DB::transaction(function () use ($batchShare, $rewardAmount) {
            // Mark reward as claimed
            $batchShare->markRewardClaimed();

            // Give reward to sharer
            $this->transactionService->credit(
                $batchShare->user,
                $rewardAmount,
                Transaction::CATEGORY_BATCH_SHARE_REWARD,
                "Batch sharing reward for {$batchShare->batch->name}",
                'earnings',
                $batchShare->batch_id
            );

            // Send notification
            $this->notificationService->sendBatchShareReward(
                $batchShare->user,
                $batchShare->batch,
                $rewardAmount
            );
        });

        Log::info('Batch sharing reward processed', [
            'user_id' => $batchShare->user_id,
            'batch_id' => $batchShare->batch_id,
            'reward_amount' => $rewardAmount,
            'share_count' => $batchShare->share_count,
        ]);
    }

    /**
     * Track batch share.
     */
    public function trackBatchShare(User $user, int $batchId, string $platform): BatchShare
    {
        return BatchShare::firstOrCreate(
            [
                'user_id' => $user->id,
                'batch_id' => $batchId,
            ],
            [
                'platform' => $platform,
                'share_count' => 0,
                'rewarded' => false,
            ]
        );
    }

    /**
     * Increment batch share member count.
     */
    public function incrementBatchShareMemberCount(User $newMember, int $batchId): void
    {
        if (!$newMember->wasReferred()) {
            return;
        }

        $referrer = $newMember->referrer;
        if (!$referrer) {
            return;
        }

        $batchShare = BatchShare::where('user_id', $referrer->id)
            ->where('batch_id', $batchId)
            ->first();

        if ($batchShare) {
            $batchShare->incrementShareCount();
            
            // Check if reward can be claimed
            if ($batchShare->canClaimReward()) {
                $this->processBatchSharingReward($batchShare);
            }
        }
    }

    /**
     * Give referral reward to user.
     */
    protected function giveReferralReward(
        User $referrer,
        float $amount,
        string $walletType,
        string $description,
        int $relatedUserId
    ): void {
        $this->transactionService->credit(
            $referrer,
            $amount,
            Transaction::CATEGORY_REFERRAL_REWARD,
            $description,
            $walletType,
            $relatedUserId
        );

        // Send notification
        $this->notificationService->sendReferralReward(
            $referrer,
            $amount,
            $description
        );
    }

    /**
     * Get referral statistics for user.
     */
    public function getReferralStatistics(User $user): array
    {
        return [
            'total_referred' => $user->referredUsers()->count(),
            'total_rewards' => $user->getTotalReferralRewards(),
            'registration_rewards' => $user->transactions()
                ->join('wallets', 'transactions.wallet_id', '=', 'wallets.id')
                ->where('transactions.category', Transaction::CATEGORY_REFERRAL_REWARD)
                ->where('transactions.description', 'like', '%Registration referral reward%')
                ->where('wallets.type', 'earnings')
                ->sum('transactions.amount'),
            'deposit_rewards' => $user->transactions()
                ->join('wallets', 'transactions.wallet_id', '=', 'wallets.id')
                ->where('transactions.category', Transaction::CATEGORY_REFERRAL_REWARD)
                ->where('transactions.description', 'like', '%Deposit referral reward%')
                ->where('wallets.type', 'earnings')
                ->sum('transactions.amount'),
        ];
    }

    /**
     * Get batch sharing statistics for user.
     */
    public function getBatchSharingStatistics(User $user): array
    {
        $batchShares = $user->batchShares;
        
        return [
            'total_shared_batches' => $batchShares->count(),
            'total_new_members' => $batchShares->sum('share_count'),
            'total_rewards_claimed' => $batchShares->where('rewarded', true)->count(),
            'total_reward_amount' => $user->transactions()
                ->join('wallets', 'transactions.wallet_id', '=', 'wallets.id')
                ->where('transactions.category', Transaction::CATEGORY_BATCH_SHARE_REWARD)
                ->where('wallets.type', 'earnings')
                ->sum('transactions.amount'),
        ];
    }
}