<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChannelAdResource\Pages;
use App\Models\ChannelAd;
use App\Models\Channel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class ChannelAdResource extends Resource
{
    protected static ?string $model = ChannelAd::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Channel Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Channel Ads';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ad Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(1000)
                            ->rows(3),
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->maxLength(5000),
                        Forms\Components\TextInput::make('media_url')
                            ->url()
                            ->maxLength(255)
                            ->label('Media URL (Optional)'),
                    ])->columns(1),
                
                Forms\Components\Section::make('Budget & Duration')
                    ->schema([
                        Forms\Components\TextInput::make('duration_days')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(365)
                            ->default(7)
                            ->label('Duration (Days)'),
                        Forms\Components\TextInput::make('budget')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->prefix('₦')
                            ->step(0.01),
                        Forms\Components\TextInput::make('payment_per_channel')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->prefix('₦')
                            ->step(0.01),
                        Forms\Components\TextInput::make('max_channels')
                            ->numeric()
                            ->minValue(1)
                            ->label('Maximum Channels (Optional)'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Targeting')
                    ->schema([
                        Forms\Components\Select::make('target_niches')
                            ->multiple()
                            ->options(Channel::NICHES)
                            ->label('Target Niches (Optional)'),
                        Forms\Components\TextInput::make('min_followers')
                            ->numeric()
                            ->minValue(1)
                            ->label('Minimum Followers (Optional)'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Schedule')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Start Date (Optional)'),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('End Date (Optional)')
                            ->after('start_date'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Instructions & Requirements')
                    ->schema([
                        Forms\Components\RichEditor::make('instructions')
                            ->maxLength(2000),
                        Forms\Components\RichEditor::make('requirements')
                            ->maxLength(2000),
                    ])->columns(1),
                
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                ChannelAd::STATUS_DRAFT => 'Draft',
                                ChannelAd::STATUS_ACTIVE => 'Active',
                                ChannelAd::STATUS_PAUSED => 'Paused',
                                ChannelAd::STATUS_COMPLETED => 'Completed',
                                ChannelAd::STATUS_EXPIRED => 'Expired',
                                ChannelAd::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->default(ChannelAd::STATUS_DRAFT),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('budget')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_per_channel')
                    ->money('NGN')
                    ->sortable()
                    ->label('Per Channel'),
                Tables\Columns\TextColumn::make('duration_days')
                    ->numeric()
                    ->sortable()
                    ->label('Duration'),
                Tables\Columns\TextColumn::make('channelAdApplications_count')
                    ->counts('channelAdApplications')
                    ->label('Applications'),
                Tables\Columns\TextColumn::make('approvedApplications_count')
                    ->counts('approvedApplications')
                    ->label('Approved'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ChannelAd::STATUS_DRAFT => 'gray',
                        ChannelAd::STATUS_ACTIVE => 'success',
                        ChannelAd::STATUS_PAUSED => 'warning',
                        ChannelAd::STATUS_COMPLETED => 'info',
                        ChannelAd::STATUS_EXPIRED => 'danger',
                        ChannelAd::STATUS_CANCELLED => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        ChannelAd::STATUS_DRAFT => 'Draft',
                        ChannelAd::STATUS_ACTIVE => 'Active',
                        ChannelAd::STATUS_PAUSED => 'Paused',
                        ChannelAd::STATUS_COMPLETED => 'Completed',
                        ChannelAd::STATUS_EXPIRED => 'Expired',
                        ChannelAd::STATUS_CANCELLED => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('budget_range')
                    ->form([
                        Forms\Components\TextInput::make('budget_from')
                            ->numeric()
                            ->prefix('₦'),
                        Forms\Components\TextInput::make('budget_to')
                            ->numeric()
                            ->prefix('₦'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['budget_from'],
                                fn (Builder $query, $budget): Builder => $query->where('budget', '>=', $budget),
                            )
                            ->when(
                                $data['budget_to'],
                                fn (Builder $query, $budget): Builder => $query->where('budget', '<=', $budget),
                            );
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('activate')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (ChannelAd $record): bool => $record->status === ChannelAd::STATUS_DRAFT)
                    ->action(function (ChannelAd $record): void {
                        $record->update(['status' => ChannelAd::STATUS_ACTIVE]);
                        
                        Notification::make()
                            ->title('Channel Ad Activated')
                            ->body("Channel ad '{$record->title}' is now active.")
                            ->success()
                            ->send();
                    }),
                Action::make('pause')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->visible(fn (ChannelAd $record): bool => $record->status === ChannelAd::STATUS_ACTIVE)
                    ->action(function (ChannelAd $record): void {
                        $record->update(['status' => ChannelAd::STATUS_PAUSED]);
                        
                        Notification::make()
                            ->title('Channel Ad Paused')
                            ->body("Channel ad '{$record->title}' has been paused.")
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Ad Information')
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('description'),
                        TextEntry::make('content')
                            ->html(),
                        TextEntry::make('media_url')
                            ->url()
                            ->openUrlInNewTab(),
                    ])->columns(1),
                
                Section::make('Budget & Performance')
                    ->schema([
                        TextEntry::make('budget')
                            ->money('NGN'),
                        TextEntry::make('payment_per_channel')
                            ->money('NGN'),
                        TextEntry::make('duration_days')
                            ->suffix(' days'),
                        TextEntry::make('max_channels'),
                        TextEntry::make('getCurrentApplicationCount')
                            ->label('Total Applications'),
                        TextEntry::make('getApprovedApplicationCount')
                            ->label('Approved Applications'),
                        TextEntry::make('getTotalBudgetSpent')
                            ->label('Budget Spent')
                            ->money('NGN'),
                        TextEntry::make('getRemainingBudget')
                            ->label('Remaining Budget')
                            ->money('NGN'),
                    ])->columns(2),
                
                Section::make('Targeting')
                    ->schema([
                        TextEntry::make('target_niches')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) return 'All niches';
                                return collect($state)->map(fn ($niche) => Channel::NICHES[$niche] ?? $niche)->join(', ');
                            }),
                        TextEntry::make('min_followers')
                            ->formatStateUsing(fn ($state) => $state ? number_format($state) : 'No minimum'),
                    ])->columns(2),
                
                Section::make('Schedule')
                    ->schema([
                        TextEntry::make('start_date')
                            ->dateTime(),
                        TextEntry::make('end_date')
                            ->dateTime(),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                ChannelAd::STATUS_DRAFT => 'gray',
                                ChannelAd::STATUS_ACTIVE => 'success',
                                ChannelAd::STATUS_PAUSED => 'warning',
                                ChannelAd::STATUS_COMPLETED => 'info',
                                ChannelAd::STATUS_EXPIRED => 'danger',
                                ChannelAd::STATUS_CANCELLED => 'gray',
                                default => 'gray',
                            }),
                    ])->columns(2),
                
                Section::make('Instructions & Requirements')
                    ->schema([
                        TextEntry::make('instructions')
                            ->html(),
                        TextEntry::make('requirements')
                            ->html(),
                    ])->columns(1),
            ]);
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
            'index' => Pages\ListChannelAds::route('/'),
            'create' => Pages\CreateChannelAd::route('/create'),
            'view' => Pages\ViewChannelAd::route('/{record}'),
            'edit' => Pages\EditChannelAd::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', ChannelAd::STATUS_ACTIVE)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}