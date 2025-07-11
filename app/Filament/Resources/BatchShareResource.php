<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BatchShareResource\Pages;
use App\Models\BatchShare;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

class BatchShareResource extends Resource
{
    protected static ?string $model = BatchShare::class;

    protected static ?string $navigationIcon = 'heroicon-o-share';

    protected static ?string $navigationGroup = 'Batch Management';

    protected static ?string $navigationLabel = 'Batch Shares';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\Select::make('batch_id')
                    ->relationship('batch', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\Select::make('platform')
                    ->options([
                        BatchShare::PLATFORM_WHATSAPP => 'WhatsApp',
                        BatchShare::PLATFORM_FACEBOOK => 'Facebook',
                        BatchShare::PLATFORM_TWITTER => 'Twitter',
                        BatchShare::PLATFORM_COPY_LINK => 'Copy Link',
                    ])
                    ->required(),
                    
                Forms\Components\TextInput::make('share_count')
                    ->numeric()
                    ->default(0)
                    ->required(),
                    
                Forms\Components\Toggle::make('rewarded')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('batch.name')
                    ->label('Batch')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                Tables\Columns\BadgeColumn::make('platform')
                    ->label('Platform')
                    ->colors([
                        'success' => BatchShare::PLATFORM_WHATSAPP,
                        'primary' => BatchShare::PLATFORM_FACEBOOK,
                        'info' => BatchShare::PLATFORM_TWITTER,
                        'secondary' => BatchShare::PLATFORM_COPY_LINK,
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        BatchShare::PLATFORM_WHATSAPP => 'WhatsApp',
                        BatchShare::PLATFORM_FACEBOOK => 'Facebook',
                        BatchShare::PLATFORM_TWITTER => 'Twitter',
                        BatchShare::PLATFORM_COPY_LINK => 'Copy Link',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('share_count')
                    ->label('Referrals')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 10 => 'success',
                        $state >= 5 => 'warning',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(fn (BatchShare $record): string => "{$record->share_count}/10")
                    ->badge()
                    ->color(fn (BatchShare $record): string => match (true) {
                        $record->share_count >= 10 => 'success',
                        $record->share_count >= 5 => 'warning',
                        default => 'gray',
                    }),
                    
                Tables\Columns\IconColumn::make('rewarded')
                    ->label('Rewarded')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('reward_status')
                    ->label('Reward Status')
                    ->getStateUsing(fn (BatchShare $record): string => match (true) {
                        $record->rewarded => '✅ Rewarded',
                        $record->share_count >= 10 => '🎁 Pending',
                        default => '⏳ In Progress',
                    })
                    ->badge()
                    ->color(fn (BatchShare $record): string => match (true) {
                        $record->rewarded => 'success',
                        $record->share_count >= 10 => 'warning',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Shared At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('platform')
                    ->options([
                        BatchShare::PLATFORM_WHATSAPP => 'WhatsApp',
                        BatchShare::PLATFORM_FACEBOOK => 'Facebook',
                        BatchShare::PLATFORM_TWITTER => 'Twitter',
                        BatchShare::PLATFORM_COPY_LINK => 'Copy Link',
                    ]),
                    
                SelectFilter::make('rewarded')
                    ->options([
                        '1' => 'Rewarded',
                        '0' => 'Not Rewarded',
                    ]),
                    
                Filter::make('eligible_for_reward')
                    ->label('Eligible for Reward')
                    ->query(fn (Builder $query): Builder => $query->where('share_count', '>=', 10)->where('rewarded', false)),
                    
                Filter::make('high_performers')
                    ->label('High Performers (5+ referrals)')
                    ->query(fn (Builder $query): Builder => $query->where('share_count', '>=', 5)),
                    
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
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
            'index' => Pages\ListBatchShares::route('/'),
            'create' => Pages\CreateBatchShare::route('/create'),
            'view' => Pages\ViewBatchShare::route('/{record}'),
            'edit' => Pages\EditBatchShare::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('share_count', '>=', 10)
            ->where('rewarded', false)
            ->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getNavigationBadge() > 0 ? 'warning' : null;
    }
}