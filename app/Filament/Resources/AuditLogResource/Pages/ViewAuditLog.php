<?php

namespace App\Filament\Resources\AuditLogResource\Pages;

use App\Filament\Resources\AuditLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\BadgeEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Support\Colors\Color;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No edit action for audit logs
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Audit Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Log ID'),
                        TextEntry::make('adminUser.name')
                            ->label('Admin User')
                            ->default('System'),
                        TextEntry::make('targetUser.name')
                            ->label('Target User')
                            ->default('N/A'),
                        BadgeEntry::make('action')
                            ->label('Action')
                            ->colors([
                                'primary' => ['user_created', 'user_updated', 'batch_created', 'batch_updated'],
                                'success' => ['user_approved', 'ad_approved', 'transaction_approved'],
                                'warning' => ['user_flagged', 'user_banned', 'batch_closed'],
                                'danger' => ['user_deleted', 'ad_rejected', 'transaction_rejected'],
                                'info' => ['login', 'logout', 'password_changed'],
                            ]),
                        TextEntry::make('reason')
                            ->label('Reason')
                            ->default('N/A')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Old Values')
                    ->schema([
                        KeyValueEntry::make('old_values')
                            ->label('')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->old_values)),
                
                Section::make('New Values')
                    ->schema([
                        KeyValueEntry::make('new_values')
                            ->label('')
                            ->hiddenLabel()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->new_values)),
                
                Section::make('System Information')
                    ->schema([
                        TextEntry::make('ip_address')
                            ->label('IP Address')
                            ->copyable(),
                        TextEntry::make('user_agent')
                            ->label('User Agent')
                            ->limit(100)
                            ->tooltip(function (TextEntry $component): ?string {
                                $state = $component->getState();
                                if (strlen($state) <= 100) {
                                    return null;
                                }
                                return $state;
                            }),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}