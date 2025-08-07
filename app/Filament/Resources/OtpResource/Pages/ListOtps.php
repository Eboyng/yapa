<?php

namespace App\Filament\Resources\OtpResource\Pages;

use App\Filament\Resources\OtpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Models\Otp;
use Filament\Notifications\Notification;

class ListOtps extends ListRecords
{
    protected static string $resource = OtpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cleanup_expired')
                ->label('Cleanup Expired')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cleanup Expired OTPs')
                ->modalDescription('This will permanently delete all expired OTP records. This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, cleanup')
                ->action(function () {
                    $deletedCount = Otp::cleanupExpired();
                    
                    Notification::make()
                        ->title('Cleanup completed')
                        ->body("Deleted {$deletedCount} expired OTP records.")
                        ->success()
                        ->send();
                }),
                
            Action::make('cleanup_verified')
                ->label('Cleanup Verified')
                ->icon('heroicon-o-check-circle')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Cleanup Verified OTPs')
                ->modalDescription('This will permanently delete all verified OTP records. This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, cleanup')
                ->action(function () {
                    $deletedCount = Otp::where('verified', true)->delete();
                    
                    Notification::make()
                        ->title('Cleanup completed')
                        ->body("Deleted {$deletedCount} verified OTP records.")
                        ->success()
                        ->send();
                }),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            // Add widgets here if needed
        ];
    }
}