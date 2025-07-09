<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WalletOverviewWidget extends BaseWidget
{
    protected ?string $heading = 'Wallet Overview';
    
    protected static ?int $sort = 6;
    
    protected function getStats(): array
    {
        // Total wallet balances across all users (sum all wallet types)
        $totalWalletBalance = Wallet::where('is_active', true)->sum('balance');
        
        // Users with positive balances (any wallet type)
        $usersWithBalance = User::whereHas('wallets', function($query) {
            $query->where('balance', '>', 0)->where('is_active', true);
        })->count();
        
        // Average wallet balance
        $totalUsers = User::count();
        $avgWalletBalance = $totalUsers > 0 ? 
            round($totalWalletBalance / $totalUsers, 2) : 0;
        
        // Pending transactions (last 24 hours)
        $pendingTransactions = WalletTransaction::where('status', 'pending')
            ->where('created_at', '>=', now()->subDay())
            ->count();
        
        // Total transaction volume today
        $transactionVolumeToday = WalletTransaction::whereDate('created_at', today())
            ->sum('amount');
        
        // Wallet activity rate (users who transacted this week)
        $activeWallets = WalletTransaction::where('created_at', '>=', now()->startOfWeek())
            ->distinct('user_id')
            ->count();
        
        $activityRate = $totalUsers > 0 ? 
            round(($activeWallets / $totalUsers) * 100, 1) : 0;
        
        return [
            Stat::make('Total Wallet Balance', '₦' . number_format($totalWalletBalance, 2))
                ->description($usersWithBalance . ' users with balance')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('success')
                ->chart([1000, 1200, 1500, 1300, 1800, 1600, 2000]),
                
            Stat::make('Average Balance', '₦' . number_format($avgWalletBalance, 2))
                ->description('Per user average')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->chart([50, 60, 75, 65, 90, 80, 100]),
                
            Stat::make('Wallet Activity', $activityRate . '%')
                ->description($activeWallets . ' active this week')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($activityRate >= 30 ? 'success' : ($activityRate >= 15 ? 'warning' : 'danger'))
                ->chart([15, 20, 25, 30, 35, 32, 38]),
                
            Stat::make('Transaction Volume', '₦' . number_format($transactionVolumeToday, 2))
                ->description('Today\'s total volume')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning')
                ->chart([500, 750, 600, 900, 1200, 800, 1100]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
}
