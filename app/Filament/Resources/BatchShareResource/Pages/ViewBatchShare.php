<?php

namespace App\Filament\Resources\BatchShareResource\Pages;

use App\Filament\Resources\BatchShareResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;

class ViewBatchShare extends ViewRecord
{
    protected static string $resource = BatchShareResource::class;

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
                Section::make('Share Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('User'),
                                TextEntry::make('batch.name')
                                    ->label('Batch'),
                                TextEntry::make('platform')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'whatsapp' => 'WhatsApp',
                                        'facebook' => 'Facebook',
                                        'twitter' => 'Twitter',
                                        'copy_link' => 'Copy Link',
                                        default => $state,
                                    }),
                                TextEntry::make('share_count')
                                    ->label('Referrals Count')
                                    ->badge()
                                    ->color(fn (int $state): string => match (true) {
                                        $state >= 10 => 'success',
                                        $state >= 5 => 'warning',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),
                    
                Section::make('Reward Status')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                IconEntry::make('rewarded')
                                    ->label('Rewarded')
                                    ->boolean(),
                                TextEntry::make('progress')
                                    ->label('Progress')
                                    ->getStateUsing(fn ($record): string => "{$record->share_count}/10")
                                    ->badge()
                                    ->color(fn ($record): string => match (true) {
                                        $record->share_count >= 10 => 'success',
                                        $record->share_count >= 5 => 'warning',
                                        default => 'gray',
                                    }),
                                TextEntry::make('reward_status')
                                    ->label('Status')
                                    ->getStateUsing(fn ($record): string => match (true) {
                                        $record->rewarded => '✅ Rewarded',
                                        $record->share_count >= 10 => '🎁 Pending Reward',
                                        default => '⏳ In Progress',
                                    })
                                    ->badge()
                                    ->color(fn ($record): string => match (true) {
                                        $record->rewarded => 'success',
                                        $record->share_count >= 10 => 'warning',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),
                    
                Section::make('Timestamps')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Shared At')
                                    ->dateTime(),
                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime(),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}