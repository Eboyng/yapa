<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\WalletTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreditUsageStatsWidget extends BaseWidget
{
    protected ?string $heading = 'Credit Usage Analytics';
    
    protected static ?int $sort = 11;
    
    protected function getStats(): array
    {
        // Total credits purchased
        $totalCreditsPurchased = WalletTransaction::where('type', 'credit')
            ->where('description', 'like', '%credit purchase%')
            ->sum('amount');
        
        // Credits purchased today
        $creditsToday = WalletTransaction::where('type', 'credit')
            ->where('description', 'like', '%credit purchase%')
            ->whereDate('created_at', today())
            ->sum('amount');
        
        // Total credits spent (debits)
        $totalCreditsSpent = WalletTransaction::where('type', 'debit')
            ->sum('amount');
        
        // Credits spent today
        $creditsSpentToday = WalletTransaction::where('type', 'debit')
            ->whereDate('created_at', today())
            ->sum('amount');
        
        // Average credit purchase per user
        $totalUsers = User::count();
        $avgCreditPerUser = $totalUsers > 0 ? 
            round($totalCreditsPurchased / $totalUsers, 2) : 0;
        
        // Top spenders this month
        $topSpendersCount = WalletTransaction::where('type', 'debit')
            ->where('created_at', '>=', now()->startOfMonth())
            ->distinct('user_id')
            ->count();
        
        return [
            Stat::make('Total Credits Purchased', '₦' . number_format($totalCreditsPurchased, 2))
                ->description('₦' . number_format($creditsToday, 2) . ' today')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('success')
                ->chart([500, 750, 600, 900, 1200, 800, 1100]),
                
            Stat::make('Total Credits Spent', '₦' . number_format($totalCreditsSpent, 2))
                ->description('₦' . number_format($creditsSpentToday, 2) . ' today')
                ->descriptionIcon('heroicon-m-minus-circle')
                ->color('warning')
                ->chart([400, 650, 500, 800, 1000, 700, 950]),
                
            Stat::make('Avg Credits/User', '₦' . number_format($avgCreditPerUser, 2))
                ->description('Per registered user')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('info')
                ->chart([50, 75, 60, 90, 120, 80, 110]),
                
            Stat::make('Active Spenders', number_format($topSpendersCount))
                ->description('Users who spent this month')
                ->descriptionIcon('heroicon-m-fire')
                ->color('danger')
                ->chart([20, 35, 28, 45, 60, 40, 55]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
}
