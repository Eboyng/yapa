<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

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
                Section::make('Transaction Information')
                    ->schema([
                        TextEntry::make('reference')
                            ->label('Reference')
                            ->copyable(),
                        TextEntry::make('user.name')
                            ->label('User'),
                        TextEntry::make('user.email')
                            ->label('Email'),
                        TextEntry::make('wallet.type')
                            ->label('Wallet Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'credits' => 'primary',
                                'naira' => 'success',
                                'earnings' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money('NGN'),
                        TextEntry::make('type')
                            ->label('Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'credit' => 'success',
                                'debit' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('category')
                            ->label('Category')
                            ->badge(),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'processing' => 'info',
                                'completed' => 'success',
                                'failed' => 'danger',
                                'cancelled' => 'gray',
                                'reversed' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),
                
                Section::make('Payment Information')
                    ->schema([
                        TextEntry::make('payment_method')
                            ->label('Payment Method')
                            ->badge(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Related Transactions')
                    ->schema([
                        TextEntry::make('parent.reference')
                            ->label('Parent Transaction')
                            ->visible(fn ($record) => $record->parent_transaction_id)
                            ->url(fn ($record) => $record->parent ? route('filament.admin.resources.transactions.view', $record->parent) : null),
                        TextEntry::make('children_count')
                            ->label('Child Transactions')
                            ->state(fn ($record) => $record->children()->count())
                            ->visible(fn ($record) => $record->children()->count() > 0),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record->parent_transaction_id || $record->children()->count() > 0),
                
                Section::make('Metadata')
                    ->schema([
                        KeyValueEntry::make('metadata')
                            ->label('Additional Data')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->metadata)),
                
                Section::make('System Information')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                        TextEntry::make('processed_at')
                            ->label('Processed')
                            ->dateTime()
                            ->visible(fn ($record) => $record->processed_at),
                    ])
                    ->columns(3),
            ]);
    }
}