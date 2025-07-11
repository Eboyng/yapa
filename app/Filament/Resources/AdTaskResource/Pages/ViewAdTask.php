<?php

namespace App\Filament\Resources\AdTaskResource\Pages;

use App\Filament\Resources\AdTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\BadgeEntry;

class ViewAdTask extends ViewRecord
{
    protected static string $resource = AdTaskResource::class;

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
                Section::make('Task Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Task ID'),
                        TextEntry::make('user.name')
                            ->label('User'),
                        TextEntry::make('user.email')
                            ->label('User Email'),
                        TextEntry::make('ad.title')
                            ->label('Advertisement'),
                        BadgeEntry::make('status')
                            ->label('Status')
                            ->colors([
                                'primary' => 'active',
                                'warning' => 'pending_review',
                                'success' => 'approved',
                                'danger' => 'rejected',
                                'success' => 'completed',
                                'secondary' => 'expired',
                            ]),
                    ])
                    ->columns(2),
                
                Section::make('Task Details')
                    ->schema([
                        TextEntry::make('view_count')
                            ->label('View Count')
                            ->numeric(),
                        TextEntry::make('earnings_amount')
                            ->label('Earnings Amount')
                            ->money('NGN'),
                        TextEntry::make('started_at')
                            ->label('Started At')
                            ->dateTime(),
                        TextEntry::make('screenshot_uploaded_at')
                            ->label('Screenshot Uploaded At')
                            ->dateTime(),
                        ImageEntry::make('screenshot_path')
                            ->label('Screenshot')
                            ->disk('public')
                            ->height(200)
                            ->width(200),
                    ])
                    ->columns(2),
                
                Section::make('Review Information')
                    ->schema([
                        TextEntry::make('reviewed_at')
                            ->label('Reviewed At')
                            ->dateTime(),
                        TextEntry::make('reviewedByAdmin.name')
                            ->label('Reviewed By'),
                        TextEntry::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record->reviewed_at),
                
                Section::make('Appeal Information')
                    ->schema([
                        TextEntry::make('appeal_message')
                            ->label('Appeal Message')
                            ->columnSpanFull(),
                        BadgeEntry::make('appeal_status')
                            ->label('Appeal Status')
                            ->colors([
                                'warning' => 'pending',
                                'success' => 'approved',
                                'danger' => 'rejected',
                            ]),
                        TextEntry::make('appeal_submitted_at')
                            ->label('Appeal Submitted At')
                            ->dateTime(),
                        TextEntry::make('appeal_reviewed_at')
                            ->label('Appeal Reviewed At')
                            ->dateTime(),
                        TextEntry::make('appealReviewedByAdmin.name')
                            ->label('Appeal Reviewed By'),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record->appeal_submitted_at),
            ]);
    }
}