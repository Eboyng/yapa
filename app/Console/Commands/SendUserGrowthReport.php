<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\NotificationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SendUserGrowthReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:user-growth {period=weekly : Report period (weekly or monthly)} {--send-to= : Specific email to send report to (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send user growth reports (weekly/monthly) to admins';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $period = $this->argument('period');
        
        if (!in_array($period, ['weekly', 'monthly'])) {
            $this->error('Invalid period. Use "weekly" or "monthly".');
            return 1;
        }
        
        $this->info("Generating {$period} user growth report...");
        
        try {
            $reportData = $this->generateReportData($period);
            $reportContent = $this->formatReport($reportData, $period);
            
            // Send to specific email if provided, otherwise to all admins
            $sendTo = $this->option('send-to');
            if ($sendTo) {
                $this->sendReportToEmail($sendTo, $reportContent, $period);
                $this->info("Report sent to: {$sendTo}");
            } else {
                $this->sendReportToAdmins($reportContent, $period);
                $this->info('Report sent to all admins.');
            }
            
            Log::info("User growth report ({$period}) generated and sent successfully");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Failed to generate {$period} report: " . $e->getMessage());
            Log::error("User growth report ({$period}) generation failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Generate report data based on period
     */
    private function generateReportData(string $period): array
    {
        $now = Carbon::now();
        
        if ($period === 'weekly') {
            $startDate = $now->copy()->subWeek()->startOfWeek();
            $endDate = $now->copy()->subWeek()->endOfWeek();
            $previousStartDate = $startDate->copy()->subWeek();
            $previousEndDate = $endDate->copy()->subWeek();
        } else {
            $startDate = $now->copy()->subMonth()->startOfMonth();
            $endDate = $now->copy()->subMonth()->endOfMonth();
            $previousStartDate = $startDate->copy()->subMonth();
            $previousEndDate = $endDate->copy()->subMonth();
        }

        // Current period data
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalUsers = User::where('created_at', '<=', $endDate)->count();
        $activeUsers = User::whereBetween('last_login_at', [$startDate, $endDate])->count();
        $verifiedUsers = User::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('email_verified_at')
            ->count();
        $flaggedUsers = User::whereBetween('created_at', [$startDate, $endDate])
            ->where('is_flagged_for_ads', true)
            ->count();
        
        // Previous period data for comparison
        $previousNewUsers = User::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
        $previousActiveUsers = User::whereBetween('last_login_at', [$previousStartDate, $previousEndDate])->count();
        $previousVerifiedUsers = User::whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->whereNotNull('email_verified_at')
            ->count();
        
        // Calculate growth percentages
        $newUsersGrowth = $previousNewUsers > 0 ? (($newUsers - $previousNewUsers) / $previousNewUsers) * 100 : 0;
        $activeUsersGrowth = $previousActiveUsers > 0 ? (($activeUsers - $previousActiveUsers) / $previousActiveUsers) * 100 : 0;
        $verifiedUsersGrowth = $previousVerifiedUsers > 0 ? (($verifiedUsers - $previousVerifiedUsers) / $previousVerifiedUsers) * 100 : 0;
        
        // User registration trends by day
        $registrationTrends = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date')
        ->get();
        
        // Get top referrers (users who referred others)
        $topReferrers = User::whereBetween('users.created_at', [$startDate, $endDate])
            ->whereNotNull('users.referred_by')
            ->join('users as referrers', 'users.referred_by', '=', 'referrers.id')
            ->select('referrers.name as referrer_name', 'referrers.email as referrer_email', DB::raw('COUNT(*) as count'))
            ->groupBy('referrers.id', 'referrers.name', 'referrers.email')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        return [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'new_users' => $newUsers,
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'verified_users' => $verifiedUsers,
            'flagged_users' => $flaggedUsers,
            'previous_new_users' => $previousNewUsers,
            'previous_active_users' => $previousActiveUsers,
            'previous_verified_users' => $previousVerifiedUsers,
            'new_users_growth' => round($newUsersGrowth, 2),
            'active_users_growth' => round($activeUsersGrowth, 2),
            'verified_users_growth' => round($verifiedUsersGrowth, 2),
            'registration_trends' => $registrationTrends,
            'top_referrers' => $topReferrers,
            'verification_rate' => $newUsers > 0 ? round(($verifiedUsers / $newUsers) * 100, 2) : 0,
            'spam_rate' => $newUsers > 0 ? round(($flaggedUsers / $newUsers) * 100, 2) : 0,
        ];
    }

    /**
     * Format the report data into HTML content
     */
    private function formatReport(array $data, string $period): string
    {
        $periodTitle = ucfirst($period);
        $growthIcon = function($growth) {
            return $growth >= 0 ? '📈' : '📉';
        };
        
        $growthColor = function($growth) {
            return $growth >= 0 ? 'color: #10b981;' : 'color: #ef4444;';
        };
        
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;'>
            <h1 style='color: #1f2937; border-bottom: 3px solid #3b82f6; padding-bottom: 10px;'>
                {$periodTitle} User Growth Report
            </h1>
            
            <div style='background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #374151; margin-top: 0;'>📊 Report Period</h3>
                <p><strong>Period:</strong> {$data['start_date']} to {$data['end_date']}</p>
                <p><strong>Generated:</strong> " . Carbon::now()->format('Y-m-d H:i:s') . "</p>
            </div>
            
            <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0;'>
                <div style='background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; text-align: center;'>
                    <h4 style='color: #3b82f6; margin: 0 0 10px 0;'>👥 New Users</h4>
                    <p style='font-size: 24px; font-weight: bold; margin: 5px 0; color: #1f2937;'>{$data['new_users']}</p>
                    <p style='margin: 0; {$growthColor($data['new_users_growth'])}'>
                        {$growthIcon($data['new_users_growth'])} {$data['new_users_growth']}% vs previous {$period}
                    </p>
                </div>
                
                <div style='background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; text-align: center;'>
                    <h4 style='color: #10b981; margin: 0 0 10px 0;'>🎯 Active Users</h4>
                    <p style='font-size: 24px; font-weight: bold; margin: 5px 0; color: #1f2937;'>{$data['active_users']}</p>
                    <p style='margin: 0; {$growthColor($data['active_users_growth'])}'>
                        {$growthIcon($data['active_users_growth'])} {$data['active_users_growth']}% vs previous {$period}
                    </p>
                </div>
                
                <div style='background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; text-align: center;'>
                    <h4 style='color: #8b5cf6; margin: 0 0 10px 0;'>✅ Verified Users</h4>
                    <p style='font-size: 24px; font-weight: bold; margin: 5px 0; color: #1f2937;'>{$data['verified_users']}</p>
                    <p style='margin: 0; {$growthColor($data['verified_users_growth'])}'>
                        {$growthIcon($data['verified_users_growth'])} {$data['verified_users_growth']}% vs previous {$period}
                    </p>
                </div>
                
                <div style='background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; text-align: center;'>
                    <h4 style='color: #f59e0b; margin: 0 0 10px 0;'>📈 Total Users</h4>
                    <p style='font-size: 24px; font-weight: bold; margin: 5px 0; color: #1f2937;'>{$data['total_users']}</p>
                    <p style='margin: 0; color: #6b7280;'>Platform total</p>
                </div>
            </div>
            
            <div style='background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0;'>
                <h3 style='color: #374151; margin-top: 0;'>📊 Key Metrics</h3>
                <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;'>
                    <div>
                        <p style='margin: 5px 0; color: #6b7280;'>Verification Rate</p>
                        <p style='font-size: 18px; font-weight: bold; color: #10b981;'>{$data['verification_rate']}%</p>
                    </div>
                    <div>
                        <p style='margin: 5px 0; color: #6b7280;'>Spam Rate</p>
                        <p style='font-size: 18px; font-weight: bold; color: #ef4444;'>{$data['spam_rate']}%</p>
                    </div>
                    <div>
                        <p style='margin: 5px 0; color: #6b7280;'>Flagged Users</p>
                        <p style='font-size: 18px; font-weight: bold; color: #f59e0b;'>{$data['flagged_users']}</p>
                    </div>
                </div>
            </div>
        ";
        
        // Registration trends
        if ($data['registration_trends']->isNotEmpty()) {
            $html .= "
            <div style='background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0;'>
                <h3 style='color: #374151; margin-top: 0;'>📅 Daily Registration Trends</h3>
                <div style='overflow-x: auto;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <thead>
                            <tr style='background: #f9fafb;'>
                                <th style='padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;'>Date</th>
                                <th style='padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;'>New Users</th>
                            </tr>
                        </thead>
                        <tbody>
            ";
            
            foreach ($data['registration_trends'] as $trend) {
                $html .= "
                            <tr>
                                <td style='padding: 8px 10px; border-bottom: 1px solid #f3f4f6;'>{$trend->date}</td>
                                <td style='padding: 8px 10px; text-align: right; border-bottom: 1px solid #f3f4f6; font-weight: bold;'>{$trend->count}</td>
                            </tr>
                ";
            }
            
            $html .= "
                        </tbody>
                    </table>
                </div>
            </div>
            ";
        }
        
        // Top referrers
        if ($data['top_referrers']->isNotEmpty()) {
            $html .= "
            <div style='background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0;'>
                <h3 style='color: #374151; margin-top: 0;'>🎯 Top Referrers</h3>
                <div style='overflow-x: auto;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <thead>
                            <tr style='background: #f9fafb;'>
                                <th style='padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;'>Referrer</th>
                                <th style='padding: 10px; text-align: right; border-bottom: 1px solid #e5e7eb;'>Referrals</th>
                            </tr>
                        </thead>
                        <tbody>
            ";
            
            foreach ($data['top_referrers'] as $referrer) {
                $html .= "
                            <tr>
                                <td style='padding: 8px 10px; border-bottom: 1px solid #f3f4f6;'>{$referrer->referrer_name} ({$referrer->referrer_email})</td>
                                <td style='padding: 8px 10px; text-align: right; border-bottom: 1px solid #f3f4f6; font-weight: bold;'>{$referrer->count}</td>
                            </tr>
                ";
            }
            
            $html .= "
                        </tbody>
                    </table>
                </div>
            </div>
            ";
        }
        
        $html .= "
            <div style='background: #f8fafc; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center;'>
                <p style='margin: 0; color: #6b7280; font-size: 14px;'>
                    This report was automatically generated by the YAPA Admin System<br>
                    Generated on " . Carbon::now()->format('F j, Y \a\t g:i A') . "
                </p>
            </div>
        </div>
        ";
        
        return $html;
    }

    /**
     * Send report to specific email
     */
    private function sendReportToEmail(string $email, string $content, string $period): void
    {
        $subject = ucfirst($period) . ' User Growth Report - ' . Carbon::now()->format('M j, Y');
        
        Mail::send([], [], function ($message) use ($email, $subject, $content) {
            $message->to($email)
                    ->subject($subject)
                    ->html($content);
        });
    }

    /**
     * Send report to all admin users
     */
    private function sendReportToAdmins(string $content, string $period): void
    {
        $subject = ucfirst($period) . ' User Growth Report - ' . Carbon::now()->format('M j, Y');
        
        // Get all admin users (assuming they have admin role or specific permission)
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();
        
        // Fallback: if no role-based admins, send to users with admin email pattern or specific flag
        if ($adminUsers->isEmpty()) {
            $adminUsers = User::where('email', 'like', '%admin%')
                            ->orWhere('is_admin', true)
                            ->get();
        }
        
        foreach ($adminUsers as $admin) {
            try {
                Mail::send([], [], function ($message) use ($admin, $subject, $content) {
                    $message->to($admin->email)
                            ->subject($subject)
                            ->html($content);
                });
            } catch (\Exception $e) {
                Log::error("Failed to send user growth report to admin: {$admin->email}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}