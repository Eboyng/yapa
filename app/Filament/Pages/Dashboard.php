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
            StatsOverviewWidget::class,
            KudismsBalanceWidget::class,
            ChannelAdStatsWidget::class,
            WithdrawalStatsWidget::class,
            CreditUsageStatsWidget::class,
            WalletOverviewWidget::class,
            BatchCompletionStatsWidget::class,
            UserRegistrationChart::class,
            RevenueChart::class,
            BatchStatusChart::class,
            RecentTransactionsWidget::class,
            TopPerformingBatchesWidget::class,
            SystemHealthWidget::class,
            NotificationStatsWidget::class,
        ];
    }
    
    public function getColumns(): int | string | array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }
}