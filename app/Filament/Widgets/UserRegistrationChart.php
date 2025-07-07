<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserRegistrationChart extends ChartWidget
{
    protected static ?string $heading = 'User Registrations';
    
    protected static ?string $description = 'Daily user registrations over the last 30 days';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 2;
    
    protected function getData(): array
    {
        $data = collect();
        $labels = collect();
        
        // Get data for the last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = User::whereDate('created_at', $date->toDateString())->count();
            
            $data->push($count);
            $labels->push($date->format('M j'));
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $data->toArray(),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
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
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
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