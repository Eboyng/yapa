<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalResource\Pages;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationLabel = 'Withdrawal Requests';
    
    protected static ?string $modelLabel = 'Withdrawal Request';
    
    protected static ?string $pluralModelLabel = 'Withdrawal Requests';
    
    protected static ?string $navigationGroup = 'Financial Management';
    
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('category', Transaction::CATEGORY_WITHDRAWAL)
            ->with(['user'])
            ->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Withdrawal Details')
                    ->schema([
                        Forms\Components\TextInput::make('reference')
                            ->label('Reference')
                            ->disabled()
                            ->columnSpanFull(),
                            
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount (₦)')
                            ->numeric()
                            ->prefix('₦')
                            ->disabled()
                            ->columnSpan(1),
                            
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                Transaction::STATUS_PENDING => 'Pending',
                                Transaction::STATUS_PROCESSING => 'Processing',
                                Transaction::STATUS_COMPLETED => 'Completed',
                                Transaction::STATUS_FAILED => 'Failed',
                                Transaction::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->required()
                            ->columnSpan(1),
                            
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->placeholder('Add notes about this withdrawal request...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Withdrawal Method & Details')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Withdrawal Details')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Reference copied!')
                    ->limit(20),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->limit(25),
                    
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('NGN')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('metadata.withdrawal_method')
                    ->label('Method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bank_account' => 'success',
                        'opay' => 'info',
                        'palmpay' => 'warning',
                        'airtime' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bank_account' => 'Bank Transfer',
                        'opay' => 'Opay',
                        'palmpay' => 'PalmPay',
                        'airtime' => 'Airtime',
                        default => ucfirst($state),
                    }),
                    
                Tables\Columns\TextColumn::make('metadata.net_amount')
                    ->label('Net Amount')
                    ->money('NGN')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Transaction::STATUS_PENDING => 'warning',
                        Transaction::STATUS_PROCESSING => 'info',
                        Transaction::STATUS_COMPLETED => 'success',
                        Transaction::STATUS_FAILED => 'danger',
                        Transaction::STATUS_CANCELLED => 'gray',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested At')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Transaction::STATUS_PENDING => 'Pending',
                        Transaction::STATUS_PROCESSING => 'Processing',
                        Transaction::STATUS_COMPLETED => 'Completed',
                        Transaction::STATUS_FAILED => 'Failed',
                        Transaction::STATUS_CANCELLED => 'Cancelled',
                    ])
                    ->default(Transaction::STATUS_PENDING),
                    
                SelectFilter::make('withdrawal_method')
                    ->label('Method')
                    ->options([
                        'bank_account' => 'Bank Transfer',
                        'opay' => 'Opay',
                        'palmpay' => 'PalmPay',
                        'airtime' => 'Airtime',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereJsonContains('metadata->withdrawal_method', $value),
                        );
                    }),
                    
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Transaction $record): bool => $record->status === Transaction::STATUS_PENDING)
                    ->requiresConfirmation()
                    ->modalHeading('Approve Withdrawal Request')
                    ->modalDescription('Are you sure you want to approve this withdrawal request? This action cannot be undone.')
                    ->action(function (Transaction $record) {
                        try {
                            DB::transaction(function () use ($record) {
                                $record->update([
                                    'status' => Transaction::STATUS_COMPLETED,
                                    'processed_at' => now(),
                                ]);
                                
                                Log::info('Withdrawal approved by admin', [
                                    'transaction_id' => $record->id,
                                    'user_id' => $record->user_id,
                                    'amount' => $record->amount,
                                    'admin_id' => auth()->id(),
                                ]);
                            });
                            
                            Notification::make()
                                ->title('Withdrawal Approved')
                                ->body('The withdrawal request has been approved successfully.')
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            Log::error('Failed to approve withdrawal', [
                                'transaction_id' => $record->id,
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
                    ->visible(fn (Transaction $record): bool => in_array($record->status, [Transaction::STATUS_PENDING, Transaction::STATUS_PROCESSING]))
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->placeholder('Please provide a reason for rejecting this withdrawal...')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (Transaction $record, array $data) {
                        try {
                            DB::transaction(function () use ($record, $data) {
                                // Refund the amount back to user's earnings wallet
                                $transactionService = app(TransactionService::class);
                                $transactionService->credit(
                                    $record->user_id,
                                    $record->amount,
                                    'earnings',
                                    Transaction::CATEGORY_REFUND,
                                    'Withdrawal request rejected: ' . $data['rejection_reason'],
                                    $record->id
                                );
                                
                                $record->update([
                                    'status' => Transaction::STATUS_CANCELLED,
                                    'admin_notes' => $data['rejection_reason'],
                                    'processed_at' => now(),
                                ]);
                                
                                Log::info('Withdrawal rejected by admin', [
                                    'transaction_id' => $record->id,
                                    'user_id' => $record->user_id,
                                    'amount' => $record->amount,
                                    'reason' => $data['rejection_reason'],
                                    'admin_id' => auth()->id(),
                                ]);
                            });
                            
                            Notification::make()
                                ->title('Withdrawal Rejected')
                                ->body('The withdrawal request has been rejected and amount refunded.')
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            Log::error('Failed to reject withdrawal', [
                                'transaction_id' => $record->id,
                                'error' => $e->getMessage(),
                            ]);
                            
                            Notification::make()
                                ->title('Error')
                                ->body('Failed to reject withdrawal: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Selected Withdrawals')
                        ->modalDescription('Are you sure you want to approve all selected withdrawal requests?')
                        ->action(function (Collection $records) {
                            $approved = 0;
                            $failed = 0;
                            
                            foreach ($records as $record) {
                                if ($record->status === Transaction::STATUS_PENDING) {
                                    try {
                                        $record->update([
                                            'status' => Transaction::STATUS_COMPLETED,
                                            'processed_at' => now(),
                                        ]);
                                        $approved++;
                                    } catch (\Exception $e) {
                                        $failed++;
                                        Log::error('Failed to approve withdrawal in bulk', [
                                            'transaction_id' => $record->id,
                                            'error' => $e->getMessage(),
                                        ]);
                                    }
                                }
                            }
                            
                            Notification::make()
                                ->title('Bulk Approval Complete')
                                ->body("Approved: {$approved}, Failed: {$failed}")
                                ->success()
                                ->send();
                        }),
                        
                    BulkAction::make('export_pdf')
                        ->label('Export as PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->action(function (Collection $records) {
                            return static::exportToPdf($records);
                        }),
                ]),
            ])
            ->headerActions([
                Action::make('export_all_pdf')
                    ->label('Export All as PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function () {
                        $records = static::getEloquentQuery()->get();
                        return static::exportToPdf($records);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
    
    public static function exportToPdf(Collection $records)
    {
        $data = [
            'withdrawals' => $records,
            'generated_at' => now()->format('M j, Y g:i A'),
            'total_amount' => $records->sum('amount'),
            'total_count' => $records->count(),
        ];
        
        $pdf = Pdf::loadView('pdf.withdrawals', $data);
        
        $filename = 'withdrawal-requests-' . now()->format('Y-m-d-H-i-s') . '.pdf';
        
        return Response::streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawals::route('/'),
            'create' => Pages\CreateWithdrawal::route('/create'),
            'view' => Pages\ViewWithdrawal::route('/{record}'),
            'edit' => Pages\EditWithdrawal::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('category', Transaction::CATEGORY_WITHDRAWAL)
            ->where('status', Transaction::STATUS_PENDING)
            ->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}