<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BatchCompletionStatsWidget extends BaseWidget
{
    protected ?string $heading = 'Batch Performance';
    
    protected static ?int $sort = 11;
    
    protected function getStats(): array
    {
        // Total batches
        $totalBatches = Batch::count();
        
        // Completed batches (using 'closed' as completed status)
        $completedBatches = Batch::where('status', 'closed')->count();
        
        // Active batches (using 'open' and 'full' as active statuses)
        $activeBatches = Batch::whereIn('status', ['open', 'full'])->count();
        
        // Completion rate
        $completionRate = $totalBatches > 0 ? 
            round(($completedBatches / $totalBatches) * 100, 1) : 0;
        
        // Average completion time (in days) - using updated_at as proxy for completion
        $avgCompletionTime = Batch::where('status', 'closed')
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days');
        
        $avgCompletionTime = $avgCompletionTime ? round($avgCompletionTime, 1) : 0;
        
        // Batches completed today (using updated_at as proxy)
        $completedToday = Batch::where('status', 'closed')
            ->whereDate('updated_at', today())
            ->count();
        
        // Average members per batch
        $avgMembersPerBatch = Batch::withCount('members')
            ->get()
            ->avg('members_count');
        
        $avgMembersPerBatch = $avgMembersPerBatch ? round($avgMembersPerBatch, 1) : 0;
        
        // Fill rate (how full batches get on average)
        $avgFillRate = Batch::selectRaw('AVG((SELECT COUNT(*) FROM batch_members WHERE batch_id = batches.id) / `limit` * 100) as fill_rate')
            ->where('limit', '>', 0)
            ->value('fill_rate');
        
        $avgFillRate = $avgFillRate ? round($avgFillRate, 1) : 0;
        
        return [
            Stat::make('Completion Rate', $completionRate . '%')
                ->description($completedBatches . '/' . $totalBatches . ' completed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($completionRate >= 80 ? 'success' : ($completionRate >= 60 ? 'warning' : 'danger'))
                ->chart([70, 75, 78, 82, 85, 83, 88]),
                
            Stat::make('Avg Completion Time', $avgCompletionTime . ' days')
                ->description($completedToday . ' completed today')
                ->descriptionIcon('heroicon-m-clock')
                ->color($avgCompletionTime <= 7 ? 'success' : ($avgCompletionTime <= 14 ? 'warning' : 'danger'))
                ->chart([10, 8, 9, 7, 6, 8, 7]),
                
            Stat::make('Avg Members/Batch', number_format($avgMembersPerBatch, 1))
                ->description('Average participation')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart([15, 18, 22, 20, 25, 23, 27]),
                
            Stat::make('Fill Rate', $avgFillRate . '%')
                ->description('How full batches get')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color($avgFillRate >= 80 ? 'success' : ($avgFillRate >= 60 ? 'warning' : 'danger'))
                ->chart([60, 65, 70, 75, 80, 78, 82]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
}
