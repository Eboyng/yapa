<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Financial Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('wallet_id')
                            ->relationship('wallet', 'type')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0),
                        Forms\Components\Select::make('type')
                            ->options([
                                'credit' => 'Credit',
                                'debit' => 'Debit',
                            ])
                            ->required(),
                        Forms\Components\Select::make('category')
                            ->options([
                                'referral_bonus' => 'Referral Bonus',
                                'share_earn_payment' => 'Share & Earn Payment',
                                'batch_payment' => 'Batch Payment',
                                'withdrawal' => 'Withdrawal',
                                'deposit' => 'Deposit',
                                'refund' => 'Refund',
                                'penalty' => 'Penalty',
                                'admin_adjustment' => 'Admin Adjustment',
                                'escrow_hold' => 'Escrow Hold',
                                'escrow_release' => 'Escrow Release',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'cancelled' => 'Cancelled',
                                'reversed' => 'Reversed',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('reference')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'bank_transfer' => 'Bank Transfer',
                                'card' => 'Card',
                                'wallet' => 'Wallet',
                                'paystack' => 'Paystack',
                                'flutterwave' => 'Flutterwave',
                                'manual' => 'Manual',
                            ]),
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadata')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('wallet.type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'credits' => 'primary',
                        Wallet::TYPE_NAIRA => 'success',
                        'earnings' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'credit' => 'success',
                        'debit' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        'reversed' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                        'reversed' => 'Reversed',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'credit' => 'Credit',
                        'debit' => 'Debit',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'referral_bonus' => 'Referral Bonus',
                        'share_earn_payment' => 'Share & Earn Payment',
                        'batch_payment' => 'Batch Payment',
                        'withdrawal' => 'Withdrawal',
                        'deposit' => 'Deposit',
                        'refund' => 'Refund',
                        'penalty' => 'Penalty',
                        'admin_adjustment' => 'Admin Adjustment',
                        'escrow_hold' => 'Escrow Hold',
                        'escrow_release' => 'Escrow Release',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
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
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Transaction $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Transaction $record): void {
                        $record->update(['status' => 'completed']);
                        
                        // Log the approval in audit log
                        AuditLog::create([
                            'user_id' => Auth::id(),
                            'action' => 'transaction_approved',
                            'details' => [
                                'transaction_id' => $record->id,
                                'reference' => $record->reference,
                                'amount' => $record->amount,
                                'user_id' => $record->user_id,
                                'approved_by' => Auth::user()->name,
                            ],
                        ]);
                        
                        Notification::make()
                            ->title('Transaction approved successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Transaction $record): bool => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (Transaction $record, array $data): void {
                        $record->update([
                            'status' => 'failed',
                            'metadata' => array_merge($record->metadata ?? [], [
                                'rejection_reason' => $data['reason'],
                                'rejected_by' => Auth::user()->name,
                                'rejected_at' => now(),
                            ]),
                        ]);
                        
                        // Log the rejection in audit log
                        AuditLog::create([
                            'user_id' => Auth::id(),
                            'action' => 'transaction_rejected',
                            'details' => [
                                'transaction_id' => $record->id,
                                'reference' => $record->reference,
                                'amount' => $record->amount,
                                'user_id' => $record->user_id,
                                'reason' => $data['reason'],
                                'rejected_by' => Auth::user()->name,
                            ],
                        ]);
                        
                        Notification::make()
                            ->title('Transaction rejected successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}