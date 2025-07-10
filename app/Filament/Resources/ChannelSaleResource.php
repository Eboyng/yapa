<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChannelSaleResource\Pages;
use App\Models\ChannelSale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChannelSaleResource extends Resource
{
    protected static ?string $model = ChannelSale::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Channel Sales';

    protected static ?string $navigationGroup = 'Channel Marketplace';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Channel Information')
                    ->schema([
                        Forms\Components\TextInput::make('channel_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('whatsapp_number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->required()
                            ->options(ChannelSale::CATEGORIES),
                        Forms\Components\TextInput::make('audience_size')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('engagement_rate')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%'),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix('₦'),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(4),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Seller Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ]),
                
                Forms\Components\Section::make('Status & Visibility')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                ChannelSale::STATUS_LISTED => 'Listed',
                                ChannelSale::STATUS_UNDER_REVIEW => 'Under Review',
                                ChannelSale::STATUS_SOLD => 'Sold',
                                ChannelSale::STATUS_REMOVED => 'Removed',
                            ])
                            ->default(ChannelSale::STATUS_UNDER_REVIEW),
                        Forms\Components\Toggle::make('visibility')
                            ->label('Publicly Visible')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('channel_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Seller')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ChannelSale::CATEGORIES[$state] ?? $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('audience_size')
                    ->label('Members')
                    ->formatStateUsing(fn (int $state): string => number_format($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ChannelSale::STATUS_LISTED => 'success',
                        ChannelSale::STATUS_UNDER_REVIEW => 'warning',
                        ChannelSale::STATUS_SOLD => 'info',
                        ChannelSale::STATUS_REMOVED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),
                Tables\Columns\IconColumn::make('visibility')
                    ->label('Visible')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        ChannelSale::STATUS_LISTED => 'Listed',
                        ChannelSale::STATUS_UNDER_REVIEW => 'Under Review',
                        ChannelSale::STATUS_SOLD => 'Sold',
                        ChannelSale::STATUS_REMOVED => 'Removed',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->options(ChannelSale::CATEGORIES),
                Tables\Filters\TernaryFilter::make('visibility')
                    ->label('Publicly Visible'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (ChannelSale $record) {
                        $record->markAsListed();
                    })
                    ->visible(fn (ChannelSale $record): bool => $record->isUnderReview()),
                Tables\Actions\Action::make('remove')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function (ChannelSale $record) {
                        $record->markAsRemoved();
                    })
                    ->visible(fn (ChannelSale $record): bool => !$record->isSold())
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(function (ChannelSale $record) {
                                if ($record->isUnderReview()) {
                                    $record->markAsListed();
                                }
                            });
                        })
                        ->requiresConfirmation(),
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
            'index' => Pages\ListChannelSales::route('/'),
            'create' => Pages\CreateChannelSale::route('/create'),
            'edit' => Pages\EditChannelSale::route('/{record}/edit'),
        ];
    }
}