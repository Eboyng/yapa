<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletResource\Pages;
use App\Models\Wallet;
use App\Models\User;
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
use App\Models\AuditLog;
use App\Models\Transaction;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationGroup = 'Financial Management';

    protected static ?int $navigationSort = 1;

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
                    ->label('Active')
                    ->boolean(),
                
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
                
                Filter::make('is_active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->label('Active Only'),
                
                Filter::make('has_balance')
                    ->query(fn (Builder $query): Builder => $query->where('balance', '>', 0))
                    ->label('With Balance'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Action::make('adjust_balance')
                    ->label('Adjust Balance')
                    ->icon('heroicon-o-calculator')
                    ->color('warning')
                    ->form([
                        Select::make('type')
                            ->label('Adjustment Type')
                            ->options([
                                'credit' => 'Credit (Add)',
                                'debit' => 'Debit (Subtract)',
                            ])
                            ->required(),
                        
                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->step(0.01)
                            ->required()
                            ->rules(['min:0.01']),
                        
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Wallet $record, array $data) {
                        $amount = (float) $data['amount'];
                        $type = $data['type'];
                        $reason = $data['reason'];
                        
                        try {
                            if ($type === 'credit') {
                                $record->credit($amount);
                            } else {
                                $record->debit($amount);
                            }
                            
                            // Create transaction record
                            Transaction::create([
                                'user_id' => $record->user_id,
                                'wallet_id' => $record->id,
                                'type' => $type === 'credit' ? Transaction::TYPE_CREDIT : Transaction::TYPE_DEBIT,
                                'category' => Transaction::CATEGORY_MANUAL_ADJUSTMENT,
                                'amount' => $amount,
                                'balance_before' => $type === 'credit' ? $record->balance - $amount : $record->balance + $amount,
                                'balance_after' => $record->balance,
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