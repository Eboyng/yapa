<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletResource\Pages;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Transaction;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationGroup = 'Financial Management';

    protected static ?int $navigationSort = 1;
    
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Wallet Information')
                    ->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Select::make('type')
                            ->label('Wallet Type')
                            ->options([
                                Wallet::TYPE_CREDITS => 'Credits',
                                Wallet::TYPE_NAIRA => 'Naira',
                                Wallet::TYPE_EARNINGS => 'Earnings',
                            ])
                            ->required(),
                        
                        TextInput::make('balance')
                            ->label('Balance')
                            ->numeric()
                            ->step(0.01)
                            ->default(0)
                            ->required(),
                        
                        Select::make('currency')
                            ->label('Currency')
                            ->options([
                                Wallet::CURRENCY_NGN => 'Nigerian Naira (NGN)',
                                Wallet::CURRENCY_CREDITS => 'Credits',
                            ])
                            ->default(Wallet::CURRENCY_NGN),
                        
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'primary' => Wallet::TYPE_CREDITS,
                        'success' => Wallet::TYPE_NAIRA,
                        'warning' => Wallet::TYPE_EARNINGS,
                    ]),
                
                Tables\Columns\TextColumn::make('formatted_balance')
                    ->label('Balance')
                    ->sortable('balance'),
                
                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        Wallet::TYPE_CREDITS => 'Credits',
                        Wallet::TYPE_NAIRA => 'Naira',
                        Wallet::TYPE_EARNINGS => 'Earnings',
                    ]),
                
                SelectFilter::make('currency')
                    ->options([
                        Wallet::CURRENCY_NGN => 'NGN',
                        Wallet::CURRENCY_CREDITS => 'Credits',
                    ]),
                
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Paused',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value'])) {
                            return $query->where('is_active', (bool) $data['value']);
                        }
                        return $query;
                    }),
                
                Filter::make('has_balance')
                    ->query(fn (Builder $query): Builder => $query->where('balance', '>', 0))
                    ->label('With Balance'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\ActionGroup::make([
                    Action::make('adjust_balance')
                        ->label('Adjust Balance')
                        ->icon('heroicon-o-calculator')
                        ->color('warning')
                        ->form([
                            Select::make('wallet_type')
                                ->label('Wallet Type')
                                ->options([
                                    Wallet::TYPE_CREDITS => 'Credits',
                                    Wallet::TYPE_NAIRA => 'Naira',
                                    Wallet::TYPE_EARNINGS => 'Earnings',
                                ])
                                ->default(fn (Wallet $record) => $record->type),
                            
                            Select::make('type')
                                ->label('Adjustment Type')
                                ->options([
                                    'credit' => 'Credit (Add)',
                                    'debit' => 'Debit (Subtract)',
                                ]),
                            
                            TextInput::make('amount')
                                ->label('Amount')
                                ->numeric()
                                ->step(0.01)
                                ->rules(['min:0.01']),
                            
                            Forms\Components\Textarea::make('reason')
                                ->label('Reason (Optional)')
                                ->rows(3),
                        ])
                        ->action(function (Wallet $record, array $data) {
                            $amount = (float) $data['amount'];
                            $type = $data['type'];
                            $reason = $data['reason'] ?? 'Manual adjustment by admin';
                            $walletType = $data['wallet_type'] ?? $record->type;
                            
                            try {
                                // Get the target wallet
                                $targetWallet = $record;
                                if ($walletType !== $record->type) {
                                    $targetWallet = $record->user->getWallet($walletType);
                                }
                                
                                if ($type === 'credit') {
                                    $targetWallet->credit($amount);
                                } else {
                                    $targetWallet->debit($amount);
                                }
                                
                                // Determine correct transaction type based on wallet type
                                $transactionType = match($walletType) {
                                    Wallet::TYPE_CREDITS => $type === 'credit' ? Transaction::TYPE_CREDIT : Transaction::TYPE_DEBIT,
                                    Wallet::TYPE_NAIRA => Transaction::TYPE_NAIRA,
                                    Wallet::TYPE_EARNINGS => Transaction::TYPE_EARNINGS,
                                    default => $type === 'credit' ? Transaction::TYPE_CREDIT : Transaction::TYPE_DEBIT,
                                };
                                
                                // Create transaction record
                                Transaction::create([
                                    'user_id' => $targetWallet->user_id,
                                    'wallet_id' => $targetWallet->id,
                                    'type' => $transactionType,
                                    'category' => Transaction::CATEGORY_MANUAL_ADJUSTMENT,
                                    'amount' => $amount,
                                    'balance_before' => $type === 'credit' ? $targetWallet->balance - $amount : $targetWallet->balance + $amount,
                                    'balance_after' => $targetWallet->balance,
                                    'description' => 'Manual adjustment by admin: ' . $reason,
                                    'status' => Transaction::STATUS_COMPLETED,
                                    'payment_method' => Transaction::PAYMENT_METHOD_SYSTEM,
                                    'source' => 'admin_panel',
                                    'completed_at' => now(),
                                ]);
                                
                                Notification::make()
                                    ->title('Balance adjusted successfully')
                                    ->success()
                                    ->send();
                                    
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Failed to adjust balance')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    
                    Action::make('pause_wallet')
                        ->label('Pause Wallet')
                        ->icon('heroicon-o-pause')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Pause Wallet')
                        ->modalDescription('Are you sure you want to pause this wallet? The user will not be able to use this wallet until it is reactivated.')
                        ->visible(fn (Wallet $record) => $record->is_active)
                        ->action(function (Wallet $record) {
                            try {
                                $record->update(['is_active' => false]);
                                
                                // Create audit log
                                AuditLog::create([
                                    'admin_user_id' => auth()->id(),
                                    'target_user_id' => $record->user_id,
                                    'action' => 'wallet_paused',
                                    'description' => "Paused {$record->user->name}'s {$record->type} wallet",
                                    'metadata' => [
                                        'wallet_id' => $record->id,
                                        'wallet_type' => $record->type,
                                        'balance' => $record->balance,
                                    ],
                                ]);
                                
                                Notification::make()
                                    ->title('Wallet paused successfully')
                                    ->success()
                                    ->send();
                                    
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Failed to pause wallet')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    
                    Action::make('activate_wallet')
                        ->label('Activate Wallet')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Activate Wallet')
                        ->modalDescription('Are you sure you want to activate this wallet?')
                        ->visible(fn (Wallet $record) => !$record->is_active)
                        ->action(function (Wallet $record) {
                            try {
                                $record->update(['is_active' => true]);
                                
                                // Create audit log
                                AuditLog::create([
                                    'admin_user_id' => auth()->id(),
                                    'target_user_id' => $record->user_id,
                                    'action' => 'wallet_activated',
                                    'description' => "Activated {$record->user->name}'s {$record->type} wallet",
                                    'metadata' => [
                                        'wallet_id' => $record->id,
                                        'wallet_type' => $record->type,
                                        'balance' => $record->balance,
                                    ],
                                ]);
                                
                                Notification::make()
                                    ->title('Wallet activated successfully')
                                    ->success()
                                    ->send();
                                    
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Failed to activate wallet')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
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
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'view' => Pages\ViewWallet::route('/{record}'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}