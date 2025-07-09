<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ChannelAdApplication;
use App\Models\ChannelAd;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChannelAdStatsWidget extends BaseWidget
{
    protected static ?int $sort = 9;
    
    protected function getStats(): array
    {
        // Get current period stats
        $totalApplications = ChannelAdApplication::count();
        $pendingApplications = ChannelAdApplication::where('status', ChannelAdApplication::STATUS_PENDING)->count();
        $approvedApplications = ChannelAdApplication::where('status', ChannelAdApplication::STATUS_APPROVED)->count();
        $rejectedApplications = ChannelAdApplication::where('status', ChannelAdApplication::STATUS_REJECTED)->count();
        
        // Applications today
        $applicationsToday = ChannelAdApplication::whereDate('created_at', today())->count();
        
        // Total escrow amount
        $totalEscrowAmount = ChannelAdApplication::whereIn('status', [
            ChannelAdApplication::STATUS_APPROVED,
            ChannelAdApplication::STATUS_PROOF_SUBMITTED
        ])->sum('escrow_amount');
        
        // Approval rate
        $approvalRate = $totalApplications > 0 ? 
            round(($approvedApplications / $totalApplications) * 100, 1) : 0;
        
        // Active channel ads
        $activeChannelAds = ChannelAd::where('status', 'active')->count();
        
        return [
            Stat::make('Total Applications', number_format($totalApplications))
                ->description($applicationsToday . ' new today')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([7, 12, 8, 15, 10, 18, 14]),
                
            Stat::make('Pending Review', number_format($pendingApplications))
                ->description('Awaiting admin action')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([3, 5, 2, 8, 4, 6, 5]),
                
            Stat::make('Approval Rate', $approvalRate . '%')
                ->description($approvedApplications . '/' . $totalApplications . ' approved')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($approvalRate >= 80 ? 'success' : ($approvalRate >= 60 ? 'warning' : 'danger'))
                ->chart([60, 70, 75, 80, 85, 82, 88]),
                
            Stat::make('Total Escrow', '₦' . number_format($totalEscrowAmount, 2))
                ->description('Funds in escrow')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([100, 150, 200, 180, 220, 250, 280]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
}
