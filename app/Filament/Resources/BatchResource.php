<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BatchResource\Pages;
use App\Models\Batch;
use App\Models\Interest;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class BatchResource extends Resource
{
    protected static ?string $model = Batch::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Batch Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Batch Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->rows(3),
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255),
                    ])->columns(1),

                Forms\Components\Section::make('Batch Settings')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                Batch::TYPE_TRIAL => 'Trial',
                                Batch::TYPE_REGULAR => 'Regular',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state === Batch::TYPE_TRIAL) {
                                    $set('limit', Batch::TRIAL_LIMIT);
                                    $set('cost_in_credits', 0);
                                } elseif ($state === Batch::TYPE_REGULAR) {
                                    $set('limit', Batch::REGULAR_LIMIT);
                                }
                            }),
                        Forms\Components\TextInput::make('limit')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(1000),
                        Forms\Components\TextInput::make('cost_in_credits')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('credits'),
                        Forms\Components\Select::make('status')
                            ->options([
                                Batch::STATUS_OPEN => 'Open',
                                Batch::STATUS_CLOSED => 'Closed',
                                Batch::STATUS_FULL => 'Full',
                                Batch::STATUS_EXPIRED => 'Expired',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('auto_close_at')
                            ->label('Auto Close Date')
                            ->default(now()->addDays(7)),
                        Forms\Components\Toggle::make('created_by_admin')
                            ->label('Created by Admin')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Interests')
                    ->schema([
                        Forms\Components\Select::make('interests')
                            ->multiple()
                            ->relationship('interests', 'name')
                            ->preload()
                            ->searchable(),
                    ]),

                Forms\Components\Section::make('Admin Assignment')
                    ->schema([
                        Forms\Components\Select::make('admin_user_id')
                            ->label('Admin User')
                            ->relationship('adminUser', 'name')
                            ->searchable()
                            ->preload()
                            ->default(Auth::id()),
                    ])
                    ->visible(fn () => Auth::user()?->is_admin),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Batch::TYPE_TRIAL => 'info',
                        Batch::TYPE_REGULAR => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Batch::STATUS_OPEN => 'success',
                        Batch::STATUS_CLOSED => 'gray',
                        Batch::STATUS_FULL => 'warning',
                        Batch::STATUS_EXPIRED => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('members_count')
                    ->label('Members')
                    ->getStateUsing(fn (Batch $record): string => $record->getCurrentMemberCount() . '/' . $record->limit)
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_in_credits')
                    ->numeric()
                    ->sortable()
                    ->suffix(' credits'),
                Tables\Columns\TextColumn::make('adminUser.name')
                    ->label('Created By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('auto_close_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        Batch::TYPE_TRIAL => 'Trial',
                        Batch::TYPE_REGULAR => 'Regular',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Batch::STATUS_OPEN => 'Open',
                        Batch::STATUS_CLOSED => 'Closed',
                        Batch::STATUS_FULL => 'Full',
                        Batch::STATUS_EXPIRED => 'Expired',
                    ]),
                Tables\Filters\Filter::make('created_by_admin')
                    ->query(fn (Builder $query): Builder => $query->where('created_by_admin', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('close_batch')
                    ->icon('heroicon-o-lock-closed')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Close Batch')
                    ->modalDescription('Are you sure you want to close this batch? This action cannot be undone.')
                    ->action(function (Batch $record) {
                        $record->update(['status' => Batch::STATUS_CLOSED]);
                        
                        AuditLog::log(
                            Auth::id(),
                            'batch_closed',
                            null,
                            ['status' => $record->getOriginal('status')],
                            ['status' => Batch::STATUS_CLOSED],
                            'Batch closed via admin panel'
                        );
                        
                        Notification::make()
                            ->title('Batch closed successfully')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Batch $record): bool => $record->status === Batch::STATUS_OPEN),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListBatches::route('/'),
            'create' => Pages\CreateBatch::route('/create'),
            'view' => Pages\ViewBatch::route('/{record}'),
            'edit' => Pages\EditBatch::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', Batch::STATUS_OPEN)->count();
    }
}