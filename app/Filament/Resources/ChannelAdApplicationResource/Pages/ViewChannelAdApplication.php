<?php

namespace App\Filament\Resources\ChannelAdApplicationResource\Pages;

use App\Filament\Resources\ChannelAdApplicationResource;
use App\Models\ChannelAdApplication;
use App\Models\AuditLog;
use App\Services\TransactionService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;

class ViewChannelAdApplication extends ViewRecord
{
    protected static string $resource = ChannelAdApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\Action::make('approve')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn (ChannelAdApplication $record): bool => $record->status === ChannelAdApplication::STATUS_PENDING)
                ->form([
                    Textarea::make('admin_notes')
                        ->label('Admin Notes (Optional)')
                        ->maxLength(500)
                        ->rows(3),
                ])
                ->requiresConfirmation()
                ->modalHeading('Approve Application')
                ->modalDescription('Are you sure you want to approve this channel ad application?')
                ->action(function (ChannelAdApplication $record, array $data): void {
                    $record->approve($data['admin_notes'] ?? null);
                    
                    // Log the action
                    AuditLog::log(
                        auth()->id(),
                        $record->channel->user_id,
                        'channel_ad_application_approved',
                        ['status' => ChannelAdApplication::STATUS_PENDING],
                        ['status' => ChannelAdApplication::STATUS_APPROVED],
                        "Approved channel ad application for: {$record->channelAd->title}"
                    );
                    
                    Notification::make()
                        ->title('Application Approved')
                        ->body("Application for '{$record->channelAd->title}' has been approved.")
                        ->success()
                        ->send();
                }),
            
            Actions\Action::make('reject')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn (ChannelAdApplication $record): bool => $record->status === ChannelAdApplication::STATUS_PENDING)
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->required()
                        ->maxLength(500)
                        ->rows(3),
                    Textarea::make('admin_notes')
                        ->label('Admin Notes (Optional)')
                        ->maxLength(500)
                        ->rows(2),
                ])
                ->requiresConfirmation()
                ->modalHeading('Reject Application')
                ->modalDescription('Are you sure you want to reject this channel ad application?')
                ->action(function (ChannelAdApplication $record, array $data): void {
                    $record->reject($data['rejection_reason'], $data['admin_notes'] ?? null);
                    
                    // Log the action
                    AuditLog::log(
                        auth()->id(),
                        $record->channel->user_id,
                        'channel_ad_application_rejected',
                        ['status' => ChannelAdApplication::STATUS_PENDING],
                        ['status' => ChannelAdApplication::STATUS_REJECTED],
                        $data['rejection_reason']
                    );
                    
                    Notification::make()
                        ->title('Application Rejected')
                        ->body("Application for '{$record->channelAd->title}' has been rejected.")
                        ->warning()
                        ->send();
                }),
            
            Actions\Action::make('complete')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (ChannelAdApplication $record): bool => $record->status === ChannelAdApplication::STATUS_PROOF_SUBMITTED)
                ->requiresConfirmation()
                ->modalHeading('Complete Application & Release Payment')
                ->modalDescription('This will release the escrow payment to the channel. Are you sure the proof is satisfactory?')
                ->action(function (ChannelAdApplication $record): void {
                    try {
                        $transactionService = app(TransactionService::class);
                        
                        // Release escrow payment
                        if ($record->escrow_transaction_id) {
                            $transaction = $transactionService->releaseEscrow(
                                $record->escrow_transaction_id,
                                $record->channel->user,
                                "Payment for channel ad: {$record->channelAd->title}"
                            );
                        }
                        
                        $record->complete();
                        
                        // Log the action
                        AuditLog::log(
                            auth()->id(),
                            $record->channel->user_id,
                            'channel_ad_application_completed',
                            ['status' => ChannelAdApplication::STATUS_PROOF_SUBMITTED],
                            ['status' => ChannelAdApplication::STATUS_COMPLETED],
                            "Completed channel ad application and released payment for: {$record->channelAd->title}"
                        );
                        
                        Notification::make()
                            ->title('Application Completed')
                            ->body("Payment of ₦{$record->channelAd->payment_per_channel} has been released to the channel.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->body('Failed to complete application: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            
            Actions\Action::make('dispute')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->visible(fn (ChannelAdApplication $record): bool => in_array($record->status, [ChannelAdApplication::STATUS_APPROVED, ChannelAdApplication::STATUS_PROOF_SUBMITTED]))
                ->form([
                    Textarea::make('dispute_reason')
                        ->label('Dispute Reason')
                        ->required()
                        ->maxLength(1000)
                        ->rows(4),
                ])
                ->requiresConfirmation()
                ->modalHeading('Mark as Disputed')
                ->modalDescription('This will mark the application as disputed and require resolution.')
                ->action(function (ChannelAdApplication $record, array $data): void {
                    $record->dispute($data['dispute_reason']);
                    
                    // Log the action
                    AuditLog::log(
                        auth()->id(),
                        $record->channel->user_id,
                        'channel_ad_application_disputed',
                        ['status' => $record->getOriginal('status')],
                        ['status' => ChannelAdApplication::STATUS_DISPUTED],
                        $data['dispute_reason']
                    );
                    
                    Notification::make()
                        ->title('Application Disputed')
                        ->body("Application for '{$record->channelAd->title}' has been marked as disputed.")
                        ->warning()
                        ->send();
                }),
            
            Actions\Action::make('resolve_dispute')
                ->icon('heroicon-o-scale')
                ->color('info')
                ->visible(fn (ChannelAdApplication $record): bool => $record->status === ChannelAdApplication::STATUS_DISPUTED)
                ->form([
                    Textarea::make('dispute_resolution')
                        ->label('Dispute Resolution')
                        ->required()
                        ->maxLength(1000)
                        ->rows(4),
                    Textarea::make('resolution_action')
                        ->label('Resolution Action')
                        ->helperText('Describe what action was taken (e.g., payment released, payment refunded, etc.)')
                        ->required()
                        ->maxLength(500)
                        ->rows(2),
                ])
                ->requiresConfirmation()
                ->modalHeading('Resolve Dispute')
                ->modalDescription('This will resolve the dispute and update the application status.')
                ->action(function (ChannelAdApplication $record, array $data): void {
                    $record->resolveDispute($data['dispute_resolution']);
                    
                    // Log the action
                    AuditLog::log(
                        auth()->id(),
                        $record->channel->user_id,
                        'channel_ad_application_dispute_resolved',
                        ['status' => ChannelAdApplication::STATUS_DISPUTED],
                        ['status' => ChannelAdApplication::STATUS_COMPLETED],
                        $data['dispute_resolution'] . ' | Action: ' . $data['resolution_action']
                    );
                    
                    Notification::make()
                        ->title('Dispute Resolved')
                        ->body("Dispute for '{$record->channelAd->title}' has been resolved.")
                        ->info()
                        ->send();
                }),
        ];
    }
}