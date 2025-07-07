<?php

namespace App\Filament\Resources\ChannelResource\Pages;

use App\Filament\Resources\ChannelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Channel;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;

class ViewChannel extends ViewRecord
{
    protected static string $resource = ChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $this->record->status === Channel::STATUS_PENDING)
                ->form([
                    Textarea::make('admin_notes')
                        ->label('Admin Notes (Optional)')
                        ->maxLength(1000),
                ])
                ->action(function (array $data): void {
                    $this->record->approve(auth()->id(), $data['admin_notes'] ?? null);
                    
                    Notification::make()
                        ->title('Channel Approved')
                        ->body("Channel '{$this->record->name}' has been approved.")
                        ->success()
                        ->send();
                        
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            Actions\Action::make('reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => $this->record->status === Channel::STATUS_PENDING)
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->required()
                        ->maxLength(500),
                    Textarea::make('admin_notes')
                        ->label('Admin Notes (Optional)')
                        ->maxLength(1000),
                ])
                ->action(function (array $data): void {
                    $this->record->reject($data['rejection_reason'], $data['admin_notes'] ?? null);
                    
                    Notification::make()
                        ->title('Channel Rejected')
                        ->body("Channel '{$this->record->name}' has been rejected.")
                        ->warning()
                        ->send();
                        
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            Actions\Action::make('suspend')
                ->icon('heroicon-o-pause-circle')
                ->color('warning')
                ->visible(fn (): bool => $this->record->status === Channel::STATUS_APPROVED)
                ->form([
                    Textarea::make('admin_notes')
                        ->label('Suspension Reason')
                        ->required()
                        ->maxLength(1000),
                ])
                ->action(function (array $data): void {
                    $this->record->suspend($data['admin_notes']);
                    
                    Notification::make()
                        ->title('Channel Suspended')
                        ->body("Channel '{$this->record->name}' has been suspended.")
                        ->warning()
                        ->send();
                        
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}