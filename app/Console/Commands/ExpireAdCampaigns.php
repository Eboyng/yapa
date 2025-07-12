<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\NotificationLog;
use App\Models\User;
use App\Mail\NotificationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ExpireAdCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ads:expire-campaigns {--dry-run : Show what would be expired without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close ad campaigns that have exceeded their duration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting ad campaign expiry check...');
        
        $dryRun = $this->option('dry-run');
        
        try {
            // Get expired ad campaigns
            $expiredAds = Ad::where('status', 'active')
                ->where('expires_at', '<', now())
                ->with(['user'])
                ->get();

            if ($expiredAds->isEmpty()) {
                $this->info('No expired ad campaigns found.');
                return self::SUCCESS;
            }

            $this->info("Found {$expiredAds->count()} expired ad campaigns.");

            $expiredCount = 0;
            
            foreach ($expiredAds as $ad) {
                if ($dryRun) {
                    $this->line("Would expire ad campaign: {$ad->title} (ID: {$ad->id}) - Expired: {$ad->expires_at}");
                    continue;
                }

                try {
                    // Close the ad campaign
                    $ad->update([
                        'status' => 'expired',
                        'ended_at' => now(),
                    ]);

                    // Notify ad owner
                    $this->notifyAdOwner($ad);

                    // Log the expiry
                    NotificationLog::create([
                        'type' => 'ad_campaign_expired',
                        'channel' => NotificationLog::CHANNEL_EMAIL,
                        'recipient' => $ad->user->email ?? 'system',
                        'message' => "Ad campaign '{$ad->title}' has expired and been closed.",
                        'user_id' => $ad->user_id,
                        'status' => 'sent',
                        'sent_at' => now(),
                        'metadata' => json_encode([
                            'ad_id' => $ad->id,
                            'ad_title' => $ad->title,
                            'expired_at' => $ad->expires_at,
                            'duration_days' => $ad->duration_days,
                        ]),
                    ]);

                    $expiredCount++;
                    $this->info("Expired ad campaign: {$ad->title} (ID: {$ad->id})");
                } catch (\Exception $e) {
                    $this->error("Failed to expire ad {$ad->id}: {$e->getMessage()}");
                    Log::error('Failed to expire ad campaign', [
                        'ad_id' => $ad->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if (!$dryRun) {
                $this->info("Successfully expired {$expiredCount} ad campaigns.");
                
                // Notify admins about the expiry
                $this->notifyAdmins($expiredCount, $expiredAds->count());
            }

            Log::info('Ad campaign expiry completed', [
                'total_expired' => $expiredAds->count(),
                'expired_count' => $expiredCount,
                'dry_run' => $dryRun,
            ]);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Ad campaign expiry failed: {$e->getMessage()}");
            Log::error('Ad campaign expiry failed', [
                'error' => $e->getMessage(),
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Notify ad owner about campaign expiry.
     */
    private function notifyAdOwner(Ad $ad): void
    {
        try {
            if ($ad->user && $ad->user->email) {
                $message = "Your ad campaign '{$ad->title}' has reached its duration limit and has been closed.\n\n";
                $message .= "Campaign Details:\n";
                $message .= "- Title: {$ad->title}\n";
                $message .= "- Duration: {$ad->duration_days} days\n";
                $message .= "- Started: {$ad->created_at->format('Y-m-d H:i:s')}\n";
                $message .= "- Expired: {$ad->expires_at->format('Y-m-d H:i:s')}\n\n";
                $message .= "Thank you for advertising with us! You can create a new campaign anytime.";

                Mail::to($ad->user->email)->queue(
                    new NotificationMail(
                        'Ad Campaign Expired',
                        $message,
                        $ad->user
                    )
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify ad owner about campaign expiry', [
                'ad_id' => $ad->id,
                'user_id' => $ad->user_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notify admins about the expiry results.
     */
    private function notifyAdmins(int $expiredCount, int $totalExpired): void
    {
        try {
            $admins = User::where('is_admin', true)->get();
            $message = "Ad campaign expiry check completed.\n\nExpired: {$expiredCount} campaigns\nTotal checked: {$totalExpired} campaigns\nTime: " . now()->format('Y-m-d H:i:s');

            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(
                    new NotificationMail(
                        'Ad Campaign Expiry Report',
                        $message,
                        $admin
                    )
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admins about ad expiry', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}