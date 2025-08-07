<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use App\Filament\Resources\VoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use App\Models\Voucher;

class ViewVoucher extends ViewRecord
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Voucher Information')
                    ->schema([
                        TextEntry::make('code')
                            ->label('Voucher Code')
                            ->copyable()
                            ->fontFamily('mono')
                            ->size('lg')
                            ->weight('bold'),
                        
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money('NGN')
                            ->size('lg')
                            ->weight('bold'),
                        
                        TextEntry::make('currency')
                            ->label('Currency')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                Voucher::CURRENCY_NGN => 'success',
                                Voucher::CURRENCY_CREDITS => 'info',
                                default => 'gray',
                            }),
                        
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                Voucher::STATUS_ACTIVE => 'success',
                                Voucher::STATUS_REDEEMED => 'warning',
                                Voucher::STATUS_EXPIRED => 'danger',
                                Voucher::STATUS_CANCELLED => 'gray',
                                default => 'gray',
                            }),
                        
                        TextEntry::make('description')
                            ->label('Description')
                            ->placeholder('No description provided'),
                        
                        TextEntry::make('batch_id')
                            ->label('Batch ID')
                            ->placeholder('No batch'),
                    ])
                    ->columns(2),
                
                Section::make('Dates & Timeline')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        
                        TextEntry::make('expires_at')
                            ->label('Expires At')
                            ->dateTime()
                            ->placeholder('Never expires'),
                        
                        TextEntry::make('redeemed_at')
                            ->label('Redeemed At')
                            ->dateTime()
                            ->placeholder('Not redeemed'),
                        
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(2),
                
                Section::make('User Information')
                    ->schema([
                        TextEntry::make('createdBy.name')
                            ->label('Created By')
                            ->placeholder('System'),
                        
                        TextEntry::make('createdBy.email')
                            ->label('Creator Email')
                            ->placeholder('N/A'),
                        
                        TextEntry::make('redeemedBy.name')
                            ->label('Redeemed By')
                            ->placeholder('Not redeemed'),
                        
                        TextEntry::make('redeemedBy.email')
                            ->label('Redeemer Email')
                            ->placeholder('N/A'),
                    ])
                    ->columns(2)
                    ->visible(fn (Voucher $record): bool => $record->created_by || $record->redeemed_by),
                
                Section::make('Additional Data')
                    ->schema([
                        KeyValueEntry::make('metadata')
                            ->label('Metadata')
                            ->placeholder('No additional data'),
                    ])
                    ->visible(fn (Voucher $record): bool => !empty($record->metadata)),
            ]);
    }
}