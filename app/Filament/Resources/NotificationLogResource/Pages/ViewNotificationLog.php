<?php

namespace App\Filament\Resources\NotificationLogResource\Pages;

use App\Filament\Resources\NotificationLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\BadgeEntry;
use Filament\Support\Colors\Color;

class ViewNotificationLog extends ViewRecord
{
    protected static string $resource = NotificationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Notification Information')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User')
                            ->default('N/A'),
                        BadgeEntry::make('type')
                            ->colors([
                                'primary' => 'whatsapp',
                                'success' => 'email',
                                'warning' => 'sms',
                                'info' => 'push',
                                'gray' => 'in_app',
                            ]),
                        TextEntry::make('recipient')
                            ->copyable(),
                        TextEntry::make('subject')
                            ->default('N/A'),
                        TextEntry::make('message')
                            ->columnSpanFull()
                            ->html(),
                    ])
                    ->columns(2),
                
                Section::make('Status & Provider')
                    ->schema([
                        BadgeEntry::make('status')
                            ->colors([
                                'warning' => 'pending',
                                'info' => 'sent',
                                'success' => ['delivered', 'read'],
                                'danger' => 'failed',
                            ]),
                        TextEntry::make('provider')
                            ->default('N/A'),
                        TextEntry::make('provider_message_id')
                            ->label('Provider Message ID')
                            ->default('N/A')
                            ->copyable(),
                        TextEntry::make('error_message')
                            ->label('Error Message')
                            ->default('N/A')
                            ->color(Color::Red)
                            ->visible(fn ($record) => !empty($record->error_message)),
                    ])
                    ->columns(2),
                
                Section::make('System Information')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                        TextEntry::make('sent_at')
                            ->label('Sent At')
                            ->dateTime()
                            ->default('N/A'),
                        TextEntry::make('delivered_at')
                            ->label('Delivered At')
                            ->dateTime()
                            ->default('N/A'),
                    ])
                    ->columns(2),
            ]);
    }
}