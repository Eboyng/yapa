<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChannelPurchaseResource\Pages;
use App\Models\ChannelPurchase;
use App\Services\TransactionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChannelPurchaseResource extends Resource
{
    protected static ?string $model = ChannelPurchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Channel Purchases';

    protected static ?string $navigationGroup = 'Channel Marketplace';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Purchase Information')
                    ->schema([
                        Forms\Components\Select::make('buyer_id')
                            ->relationship('buyer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('channel_sale_id')
                            ->relationship('channelSale', 'channel_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix('₦')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                ChannelPurchase::STATUS_PENDING => 'Pending',
                                ChannelPurchase::STATUS_IN_ESCROW => 'In Escrow',
                                ChannelPurchase::STATUS_COMPLETED => 'Completed',
                                ChannelPurchase::STATUS_FAILED => 'Failed',
                                ChannelPurchase::STATUS_REFUNDED => 'Refunded',
                            ]),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Transaction Information')
                    ->schema([
                        Forms\Components\TextInput::make('escrow_transaction_id')
                            ->label('Escrow Transaction ID')
                            ->disabled(),
                    ]),
                
                Forms\Components\Section::make('Admin Notes')
                    ->schema([
                        Forms\Components\Textarea::make('admin_note')
                            ->label('Admin Note')
                            ->columnSpanFull()
                            ->rows(4),
                        Forms\Components\Textarea::make('buyer_note')
                            ->label('Buyer Note')
                            ->columnSpanFull()
                            ->rows(3)
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('channelSale.channel_name')
                    ->label('Channel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('channelSale.user.name')
                    ->label('Seller')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ChannelPurchase::STATUS_PENDING => 'warning',
                        ChannelPurchase::STATUS_IN_ESCROW => 'info',
                        ChannelPurchase::STATUS_COMPLETED => 'success',
                        ChannelPurchase::STATUS_FAILED => 'danger',
                        ChannelPurchase::STATUS_REFUNDED => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('escrow_transaction_id')
                    ->label('Transaction ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Purchase Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        ChannelPurchase::STATUS_PENDING => 'Pending',
                        ChannelPurchase::STATUS_IN_ESCROW => 'In Escrow',
                        ChannelPurchase::STATUS_COMPLETED => 'Completed',
                        ChannelPurchase::STATUS_FAILED => 'Failed',
                        ChannelPurchase::STATUS_REFUNDED => 'Refunded',
                    ]),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('complete_purchase')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (ChannelPurchase $record) {
                        $transactionService = app(TransactionService::class);
                        
                        try {
                            \DB::transaction(function () use ($record, $transactionService) {
                                // Release escrow funds
                                $transactionService->releaseEscrow($record->escrow_transaction_id);
                                
                                // Update purchase status
                                $record->update([
                                    'status' => ChannelPurchase::STATUS_COMPLETED,
                                    'admin_note' => 'Purchase completed by admin at ' . now()->format('Y-m-d H:i:s'),
                                ]);
                                
                                // Mark channel as sold
                                $record->channelSale->markAsSold();
                            });
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Purchase completed successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Failed to complete purchase')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (ChannelPurchase $record): bool => $record->status === ChannelPurchase::STATUS_IN_ESCROW)
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('refund_purchase')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->action(function (ChannelPurchase $record) {
                        $transactionService = app(TransactionService::class);
                        
                        try {
                            \DB::transaction(function () use ($record, $transactionService) {
                                // Refund escrow funds
                                $transactionService->refundEscrow($record->escrow_transaction_id);
                                
                                // Update purchase status
                                $record->update([
                                    'status' => ChannelPurchase::STATUS_REFUNDED,
                                    'admin_note' => 'Purchase refunded by admin at ' . now()->format('Y-m-d H:i:s'),
                                ]);
                            });
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Purchase refunded successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Failed to refund purchase')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (ChannelPurchase $record): bool => $record->status === ChannelPurchase::STATUS_IN_ESCROW)
                    ->requiresConfirmation(),
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
            'index' => Pages\ListChannelPurchases::route('/'),
            'create' => Pages\CreateChannelPurchase::route('/create'),
            'edit' => Pages\EditChannelPurchase::route('/{record}/edit'),
        ];
    }
}