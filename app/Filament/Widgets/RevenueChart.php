<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue Trends';
    
    protected static ?string $description = 'Daily revenue from credit purchases over the last 30 days';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 2;
    
    protected function getData(): array
    {
        $data = collect();
        $labels = collect();
        
        // Get revenue data for the last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = WalletTransaction::where('type', 'credit')
                ->where('description', 'like', '%credit purchase%')
                ->whereDate('created_at', $date->toDateString())
                ->sum('amount');
            
            $data->push((float) $revenue);
            $labels->push($date->format('M j'));
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Revenue (₦)',
                    'data' => $data->toArray(),
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }
    
    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return "₦" + context.parsed.y.toLocaleString(); }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "₦" + value.toLocaleString(); }',
                    ],
                ],
            ],
            'elements' => [
                'point' => [
                    'radius' => 3,
                    'hoverRadius' => 6,
                ],
            ],
        ];
    }
}