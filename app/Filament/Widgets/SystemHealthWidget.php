<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\NotificationLog;
use App\Models\User;
use App\Models\Batch;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemHealthWidget extends Widget
{
    protected static string $view = 'filament.widgets.system-health-widget';
    
    protected static ?int $sort = 12;
    
    protected int | string | array $columnSpan = [
        'default' => 1,
        'sm' => 1,
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];
    
    public function getHealthMetrics(): array
    {
        $metrics = [];
        
        // Database Health
        try {
            DB::connection()->getPdo();
            $metrics['database'] = [
                'status' => 'healthy',
                'message' => 'Connected',
                'icon' => 'heroicon-o-check-circle',
                'color' => 'success'
            ];
        } catch (\Exception $e) {
            $metrics['database'] = [
                'status' => 'unhealthy',
                'message' => 'Connection failed',
                'icon' => 'heroicon-o-x-circle',
                'color' => 'danger'
            ];
        }
        
        // Cache Health
        try {
            Cache::put('health_check', 'test', 10);
            $test = Cache::get('health_check');
            $metrics['cache'] = [
                'status' => $test === 'test' ? 'healthy' : 'unhealthy',
                'message' => $test === 'test' ? 'Working' : 'Not responding',
                'icon' => $test === 'test' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle',
                'color' => $test === 'test' ? 'success' : 'danger'
            ];
        } catch (\Exception $e) {
            $metrics['cache'] = [
                'status' => 'unhealthy',
                'message' => 'Error: ' . $e->getMessage(),
                'icon' => 'heroicon-o-x-circle',
                'color' => 'danger'
            ];
        }
        
        // Queue Health (check failed jobs in last hour)
        $failedJobs = DB::table('failed_jobs')
            ->where('failed_at', '>=', now()->subHour())
            ->count();
            
        $metrics['queue'] = [
            'status' => $failedJobs < 5 ? 'healthy' : 'warning',
            'message' => $failedJobs === 0 ? 'No failed jobs' : $failedJobs . ' failed jobs (1h)',
            'icon' => $failedJobs < 5 ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle',
            'color' => $failedJobs < 5 ? 'success' : 'warning'
        ];
        
        // Notification Health (check recent notification failures)
        $failedNotifications = NotificationLog::where('status', 'failed')
            ->where('created_at', '>=', now()->subHour())
            ->count();
            
        $metrics['notifications'] = [
            'status' => $failedNotifications < 10 ? 'healthy' : 'warning',
            'message' => $failedNotifications === 0 ? 'All notifications sent' : $failedNotifications . ' failed (1h)',
            'icon' => $failedNotifications < 10 ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle',
            'color' => $failedNotifications < 10 ? 'success' : 'warning'
        ];
        
        return $metrics;
    }
    
    public function getSystemStats(): array
    {
        return [
            'active_users_today' => User::whereDate('last_login_at', today())->count(),
            'active_batches' => Batch::where('status', 'active')->count(),
            'pending_notifications' => NotificationLog::where('status', 'pending')->count(),
            'disk_usage' => $this->getDiskUsage(),
        ];
    }
    
    private function getDiskUsage(): string
    {
        try {
            $bytes = disk_free_space('/');
            $total = disk_total_space('/');
            
            if ($bytes !== false && $total !== false) {
                $used = $total - $bytes;
                $percentage = round(($used / $total) * 100, 1);
                return $percentage . '%';
            }
        } catch (\Exception $e) {
            // Ignore errors
        }
        
        return 'N/A';
    }
}