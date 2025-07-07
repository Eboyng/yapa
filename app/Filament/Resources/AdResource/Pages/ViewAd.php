<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;

class ViewAd extends ViewRecord
{
    protected static string $resource = AdResource::class;

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
                Section::make('Ad Information')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        TextEntry::make('url')
                            ->label('Target URL')
                            ->url(fn ($state) => $state)
                            ->openUrlInNewTab()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Media')
                    ->schema([
                        ImageEntry::make('image_url')
                            ->label('Ad Image')
                            ->height(200)
                            ->visible(fn ($record) => $record->image_url),
                        TextEntry::make('video_url')
                            ->label('Video URL')
                            ->url(fn ($state) => $state)
                            ->openUrlInNewTab()
                            ->visible(fn ($record) => $record->video_url),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record->image_url || $record->video_url),
                
                Section::make('Settings')
                    ->schema([
                        TextEntry::make('type')
                            ->label('Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'banner' => 'primary',
                                'video' => 'success',
                                'text' => 'info',
                                'interactive' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'pending' => 'warning',
                                'approved' => 'info',
                                'rejected' => 'danger',
                                'active' => 'success',
                                'paused' => 'warning',
                                'expired' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('budget')
                            ->label('Budget')
                            ->money('NGN'),
                        TextEntry::make('cost_per_view')
                            ->label('Cost per View')
                            ->money('NGN'),
                        TextEntry::make('max_views')
                            ->label('Maximum Views')
                            ->numeric(),
                        TextEntry::make('duration')
                            ->label('Duration (seconds)')
                            ->numeric()
                            ->visible(fn ($record) => $record->duration),
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                    ])
                    ->columns(2),
                
                Section::make('Performance')
                    ->schema([
                        TextEntry::make('views')
                            ->label('Total Views')
                            ->numeric(),
                        TextEntry::make('clicks')
                            ->label('Total Clicks')
                            ->numeric(),
                        TextEntry::make('click_rate')
                            ->label('Click Rate')
                            ->state(fn ($record) => $record->views > 0 ? round(($record->clicks / $record->views) * 100, 2) . '%' : '0%'),
                        TextEntry::make('total_spent')
                            ->label('Total Spent')
                            ->state(fn ($record) => '₦' . number_format($record->views * $record->cost_per_view, 2)),
                        TextEntry::make('remaining_budget')
                            ->label('Remaining Budget')
                            ->state(fn ($record) => '₦' . number_format($record->budget - ($record->views * $record->cost_per_view), 2)),
                    ])
                    ->columns(3),
                
                Section::make('Scheduling')
                    ->schema([
                        TextEntry::make('start_date')
                            ->label('Start Date')
                            ->dateTime(),
                        TextEntry::make('end_date')
                            ->label('End Date')
                            ->dateTime()
                            ->placeholder('No end date set'),
                    ])
                    ->columns(2),
                
                Section::make('Targeting')
                    ->schema([
                        TextEntry::make('target_audience')
                            ->label('Target Audience')
                            ->badge()
                            ->separator(', '),
                        KeyValueEntry::make('targeting_criteria')
                            ->label('Additional Targeting')
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->targeting_criteria)),
                    ])
                    ->columns(1),
                
                Section::make('System Information')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}