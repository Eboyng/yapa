<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\User;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

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
                Section::make('User Information')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('whatsapp_number'),
                        TextEntry::make('location'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])->columns(2),

                Section::make('Balances')
                    ->schema([
                        TextEntry::make('credits_balance')
                            ->suffix(' credits'),
                        TextEntry::make('naira_balance')
                            ->money('NGN'),
                        TextEntry::make('earnings_balance')
                            ->money('NGN'),
                    ])->columns(3),

                Section::make('Status & Verification')
                    ->schema([
                        IconEntry::make('is_admin')
                            ->boolean(),
                        IconEntry::make('is_flagged_for_ads')
                            ->boolean(),
                        IconEntry::make('whatsapp_notifications_enabled')
                            ->boolean(),
                        IconEntry::make('email_notifications_enabled')
                            ->boolean(),
                        TextEntry::make('email_verified_at')
                            ->dateTime()
                            ->placeholder('Not verified'),
                        TextEntry::make('whatsapp_verified_at')
                            ->dateTime()
                            ->placeholder('Not verified'),
                    ])->columns(2),

                Section::make('Statistics')
                    ->schema([
                        TextEntry::make('ad_rejection_count')
                            ->label('Ad Rejections'),
                        TextEntry::make('otp_attempts')
                            ->label('OTP Attempts'),
                        TextEntry::make('flagged_at')
                            ->dateTime()
                            ->placeholder('Not flagged'),
                        TextEntry::make('appeal_submitted_at')
                            ->dateTime()
                            ->placeholder('No appeal submitted'),
                    ])->columns(2),

                Section::make('Appeal Message')
                    ->schema([
                        TextEntry::make('appeal_message')
                            ->placeholder('No appeal message')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (User $record): bool => !empty($record->appeal_message)),
            ]);
    }
}