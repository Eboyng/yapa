<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Number;

class UserStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    
    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // Calculate statistics
        $totalUsers = User::count();
        $todayUsers = User::whereDate('created_at', $today)->count();
        $weeklyUsers = User::where('created_at', '>=', $thisWeek)->count();
        $monthlyUsers = User::where('created_at', '>=', $thisMonth)->count();
        
        // Calculate growth percentages
        $lastWeekUsers = User::whereBetween('created_at', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ])->count();
        
        $lastMonthUsersCount = User::whereBetween('created_at', [
            $lastMonth,
            $lastMonth->copy()->endOfMonth()
        ])->count();
        
        $weeklyGrowth = $lastWeekUsers > 0 
            ? round((($weeklyUsers - $lastWeekUsers) / $lastWeekUsers) * 100, 1)
            : 100;
            
        $monthlyGrowth = $lastMonthUsersCount > 0
            ? round((($monthlyUsers - $lastMonthUsersCount) / $lastMonthUsersCount) * 100, 1)
            : 100;
        
        // Get latest users for description
        $latestUsers = User::latest()
            ->take(3)
            ->get()
            ->map(fn ($user) => $user->name)
            ->join(', ');
        
        // Active users (logged in within last 7 days)
        $activeUsers = User::where('last_login_at', '>=', Carbon::now()->subDays(7))->count();
        $activePercentage = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0;
        
        // Verified users
        $verifiedUsers = User::whereNotNull('email_verified_at')
            ->whereNotNull('whatsapp_verified_at')
            ->count();
        $verifiedPercentage = $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100, 1) : 0;
        
        return [
            Stat::make('Total Users', Number::format($totalUsers))
                ->description("Latest: {$latestUsers}")
                ->descriptionIcon('heroicon-m-users')
                ->chart($this->getMonthlyChart())
                ->color('primary'),
                
            Stat::make('Today\'s Registrations', Number::format($todayUsers))
                ->description($todayUsers > 0 ? 'New users today' : 'No new users yet')
                ->descriptionIcon($todayUsers > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus')
                ->color($todayUsers > 0 ? 'success' : 'gray')
                ->extraAttributes([
                    'class' => 'ring-1 ring-green-200 dark:ring-green-800',
                ]),
                
            Stat::make('This Week', Number::format($weeklyUsers))
                ->description("{$weeklyGrowth}% from last week")
                ->descriptionIcon($weeklyGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($this->getWeeklyChart())
                ->color($weeklyGrowth >= 0 ? 'success' : 'danger'),
                
            Stat::make('This Month', Number::format($monthlyUsers))
                ->description("{$monthlyGrowth}% from last month")
                ->descriptionIcon($monthlyGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($this->getDailyChart())
                ->color($monthlyGrowth >= 0 ? 'success' : 'danger'),
                
            Stat::make('Active Users', Number::format($activeUsers))
                ->description("{$activePercentage}% of total users")
                ->descriptionIcon('heroicon-m-signal')
                ->color('info')
                ->extraAttributes([
                    'class' => 'ring-1 ring-blue-200 dark:ring-blue-800',
                ]),
                
            Stat::make('Fully Verified', Number::format($verifiedUsers))
                ->description("{$verifiedPercentage}% verified")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->extraAttributes([
                    'class' => 'ring-1 ring-emerald-200 dark:ring-emerald-800',
                ]),
        ];
    }
    
    protected function getMonthlyChart(): array
    {
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $data[] = $count;
        }
        
        return $data;
    }
    
    protected function getWeeklyChart(): array
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $count = User::whereDate('created_at', $day)->count();
            $data[] = $count;
        }
        
        return $data;
    }
    
    protected function getDailyChart(): array
    {
        $data = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $count = User::whereDate('created_at', $day)->count();
            $data[] = $count;
        }
        
        return $data;
    }
}