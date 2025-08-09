<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Widgets\UserStatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->label('New User'),
                
            Actions\Action::make('export_all_vcf')
                ->label('Export All WhatsApp')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return $this->exportAllWhatsAppNumbers();
                })
                ->requiresConfirmation()
                ->modalHeading('Export All WhatsApp Numbers')
                ->modalDescription('This will export all users\' WhatsApp numbers as a VCF file.')
                ->modalSubmitActionLabel('Export'),
                
            Actions\Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->redirect(request()->header('Referer'))),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsOverview::class,
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Users')
                ->icon('heroicon-o-users')
                ->badge($this->getAllUsersCount())
                ->badgeColor('primary'),
                
            'today' => Tab::make('Today')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereDate('created_at', Carbon::today())
                )
                ->badge($this->getTodayUsersCount())
                ->badgeColor('success'),
                
            'this_week' => Tab::make('This Week')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ])
                )
                ->badge($this->getWeeklyUsersCount())
                ->badgeColor('info'),
                
            'this_month' => Tab::make('This Month')
                ->icon('heroicon-o-calendar-days')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                )
                ->badge($this->getMonthlyUsersCount())
                ->badgeColor('warning'),
                
            'verified' => Tab::make('Verified')
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereNotNull('email_verified_at')
                        ->whereNotNull('whatsapp_verified_at')
                )
                ->badge($this->getVerifiedUsersCount())
                ->badgeColor('success'),
                
            'unverified' => Tab::make('Unverified')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where(function ($q) {
                        $q->whereNull('email_verified_at')
                            ->orWhereNull('whatsapp_verified_at');
                    })
                )
                ->badge($this->getUnverifiedUsersCount())
                ->badgeColor('danger'),
                
            'flagged' => Tab::make('Flagged')
                ->icon('heroicon-o-flag')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('is_flagged_for_ads', true)
                )
                ->badge($this->getFlaggedUsersCount())
                ->badgeColor('danger'),
                
            'banned' => Tab::make('Banned')
                ->icon('heroicon-o-no-symbol')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('is_banned_from_batches', true)
                )
                ->badge($this->getBannedUsersCount())
                ->badgeColor('danger'),
        ];
    }
    
    // Count methods for badges
    protected function getAllUsersCount(): int
    {
        return \App\Models\User::count();
    }
    
    protected function getTodayUsersCount(): int
    {
        return \App\Models\User::whereDate('created_at', Carbon::today())->count();
    }
    
    protected function getWeeklyUsersCount(): int
    {
        return \App\Models\User::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
    }
    
    protected function getMonthlyUsersCount(): int
    {
        return \App\Models\User::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }
    
    protected function getVerifiedUsersCount(): int
    {
        return \App\Models\User::whereNotNull('email_verified_at')
            ->whereNotNull('whatsapp_verified_at')
            ->count();
    }
    
    protected function getUnverifiedUsersCount(): int
    {
        return \App\Models\User::where(function ($q) {
            $q->whereNull('email_verified_at')
                ->orWhereNull('whatsapp_verified_at');
        })->count();
    }
    
    protected function getFlaggedUsersCount(): int
    {
        return \App\Models\User::where('is_flagged_for_ads', true)->count();
    }
    
    protected function getBannedUsersCount(): int
    {
        return \App\Models\User::where('is_banned_from_batches', true)->count();
    }
    
    // Export functionality
    protected function exportAllWhatsAppNumbers()
    {
        $users = \App\Models\User::whereNotNull('whatsapp_number')->get();
        
        $vcfContent = "";
        
        foreach ($users as $user) {
            $vcfContent .= "BEGIN:VCARD\n";
            $vcfContent .= "VERSION:3.0\n";
            $vcfContent .= "FN:{$user->name}\n";
            $vcfContent .= "TEL;TYPE=CELL:{$user->whatsapp_number}\n";
            $vcfContent .= "EMAIL:{$user->email}\n";
            
            if ($user->location) {
                $vcfContent .= "ADR;TYPE=HOME:;;{$user->location};;;;\n";
            }
            
            $vcfContent .= "NOTE:Registered " . $user->created_at->format('d M Y') . "\n";
            $vcfContent .= "END:VCARD\n";
        }
        
        $fileName = 'all_users_whatsapp_' . now()->format('Y-m-d_His') . '.vcf';
        
        return response()->streamDownload(
            function () use ($vcfContent) {
                echo $vcfContent;
            },
            $fileName,
            [
                'Content-Type' => 'text/vcard',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }
}