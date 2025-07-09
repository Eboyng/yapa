<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\WithdrawalRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WithdrawalStatsWidget extends BaseWidget
{
    protected ?string $heading = 'Withdrawal Requests';
    
    protected static ?int $sort = 10;
    
    protected function getStats(): array
    {
        // Get current period stats
        $totalWithdrawals = WithdrawalRequest::count();
        $pendingWithdrawals = WithdrawalRequest::where('status', 'pending')->count();
        $completedWithdrawals = WithdrawalRequest::where('status', 'completed')->count();
        $failedWithdrawals = WithdrawalRequest::where('status', 'failed')->count();
        
        // Withdrawals today
        $withdrawalsToday = WithdrawalRequest::whereDate('created_at', today())->count();
        
        // Total withdrawal amount (completed)
        $totalWithdrawalAmount = WithdrawalRequest::where('status', 'completed')
            ->sum('amount');
        
        // Pending withdrawal amount
        $pendingWithdrawalAmount = WithdrawalRequest::where('status', 'pending')
            ->sum('amount');
        
        // Success rate
        $successRate = $totalWithdrawals > 0 ? 
            round(($completedWithdrawals / $totalWithdrawals) * 100, 1) : 0;
        
        return [
            Stat::make('Total Requests', number_format($totalWithdrawals))
                ->description($withdrawalsToday . ' new today')
                ->descriptionIcon('heroicon-m-arrow-up-tray')
                ->color('info')
                ->chart([5, 8, 12, 6, 15, 9, 11]),
                
            Stat::make('Pending Review', number_format($pendingWithdrawals))
                ->description('₦' . number_format($pendingWithdrawalAmount, 2) . ' pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([2, 4, 3, 6, 2, 5, 4]),
                
            Stat::make('Success Rate', $successRate . '%')
                ->description($completedWithdrawals . '/' . $totalWithdrawals . ' completed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($successRate >= 90 ? 'success' : ($successRate >= 70 ? 'warning' : 'danger'))
                ->chart([85, 88, 92, 89, 94, 91, 95]),
                
            Stat::make('Total Paid Out', '₦' . number_format($totalWithdrawalAmount, 2))
                ->description('Successfully withdrawn')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([1000, 1200, 1500, 1800, 2100, 2400, 2700]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
}
