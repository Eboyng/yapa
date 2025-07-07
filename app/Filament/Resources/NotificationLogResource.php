<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationLogResource\Pages;
use App\Models\NotificationLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationLogResource extends Resource
{
    protected static ?string $model = NotificationLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Notification Logs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Notification Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'whatsapp' => 'WhatsApp',
                                'email' => 'Email',
                                'sms' => 'SMS',
                                'push' => 'Push Notification',
                                'in_app' => 'In-App',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('recipient')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subject')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Status & Tracking')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'sent' => 'Sent',
                                'delivered' => 'Delivered',
                                'failed' => 'Failed',
                                'read' => 'Read',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\TextInput::make('provider')
                            ->label('Service Provider')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('provider_message_id')
                            ->label('Provider Message ID')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('error_message')
                            ->label('Error Message')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Timestamps')
                    ->schema([
                        Forms\Components\DateTimePicker::make('sent_at')
                            ->label('Sent At'),
                        Forms\Components\DateTimePicker::make('delivered_at')
                            ->label('Delivered At'),
                        Forms\Components\DateTimePicker::make('read_at')
                            ->label('Read At'),
                        Forms\Components\DateTimePicker::make('failed_at')
                            ->label('Failed At'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Additional Data')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadata')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'whatsapp' => 'success',
                        'email' => 'info',
                        'sms' => 'warning',
                        'push' => 'primary',
                        'in_app' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('recipient')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('message')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'sent' => 'info',
                        'delivered' => 'success',
                        'failed' => 'danger',
                        'read' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider')
                    ->label('Provider')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('delivered_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'email' => 'Email',
                        'sms' => 'SMS',
                        'push' => 'Push Notification',
                        'in_app' => 'In-App',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'delivered' => 'Delivered',
                        'failed' => 'Failed',
                        'read' => 'Read',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('provider')
                    ->options([
                        'kudisms' => 'Kudisms',
                        'smtp' => 'SMTP Email',
                        'mailgun' => 'Mailgun',
                        'sendgrid' => 'SendGrid',
                        'twilio' => 'Twilio',
                        'firebase' => 'Firebase',
                    ])
                    ->multiple(),
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
                Tables\Filters\Filter::make('failed_notifications')
                    ->label('Failed Only')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'failed')),
                Tables\Filters\Filter::make('undelivered_notifications')
                    ->label('Undelivered')
                    ->query(fn (Builder $query): Builder => $query->whereIn('status', ['pending', 'sent'])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('resend')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (NotificationLog $record): bool => in_array($record->status, ['failed', 'pending']))
                    ->requiresConfirmation()
                    ->action(function (NotificationLog $record): void {
                        // Here you would implement the resend logic
                        // This could dispatch a job to resend the notification
                        $record->update([
                            'status' => 'pending',
                            'error_message' => null,
                            'failed_at' => null,
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Notification queued for resend')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_as_read')
                        ->label('Mark as Read')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $records->each(function (NotificationLog $record) {
                                $record->update([
                                    'status' => 'read',
                                    'read_at' => now(),
                                ]);
                            });
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Notifications marked as read')
                                ->success()
                                ->send();
                        }),
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
            'index' => Pages\ListNotificationLogs::route('/'),
            'view' => Pages\ViewNotificationLog::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'failed')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function canCreate(): bool
    {
        return false; // Notification logs are created automatically
    }
}