<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChannelResource\Pages;
use App\Models\Channel;
use App\Models\User;
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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class ChannelResource extends Resource
{
    protected static ?string $model = Channel::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Channel Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Channel Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('niche')
                            ->required()
                            ->options(Channel::NICHES),
                        Forms\Components\TextInput::make('follower_count')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('whatsapp_link')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->rows(3),
                        Forms\Components\FileUpload::make('sample_screenshot')
                            ->image()
                            ->required()
                            ->directory('channel-screenshots')
                            ->visibility('public'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Status & Admin Notes')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                Channel::STATUS_PENDING => 'Pending',
                                Channel::STATUS_APPROVED => 'Approved',
                                Channel::STATUS_REJECTED => 'Rejected',
                                Channel::STATUS_SUSPENDED => 'Suspended',
                            ])
                            ->default(Channel::STATUS_PENDING),
                        Forms\Components\Textarea::make('admin_notes')
                            ->maxLength(1000)
                            ->rows(3),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->maxLength(500)
                            ->visible(fn (Forms\Get $get) => $get('status') === Channel::STATUS_REJECTED),
                    ])->columns(1),
                
                Forms\Components\Section::make('Featured Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Channel'),
                        Forms\Components\TextInput::make('featured_priority')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(1)
                            ->visible(fn (Forms\Get $get) => $get('is_featured')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('sample_screenshot')
                    ->size(60)
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('niche')
                    ->formatStateUsing(fn (string $state): string => Channel::NICHES[$state] ?? $state)
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('follower_count')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => number_format($state)),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Channel::STATUS_PENDING => 'warning',
                        Channel::STATUS_APPROVED => 'success',
                        Channel::STATUS_REJECTED => 'danger',
                        Channel::STATUS_SUSPENDED => 'gray',
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
                        Channel::STATUS_PENDING => 'Pending',
                        Channel::STATUS_APPROVED => 'Approved',
                        Channel::STATUS_REJECTED => 'Rejected',
                        Channel::STATUS_SUSPENDED => 'Suspended',
                    ]),
                Tables\Filters\SelectFilter::make('niche')
                    ->options(Channel::NICHES),
                Tables\Filters\Filter::make('is_featured')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true))
                    ->label('Featured Only'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Channel $record): bool => $record->status === Channel::STATUS_PENDING)
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Admin Notes (Optional)')
                            ->maxLength(1000),
                    ])
                    ->action(function (Channel $record, array $data): void {
                        $record->approve(auth()->id(), $data['admin_notes'] ?? null);
                        
                        Notification::make()
                            ->title('Channel Approved')
                            ->body("Channel '{$record->name}' has been approved.")
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Channel $record): bool => $record->status === Channel::STATUS_PENDING)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->maxLength(500),
                        Textarea::make('admin_notes')
                            ->label('Admin Notes (Optional)')
                            ->maxLength(1000),
                    ])
                    ->action(function (Channel $record, array $data): void {
                        $record->reject($data['rejection_reason'], $data['admin_notes'] ?? null);
                        
                        Notification::make()
                            ->title('Channel Rejected')
                            ->body("Channel '{$record->name}' has been rejected.")
                            ->warning()
                            ->send();
                    }),
                Action::make('suspend')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->visible(fn (Channel $record): bool => $record->status === Channel::STATUS_APPROVED)
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Suspension Reason')
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->action(function (Channel $record, array $data): void {
                        $record->suspend($data['admin_notes']);
                        
                        Notification::make()
                            ->title('Channel Suspended')
                            ->body("Channel '{$record->name}' has been suspended.")
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
                Section::make('Channel Information')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('user.name')
                            ->label('Owner'),
                        TextEntry::make('niche')
                            ->formatStateUsing(fn (string $state): string => Channel::NICHES[$state] ?? $state),
                        TextEntry::make('follower_count')
                            ->formatStateUsing(fn (int $state): string => number_format($state)),
                        TextEntry::make('whatsapp_link')
                            ->url()
                            ->openUrlInNewTab(),
                        TextEntry::make('description'),
                    ])->columns(2),
                
                Section::make('Sample Screenshot')
                    ->schema([
                        ImageEntry::make('sample_screenshot')
                            ->hiddenLabel(),
                    ]),
                
                Section::make('Status & Admin Information')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                Channel::STATUS_PENDING => 'warning',
                                Channel::STATUS_APPROVED => 'success',
                                Channel::STATUS_REJECTED => 'danger',
                                Channel::STATUS_SUSPENDED => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('is_featured')
                            ->label('Featured')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('featured_priority')
                            ->visible(fn (Channel $record): bool => $record->is_featured),
                        TextEntry::make('admin_notes'),
                        TextEntry::make('rejection_reason')
                            ->visible(fn (Channel $record): bool => $record->status === Channel::STATUS_REJECTED),
                        TextEntry::make('approvedBy.name')
                            ->label('Approved By')
                            ->visible(fn (Channel $record): bool => $record->status === Channel::STATUS_APPROVED),
                        TextEntry::make('approved_at')
                            ->dateTime()
                            ->visible(fn (Channel $record): bool => $record->status === Channel::STATUS_APPROVED),
                    ])->columns(2),
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
            'index' => Pages\ListChannels::route('/'),
            'create' => Pages\CreateChannel::route('/create'),
            'view' => Pages\ViewChannel::route('/{record}'),
            'edit' => Pages\EditChannel::route('/{record}/edit'),
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
        return static::getModel()::where('status', Channel::STATUS_PENDING)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}