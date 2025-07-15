<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\UserRegistrationChart;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\BatchStatusChart;
use App\Filament\Widgets\RecentTransactionsWidget;
use App\Filament\Widgets\TopPerformingBatchesWidget;
use App\Filament\Widgets\KudismsBalanceWidget;
use App\Filament\Widgets\SystemHealthWidget;
use App\Filament\Widgets\NotificationStatsWidget;
use App\Filament\Widgets\ChannelAdStatsWidget;
use App\Filament\Widgets\WithdrawalStatsWidget;
use App\Filament\Widgets\CreditUsageStatsWidget;
use App\Filament\Widgets\WalletOverviewWidget;
use App\Filament\Widgets\BatchCompletionStatsWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.pages.dashboard';
    
    public function getWidgets(): array
    {
        return [
            // Primary Stats Row (Full Width)
            StatsOverviewWidget::class,
            
            // Balance Widget (Full Width)
            KudismsBalanceWidget::class,
            
            // Secondary Stats Row (2x2 Grid)
            WithdrawalStatsWidget::class,
            CreditUsageStatsWidget::class,
            WalletOverviewWidget::class,
            
            // Charts Row (2+1 Layout)
            UserRegistrationChart::class,
            RevenueChart::class,
            BatchStatusChart::class,
            
            // Additional Stats
            BatchCompletionStatsWidget::class,
            
            // Data Tables and Lists
            RecentTransactionsWidget::class,
            TopPerformingBatchesWidget::class,
            
            // System Monitoring
            SystemHealthWidget::class,
            NotificationStatsWidget::class,
        ];
    }
    
    // public function getColumns(): int | string | array
    // {
    //     return [
    //         'default' => 2,
    //         'sm' => 2,
    //         'md' => 3,
    //         'lg' => 4,
    //         'xl' => 4,
    //         '2xl' => 4,
    //     ];
    // }
}