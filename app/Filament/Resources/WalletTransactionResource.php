<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletTransactionResource\Pages;
use App\Filament\Resources\WalletTransactionResource\RelationManagers;
use App\Models\WalletTransaction;
use App\Models\Wallet;
use App\Models\User;
use App\Services\WalletService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class WalletTransactionResource extends Resource
{
    protected static ?string $model = WalletTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationGroup = 'Financial Management';
    
    protected static ?string $navigationLabel = 'Wallet Transactions';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn (User $record): string => "{$record->name} ({$record->email})")
                            ->helperText('Select the user for this transaction'),
                            
                        Forms\Components\Select::make('wallet_type')
                            ->label('Wallet Type')
                            ->options(WalletTransaction::getWalletTypes())
                            ->required()
                            ->helperText('Select the wallet type'),
                            
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->prefix('₦')
                            ->helperText('Enter the transaction amount'),
                            
                        Forms\Components\Select::make('type')
                            ->label('Transaction Type')
                            ->options(WalletTransaction::getTransactionTypes())
                            ->required()
                            ->helperText('Select credit to add funds, debit to deduct funds'),
                            
                        Forms\Components\Select::make('category')
                            ->label('Category')
                            ->options(WalletTransaction::getCategories())
                            ->required()
                            ->default(WalletTransaction::CATEGORY_ADMIN_FUNDING)
                            ->helperText('Select the transaction category'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500)
                            ->helperText('Optional description for this transaction'),
                            
                        Forms\Components\TextInput::make('reference')
                            ->label('Reference')
                            ->default(fn () => WalletTransaction::generateReference())
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique reference for this transaction'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->sortable()
                    ->label('Reference')
                    ->weight('bold')
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('User')
                    ->description(fn (WalletTransaction $record): string => $record->user->email),
                    
                Tables\Columns\TextColumn::make('wallet_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'credits' => 'info',
                        Wallet::TYPE_NAIRA => 'success',
                        'earnings' => 'warning',
                        default => 'gray',
                    })
                    ->label('Wallet'),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->money('NGN')
                    ->sortable()
                    ->label('Amount')
                    ->color(fn (WalletTransaction $record): string => $record->type === 'credit' ? 'success' : 'danger'),
                    
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'credit' => 'success',
                        'debit' => 'danger',
                        default => 'gray',
                    })
                    ->label('Type'),
                    
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('gray')
                    ->label('Category'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->label('Status'),
                    
                Tables\Columns\TextColumn::make('adminUser.name')
                    ->label('Admin User')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('wallet_type')
                    ->options(WalletTransaction::getWalletTypes())
                    ->label('Wallet Type'),
                    
                Tables\Filters\SelectFilter::make('type')
                    ->options(WalletTransaction::getTransactionTypes())
                    ->label('Transaction Type'),
                    
                Tables\Filters\SelectFilter::make('category')
                    ->options(WalletTransaction::getCategories())
                    ->label('Category'),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->options(WalletTransaction::getStatuses())
                    ->label('Status'),
                    
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created Until'),
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
                    })
                    ->label('Creation Date'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (WalletTransaction $record): bool => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->visible(fn (): bool => Auth::user()->hasRole('super-admin')),
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
            'index' => Pages\ListWalletTransactions::route('/'),
            'create' => Pages\CreateWalletTransaction::route('/create'),
            'view' => Pages\ViewWalletTransaction::route('/{record}'),
            'edit' => Pages\EditWalletTransaction::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() > 0 ? 'warning' : null;
    }
}
