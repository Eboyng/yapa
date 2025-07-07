<?php

namespace App\Filament\Resources\ChannelAdResource\Pages;

use App\Filament\Resources\ChannelAdResource;
use App\Models\ChannelAd;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class ViewChannelAd extends ViewRecord
{
    protected static string $resource = ChannelAdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\Action::make('activate')
                ->icon('heroicon-o-play')
                ->color('success')
                ->visible(fn (ChannelAd $record): bool => $record->status === ChannelAd::STATUS_DRAFT || $record->status === ChannelAd::STATUS_PAUSED)
                ->requiresConfirmation()
                ->modalHeading('Activate Channel Ad')
                ->modalDescription('Are you sure you want to activate this channel ad? It will become available for channel applications.')
                ->action(function (ChannelAd $record): void {
                    $oldStatus = $record->status;
                    $record->update(['status' => ChannelAd::STATUS_ACTIVE]);
                    
                    // Log the action
                    AuditLog::log(
                        auth()->id(),
                        null,
                        'channel_ad_activated',
                        ['status' => $oldStatus],
                        ['status' => ChannelAd::STATUS_ACTIVE],
                        "Activated channel ad: {$record->title}"
                    );
                    
                    Notification::make()
                        ->title('Channel Ad Activated')
                        ->body("Channel ad '{$record->title}' is now active and available for applications.")
                        ->success()
                        ->send();
                }),
            
            Actions\Action::make('pause')
                ->icon('heroicon-o-pause')
                ->color('warning')
                ->visible(fn (ChannelAd $record): bool => $record->status === ChannelAd::STATUS_ACTIVE)
                ->form([
                    Textarea::make('reason')
                        ->label('Reason for pausing (optional)')
                        ->maxLength(500),
                ])
                ->requiresConfirmation()
                ->modalHeading('Pause Channel Ad')
                ->modalDescription('Are you sure you want to pause this channel ad? No new applications will be accepted.')
                ->action(function (ChannelAd $record, array $data): void {
                    $oldStatus = $record->status;
                    $record->update(['status' => ChannelAd::STATUS_PAUSED]);
                    
                    // Log the action
                    AuditLog::log(
                        auth()->id(),
                        null,
                        'channel_ad_paused',
                        ['status' => $oldStatus],
                        ['status' => ChannelAd::STATUS_PAUSED],
                        $data['reason'] ?: "Paused channel ad: {$record->title}"
                    );
                    
                    Notification::make()
                        ->title('Channel Ad Paused')
                        ->body("Channel ad '{$record->title}' has been paused.")
                        ->warning()
                        ->send();
                }),
            
            Actions\Action::make('complete')
                ->icon('heroicon-o-check-circle')
                ->color('info')
                ->visible(fn (ChannelAd $record): bool => in_array($record->status, [ChannelAd::STATUS_ACTIVE, ChannelAd::STATUS_PAUSED]))
                ->form([
                    Textarea::make('completion_notes')
                        ->label('Completion Notes (optional)')
                        ->maxLength(500),
                ])
                ->requiresConfirmation()
                ->modalHeading('Complete Channel Ad')
                ->modalDescription('Are you sure you want to mark this channel ad as completed? This action cannot be undone.')
                ->action(function (ChannelAd $record, array $data): void {
                    $oldStatus = $record->status;
                    $record->update(['status' => ChannelAd::STATUS_COMPLETED]);
                    
                    // Log the action
                    AuditLog::log(
                        auth()->id(),
                        null,
                        'channel_ad_completed',
                        ['status' => $oldStatus],
                        ['status' => ChannelAd::STATUS_COMPLETED],
                        $data['completion_notes'] ?: "Completed channel ad: {$record->title}"
                    );
                    
                    Notification::make()
                        ->title('Channel Ad Completed')
                        ->body("Channel ad '{$record->title}' has been marked as completed.")
                        ->info()
                        ->send();
                }),
            
            Actions\Action::make('cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (ChannelAd $record): bool => !in_array($record->status, [ChannelAd::STATUS_COMPLETED, ChannelAd::STATUS_CANCELLED, ChannelAd::STATUS_EXPIRED]))
                ->form([
                    Textarea::make('cancellation_reason')
                        ->label('Cancellation Reason')
                        ->required()
                        ->maxLength(500),
                ])
                ->requiresConfirmation()
                ->modalHeading('Cancel Channel Ad')
                ->modalDescription('Are you sure you want to cancel this channel ad? This action cannot be undone and may affect ongoing applications.')
                ->action(function (ChannelAd $record, array $data): void {
                    $oldStatus = $record->status;
                    $record->update(['status' => ChannelAd::STATUS_CANCELLED]);
                    
                    // Log the action
                    AuditLog::log(
                        auth()->id(),
                        null,
                        'channel_ad_cancelled',
                        ['status' => $oldStatus],
                        ['status' => ChannelAd::STATUS_CANCELLED],
                        $data['cancellation_reason']
                    );
                    
                    Notification::make()
                        ->title('Channel Ad Cancelled')
                        ->body("Channel ad '{$record->title}' has been cancelled.")
                        ->danger()
                        ->send();
                }),
        ];
    }
}