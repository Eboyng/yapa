<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Batch;
use App\Models\Transaction;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        // Get current period stats
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersThisWeek = User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        
        $activeBatches = Batch::where('status', 'active')->count();
        $completedBatchesToday = Batch::where('status', 'completed')
            ->whereDate('updated_at', today())
            ->count();
        
        $totalRevenue = WalletTransaction::where('type', 'credit')
            ->where('description', 'like', '%credit purchase%')
            ->sum('amount');
        $revenueToday = WalletTransaction::where('type', 'credit')
            ->where('description', 'like', '%credit purchase%')
            ->whereDate('created_at', today())
            ->sum('amount');
        
        $totalTransactions = Transaction::count();
        $successfulTransactions = Transaction::where('status', 'completed')->count();
        $successRate = $totalTransactions > 0 ? round(($successfulTransactions / $totalTransactions) * 100, 1) : 0;
        
        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description($newUsersToday . ' new today')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
                
            Stat::make('Active Batches', number_format($activeBatches))
                ->description($completedBatchesToday . ' completed today')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('info')
                ->chart([15, 4, 10, 2, 12, 4, 12]),
                
            Stat::make('Total Revenue', '₦' . number_format($totalRevenue, 2))
                ->description('₦' . number_format($revenueToday, 2) . ' today')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
                
            Stat::make('Success Rate', $successRate . '%')
                ->description($successfulTransactions . '/' . $totalTransactions . ' transactions')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($successRate >= 90 ? 'success' : ($successRate >= 70 ? 'warning' : 'danger'))
                ->chart([15, 4, 10, 2, 12, 4, 12]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
}