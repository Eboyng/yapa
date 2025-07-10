<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipResource\Pages;
use App\Models\Tip;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

class TipResource extends Resource
{
    protected static ?string $model = Tip::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tip Details')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $context, $state, Forms\Set $set) {
                                if ($context === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Tip::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText('SEO-friendly URL slug'),

                        FileUpload::make('image')
                            ->image()
                            ->directory('tips')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->helperText('Upload a cover image for the tip'),

                        Textarea::make('content')
                            ->required()
                            ->rows(10)
                            ->columnSpanFull()
                            ->helperText('Write the full tip content'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Publishing')
                    ->schema([
                        Select::make('author_id')
                            ->label('Author')
                            ->options(User::whereHas('roles', function ($query) {
                                $query->where('name', 'admin');
                            })->pluck('name', 'id'))
                            ->default(auth()->id())
                            ->required()
                            ->searchable(),

                        Select::make('status')
                            ->options([
                                Tip::STATUS_DRAFT => 'Draft',
                                Tip::STATUS_PUBLISHED => 'Published',
                                Tip::STATUS_ARCHIVED => 'Archived',
                            ])
                            ->default(Tip::STATUS_DRAFT)
                            ->required()
                            ->live(),

                        DateTimePicker::make('published_at')
                            ->label('Publish Date & Time')
                            ->helperText('Leave empty to publish immediately when status is set to Published')
                            ->visible(fn (Forms\Get $get) => $get('status') === Tip::STATUS_PUBLISHED),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->size(60)
                    ->circular(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'secondary' => Tip::STATUS_DRAFT,
                        'success' => Tip::STATUS_PUBLISHED,
                        'warning' => Tip::STATUS_ARCHIVED,
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('claps')
                    ->label('Claps')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => number_format($state)),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not scheduled'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Tip::STATUS_DRAFT => 'Draft',
                        Tip::STATUS_PUBLISHED => 'Published',
                        Tip::STATUS_ARCHIVED => 'Archived',
                    ]),

                SelectFilter::make('author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('published_date')
                    ->form([
                        DateTimePicker::make('published_from')
                            ->label('Published from'),
                        DateTimePicker::make('published_until')
                            ->label('Published until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Action::make('preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Tip $record): string => route('tips.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Tip $record): bool => $record->isPublished()),

                Action::make('publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Tip $record) {
                        $record->markAsPublished();
                        if (!$record->published_at) {
                            $record->update(['published_at' => now()]);
                        }
                        
                        Notification::make()
                            ->title('Tip published successfully')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Tip $record): bool => $record->isDraft()),

                Action::make('archive')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->action(function (Tip $record) {
                        $record->markAsArchived();
                        
                        Notification::make()
                            ->title('Tip archived successfully')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Tip $record): bool => !$record->isArchived()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    BulkAction::make('publish')
                        ->label('Publish Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each(function (Tip $record) {
                                $record->markAsPublished();
                                if (!$record->published_at) {
                                    $record->update(['published_at' => now()]);
                                }
                            });
                            
                            Notification::make()
                                ->title('Tips published successfully')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),

                    BulkAction::make('archive')
                        ->label('Archive Selected')
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $records->each(fn (Tip $record) => $record->markAsArchived());
                            
                            Notification::make()
                                ->title('Tips archived successfully')
                                ->success()
                                ->send();
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
            'index' => Pages\ListTips::route('/'),
            'create' => Pages\CreateTip::route('/create'),
            'view' => Pages\ViewTip::route('/{record}'),
            'edit' => Pages\EditTip::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', Tip::STATUS_DRAFT)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}