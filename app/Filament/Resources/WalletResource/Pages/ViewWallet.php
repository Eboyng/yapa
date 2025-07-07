<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Filament\Resources\WalletResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;

class ViewWallet extends ViewRecord
{
    protected static string $resource = WalletResource::class;

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
                Section::make('Wallet Information')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User'),
                        TextEntry::make('user.email')
                            ->label('Email'),
                        TextEntry::make('type')
                            ->label('Wallet Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'credits' => 'primary',
                                'naira' => 'success',
                                'earnings' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('formatted_balance')
                            ->label('Current Balance'),
                        TextEntry::make('currency')
                            ->label('Currency'),
                        IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean(),
                    ])
                    ->columns(2),
                
                Section::make('Transaction Statistics')
                    ->schema([
                        TextEntry::make('transactions_count')
                            ->label('Total Transactions')
                            ->state(fn ($record) => $record->transactions()->count()),
                        TextEntry::make('total_credits')
                            ->label('Total Credits')
                            ->state(fn ($record) => '₦' . number_format($record->transactions()->where('type', 'credit')->sum('amount'), 2)),
                        TextEntry::make('total_debits')
                            ->label('Total Debits')
                            ->state(fn ($record) => '₦' . number_format($record->transactions()->where('type', 'debit')->sum('amount'), 2)),
                        TextEntry::make('last_transaction')
                            ->label('Last Transaction')
                            ->state(fn ($record) => $record->transactions()->latest()->first()?->created_at?->diffForHumans() ?? 'No transactions'),
                    ])
                    ->columns(2),
                
                Section::make('System Information')
                    ->schema([
                        TextEntry::make('version')
                            ->label('Version'),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(3),
            ]);
    }
}