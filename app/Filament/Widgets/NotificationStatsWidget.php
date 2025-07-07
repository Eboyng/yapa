<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\NotificationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.notification-stats-widget';
    
    protected static ?int $sort = 8;
    
    protected int | string | array $columnSpan = 1;
    
    public function getNotificationStats(): array
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        
        // Today's stats
        $todayStats = NotificationLog::where('created_at', '>=', $today)
            ->selectRaw('status, channel, count(*) as count')
            ->groupBy(['status', 'channel'])
            ->get()
            ->groupBy('status');
            
        // This week's stats
        $weekStats = NotificationLog::where('created_at', '>=', $thisWeek)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
            
        // Channel breakdown for today
        $channelStats = NotificationLog::where('created_at', '>=', $today)
            ->selectRaw('channel, count(*) as count')
            ->groupBy('channel')
            ->pluck('count', 'channel');
            
        // Success rate calculation
        $totalToday = NotificationLog::where('created_at', '>=', $today)->count();
        $successToday = NotificationLog::where('created_at', '>=', $today)
            ->where('status', 'sent')
            ->count();
            
        $successRate = $totalToday > 0 ? round(($successToday / $totalToday) * 100, 1) : 0;
        
        return [
            'today' => [
                'total' => $totalToday,
                'sent' => $successToday,
                'failed' => $todayStats->get('failed', collect())->sum('count'),
                'pending' => $todayStats->get('pending', collect())->sum('count'),
                'success_rate' => $successRate,
            ],
            'week' => [
                'total' => $weekStats->sum(),
                'sent' => $weekStats->get('sent', 0),
                'failed' => $weekStats->get('failed', 0),
                'pending' => $weekStats->get('pending', 0),
            ],
            'channels' => [
                'whatsapp' => $channelStats->get(NotificationLog::CHANNEL_WHATSAPP, 0),
                'sms' => $channelStats->get(NotificationLog::CHANNEL_SMS, 0),
                'email' => $channelStats->get(NotificationLog::CHANNEL_EMAIL, 0),
            ],
        ];
    }
    
    public function getRecentFailures(): array
    {
        return NotificationLog::where('status', 'failed')
            ->where('created_at', '>=', now()->subHours(24))
            ->selectRaw('error_message, count(*) as count')
            ->groupBy('error_message')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'error' => $item->error_message ? 
                        (strlen($item->error_message) > 50 ? 
                            substr($item->error_message, 0, 50) . '...' : 
                            $item->error_message
                        ) : 'Unknown error',
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }
}