<?php

namespace App\Filament\Resources\BatchResource\Pages;

use App\Filament\Resources\BatchResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;

class ViewBatch extends ViewRecord
{
    protected static string $resource = BatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('close_batch')
                ->label('Close Batch')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->visible(fn () => $this->record->status === 'active')
                ->requiresConfirmation()
                ->modalHeading('Close Batch')
                ->modalDescription('Are you sure you want to close this batch? This action cannot be undone.')
                ->form([
                    Textarea::make('reason')
                        ->label('Reason for closing')
                        ->required()
                        ->rows(3)
                        ->placeholder('Please provide a reason for closing this batch...')
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'closed',
                        'closed_at' => now(),
                    ]);

                    // Log the action
                    AuditLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'batch_closed',
                        'details' => 'Closed batch: ' . $this->record->name . ' (ID: ' . $this->record->id . ') - Reason: ' . $data['reason'],
                        'timestamp' => now(),
                    ]);

                    // Send notification to batch members (implement notification logic here)
                    // NotificationService::sendBatchClosedNotification($this->record, $data['reason']);

                    Notification::make()
                        ->title('Batch closed successfully')
                        ->success()
                        ->send();
                }),
        ];
    }
}