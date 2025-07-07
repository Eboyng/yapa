<?php

namespace App\Filament\Resources\WithdrawalResource\Pages;

use App\Filament\Resources\WithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TransactionService;

class ViewWithdrawal extends ViewRecord
{
    protected static string $resource = WithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $this->record->status === Transaction::STATUS_PENDING)
                ->requiresConfirmation()
                ->modalHeading('Approve Withdrawal Request')
                ->modalDescription('Are you sure you want to approve this withdrawal request? This action cannot be undone.')
                ->action(function () {
                    try {
                        DB::transaction(function () {
                            $this->record->update([
                                'status' => Transaction::STATUS_COMPLETED,
                                'processed_at' => now(),
                            ]);
                            
                            Log::info('Withdrawal approved by admin', [
                                'transaction_id' => $this->record->id,
                                'user_id' => $this->record->user_id,
                                'amount' => $this->record->amount,
                                'admin_id' => auth()->id(),
                            ]);
                        });
                        
                        Notification::make()
                            ->title('Withdrawal Approved')
                            ->body('The withdrawal request has been approved successfully.')
                            ->success()
                            ->send();
                            
                        $this->redirect($this->getResource()::getUrl('index'));
                            
                    } catch (\Exception $e) {
                        Log::error('Failed to approve withdrawal', [
                            'transaction_id' => $this->record->id,
                            'error' => $e->getMessage(),
                        ]);
                        
                        Notification::make()
                            ->title('Error')
                            ->body('Failed to approve withdrawal: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
                
            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => in_array($this->record->status, [Transaction::STATUS_PENDING, Transaction::STATUS_PROCESSING]))
                ->form([
                    Forms\Components\Textarea::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->placeholder('Please provide a reason for rejecting this withdrawal...')
                        ->required()
                        ->maxLength(500),
                ])
                ->action(function (array $data) {
                    try {
                        DB::transaction(function () use ($data) {
                            // Refund the amount back to user's earnings wallet
                            $transactionService = app(TransactionService::class);
                            $transactionService->credit(
                                $this->record->user_id,
                                $this->record->amount,
                                'earnings',
                                Transaction::CATEGORY_REFUND,
                                'Withdrawal request rejected: ' . $data['rejection_reason'],
                                $this->record->id
                            );
                            
                            $this->record->update([
                                'status' => Transaction::STATUS_CANCELLED,
                                'admin_notes' => $data['rejection_reason'],
                                'processed_at' => now(),
                            ]);
                            
                            Log::info('Withdrawal rejected by admin', [
                                'transaction_id' => $this->record->id,
                                'user_id' => $this->record->user_id,
                                'amount' => $this->record->amount,
                                'reason' => $data['rejection_reason'],
                                'admin_id' => auth()->id(),
                            ]);
                        });
                        
                        Notification::make()
                            ->title('Withdrawal Rejected')
                            ->body('The withdrawal request has been rejected and amount refunded.')
                            ->success()
                            ->send();
                            
                        $this->redirect($this->getResource()::getUrl('index'));
                            
                    } catch (\Exception $e) {
                        Log::error('Failed to reject withdrawal', [
                            'transaction_id' => $this->record->id,
                            'error' => $e->getMessage(),
                        ]);
                        
                        Notification::make()
                            ->title('Error')
                            ->body('Failed to reject withdrawal: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}