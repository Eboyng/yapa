<?php

namespace App\Filament\Resources\OtpResource\Pages;

use App\Filament\Resources\OtpResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Support\Colors\Color;
use Carbon\Carbon;

class ViewOtp extends ViewRecord
{
    protected static string $resource = OtpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete OTP')
                ->modalDescription('Are you sure you want to delete this OTP record? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, delete it'),
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('OTP Details')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID'),
                            
                        TextEntry::make('identifier')
                            ->label('Identifier')
                            ->copyable()
                            ->tooltip('Email or phone number'),
                            
                        TextEntry::make('context')
                            ->label('Context')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'registration' => 'success',
                                'login' => 'info',
                                'password_reset' => 'warning',
                                'email_verification' => 'primary',
                                default => 'gray',
                            }),
                            
                        TextEntry::make('user.name')
                            ->label('Associated User')
                            ->placeholder('No user associated')
                            ->url(fn ($record) => $record->user ? route('filament.admin.resources.users.view', $record->user) : null),
                            
                        IconEntry::make('verified')
                            ->label('Verification Status')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                            
                        TextEntry::make('attempts')
                            ->label('Attempts')
                            ->badge()
                            ->color(fn (int $state): string => match (true) {
                                $state === 0 => 'gray',
                                $state <= 2 => 'warning',
                                default => 'danger',
                            }),
                    ])
                    ->columns(2),
                    
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->since(),
                            
                        TextEntry::make('expires_at')
                            ->label('Expires At')
                            ->dateTime()
                            ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : 'success')
                            ->formatStateUsing(function ($record) {
                                if (!$record->expires_at) return 'No expiration';
                                
                                $status = $record->expires_at->isPast() ? 'Expired' : 'Valid';
                                $time = $record->expires_at->format('M j, Y g:i A');
                                $relative = $record->expires_at->diffForHumans();
                                
                                return "{$status} - {$time} ({$relative})";
                            }),
                            
                        TextEntry::make('verified_at')
                            ->label('Verified At')
                            ->dateTime()
                            ->since()
                            ->placeholder('Not verified'),
                            
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime()
                            ->since(),
                    ])
                    ->columns(2),
                    
                Section::make('Security Information')
                    ->schema([
                        TextEntry::make('otp_code')
                            ->label('OTP Code (Hashed)')
                            ->formatStateUsing(fn ($state) => substr($state, 0, 20) . '...')
                            ->copyable()
                            ->tooltip('This is the hashed version of the OTP code for security'),
                            
                        TextEntry::make('status')
                            ->label('Current Status')
                            ->formatStateUsing(function ($record) {
                                if ($record->verified) {
                                    return 'Verified';
                                }
                                
                                if ($record->expires_at && $record->expires_at->isPast()) {
                                    return 'Expired';
                                }
                                
                                if ($record->attempts >= 3) {
                                    return 'Max attempts exceeded';
                                }
                                
                                return 'Active';
                            })
                            ->badge()
                            ->color(function ($record) {
                                if ($record->verified) return 'success';
                                if ($record->expires_at && $record->expires_at->isPast()) return 'danger';
                                if ($record->attempts >= 3) return 'danger';
                                return 'warning';
                            }),
                            
                        TextEntry::make('time_remaining')
                            ->label('Time Remaining')
                            ->formatStateUsing(function ($record) {
                                if ($record->verified) {
                                    return 'N/A (Verified)';
                                }
                                
                                if (!$record->expires_at) {
                                    return 'No expiration';
                                }
                                
                                if ($record->expires_at->isPast()) {
                                    return 'Expired ' . $record->expires_at->diffForHumans();
                                }
                                
                                return 'Expires ' . $record->expires_at->diffForHumans();
                            })
                            ->color(function ($record) {
                                if ($record->verified) return 'gray';
                                if (!$record->expires_at) return 'gray';
                                if ($record->expires_at->isPast()) return 'danger';
                                
                                $minutesLeft = $record->expires_at->diffInMinutes(now());
                                if ($minutesLeft <= 1) return 'danger';
                                if ($minutesLeft <= 2) return 'warning';
                                return 'success';
                            }),
                    ])
                    ->columns(1),
            ]);
    }
}