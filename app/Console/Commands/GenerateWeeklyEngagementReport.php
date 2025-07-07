<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Batch;
use App\Models\Transaction;
use App\Models\WalletTransaction;
use App\Models\NotificationLog;
use App\Mail\NotificationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class GenerateWeeklyEngagementReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:weekly-engagement {--send-to= : Specific email to send report to (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send weekly engagement report to admins';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating weekly engagement report...');
        
        try {
            $reportData = $this->generateReportData();
            $reportContent = $this->formatReport($reportData);
            
            // Send to specific email if provided, otherwise to all admins
            $sendTo = $this->option('send-to');
            if ($sendTo) {
                $this->sendReportToEmail($sendTo, $reportContent);
                $this->info("Report sent to: {$sendTo}");
            } else {
                $this->sendReportToAdmins($reportContent);
                $this->info('Report sent to all admins.');
            }

            // Log the report generation
            NotificationLog::create([
                'type' => 'weekly_engagement_report',
                'recipient' => $sendTo ?? 'admins',
                'subject' => 'Weekly Engagement Report - ' . now()->format('Y-m-d'),
                'message' => 'Weekly engagement report generated and sent',
                'status' => 'sent',
                'sent_at' => now(),
                'metadata' => json_encode($reportData),
            ]);

            Log::info('Weekly engagement report generated successfully', $reportData);
            
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to generate weekly engagement report: {$e->getMessage()}");
            Log::error('Weekly engagement report generation failed', [
                'error' => $e->getMessage(),
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Generate report data for the past week.
     */
    private function generateReportData(): array
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();

        // Total users
        $totalUsers = User::count();
        
        // New registrations this week
        $newRegistrations = User::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $lastWeekRegistrations = User::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();
        
        // Active batches
        $activeBatches = Batch::where('status', 'active')->count();
        $newBatchesThisWeek = Batch::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $completedBatchesThisWeek = Batch::where('status', 'completed')
            ->whereBetween('updated_at', [$weekStart, $weekEnd])
            ->count();
        
        // Credit sale revenue
        $creditSalesRevenue = WalletTransaction::where('type', 'credit_purchase')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->sum('amount');
        
        $lastWeekCreditRevenue = WalletTransaction::where('type', 'credit_purchase')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->sum('amount');
        
        // Transaction statistics
        $totalTransactions = Transaction::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $successfulTransactions = Transaction::where('status', 'success')
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        
        // User engagement metrics
        $activeUsersThisWeek = User::whereHas('walletTransactions', function ($query) use ($weekStart, $weekEnd) {
            $query->whereBetween('created_at', [$weekStart, $weekEnd]);
        })->orWhereHas('batches', function ($query) use ($weekStart, $weekEnd) {
            $query->whereBetween('created_at', [$weekStart, $weekEnd]);
        })->count();
        
        // Top performing batches
        $topBatches = Batch::withCount('members')
            ->where('status', 'active')
            ->orderBy('members_count', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'max_members', 'created_at'])
            ->map(function ($batch) {
                return [
                    'name' => $batch->name,
                    'members' => $batch->members_count,
                    'max_members' => $batch->max_members,
                    'fill_percentage' => round(($batch->members_count / $batch->max_members) * 100, 1),
                    'created_at' => $batch->created_at->format('Y-m-d'),
                ];
            });

        return [
            'period' => [
                'start' => $weekStart->format('Y-m-d'),
                'end' => $weekEnd->format('Y-m-d'),
            ],
            'users' => [
                'total' => $totalUsers,
                'new_registrations' => $newRegistrations,
                'last_week_registrations' => $lastWeekRegistrations,
                'registration_change' => $newRegistrations - $lastWeekRegistrations,
                'active_this_week' => $activeUsersThisWeek,
            ],
            'batches' => [
                'active' => $activeBatches,
                'new_this_week' => $newBatchesThisWeek,
                'completed_this_week' => $completedBatchesThisWeek,
                'top_performing' => $topBatches,
            ],
            'revenue' => [
                'credit_sales_this_week' => $creditSalesRevenue,
                'credit_sales_last_week' => $lastWeekCreditRevenue,
                'revenue_change' => $creditSalesRevenue - $lastWeekCreditRevenue,
            ],
            'transactions' => [
                'total' => $totalTransactions,
                'successful' => $successfulTransactions,
                'success_rate' => $totalTransactions > 0 ? round(($successfulTransactions / $totalTransactions) * 100, 1) : 0,
            ],
        ];
    }

    /**
     * Format the report data into a readable email content.
     */
    private function formatReport(array $data): string
    {
        $content = "📊 Weekly Engagement Report\n";
        $content .= "Period: {$data['period']['start']} to {$data['period']['end']}\n\n";
        
        // Users section
        $content .= "👥 USER METRICS\n";
        $content .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $content .= "Total Users: " . number_format($data['users']['total']) . "\n";
        $content .= "New Registrations: " . number_format($data['users']['new_registrations']);
        if ($data['users']['registration_change'] != 0) {
            $change = $data['users']['registration_change'] > 0 ? '+' . $data['users']['registration_change'] : $data['users']['registration_change'];
            $content .= " ({$change} vs last week)";
        }
        $content .= "\n";
        $content .= "Active Users This Week: " . number_format($data['users']['active_this_week']) . "\n\n";
        
        // Batches section
        $content .= "📦 BATCH METRICS\n";
        $content .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $content .= "Active Batches: " . number_format($data['batches']['active']) . "\n";
        $content .= "New Batches This Week: " . number_format($data['batches']['new_this_week']) . "\n";
        $content .= "Completed This Week: " . number_format($data['batches']['completed_this_week']) . "\n\n";
        
        // Top performing batches
        if (!empty($data['batches']['top_performing'])) {
            $content .= "🏆 TOP PERFORMING BATCHES\n";
            $content .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            foreach ($data['batches']['top_performing'] as $batch) {
                $content .= "• {$batch['name']}: {$batch['members']}/{$batch['max_members']} ({$batch['fill_percentage']}%)\n";
            }
            $content .= "\n";
        }
        
        // Revenue section
        $content .= "💰 REVENUE METRICS\n";
        $content .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $content .= "Credit Sales This Week: ₦" . number_format($data['revenue']['credit_sales_this_week'], 2) . "\n";
        if ($data['revenue']['revenue_change'] != 0) {
            $change = $data['revenue']['revenue_change'] > 0 ? '+₦' . number_format($data['revenue']['revenue_change'], 2) : '-₦' . number_format(abs($data['revenue']['revenue_change']), 2);
            $content .= "Change from Last Week: {$change}\n";
        }
        $content .= "\n";
        
        // Transactions section
        $content .= "💳 TRANSACTION METRICS\n";
        $content .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $content .= "Total Transactions: " . number_format($data['transactions']['total']) . "\n";
        $content .= "Successful Transactions: " . number_format($data['transactions']['successful']) . "\n";
        $content .= "Success Rate: {$data['transactions']['success_rate']}%\n\n";
        
        $content .= "Generated on: " . now()->format('Y-m-d H:i:s') . "\n";
        $content .= "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $content .= "This is an automated weekly report from YAPA.";
        
        return $content;
    }

    /**
     * Send report to all admins.
     */
    private function sendReportToAdmins(string $content): void
    {
        $admins = User::where('is_admin', true)->get();
        
        foreach ($admins as $admin) {
            $this->sendReportToEmail($admin->email, $content, $admin);
        }
    }

    /**
     * Send report to a specific email.
     */
    private function sendReportToEmail(string $email, string $content, ?User $user = null): void
    {
        try {
            Mail::to($email)->send(
                new NotificationMail(
                    'Weekly Engagement Report - ' . now()->format('Y-m-d'),
                    $content,
                    $user
                )
            );
        } catch (\Exception $e) {
            Log::error('Failed to send weekly engagement report', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}