<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class BatchStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Batch Status Distribution';
    
    protected static ?string $description = 'Current distribution of batch statuses';
    
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 1;
    
    protected function getData(): array
    {
        $statusCounts = Batch::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        $labels = [];
        $data = [];
        $colors = [];
        
        $statusColors = [
            'pending' => '#F59E0B',
            'active' => '#10B981',
            'completed' => '#3B82F6',
            'cancelled' => '#EF4444',
            'expired' => '#6B7280',
        ];
        
        foreach ($statusCounts as $status => $count) {
            $labels[] = ucfirst($status);
            $data[] = $count;
            $colors[] = $statusColors[$status] ?? '#6B7280';
        }
        
        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }
    
    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }
}