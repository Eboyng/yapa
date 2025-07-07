<?php

namespace App\Filament\Resources\ChannelAdResource\Pages;

use App\Filament\Resources\ChannelAdResource;
use App\Models\ChannelAd;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListChannelAds extends ListRecords
{
    protected static string $resource = ChannelAdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(ChannelAd::count()),
            
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChannelAd::STATUS_DRAFT))
                ->badge(ChannelAd::where('status', ChannelAd::STATUS_DRAFT)->count())
                ->badgeColor('gray'),
            
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChannelAd::STATUS_ACTIVE))
                ->badge(ChannelAd::where('status', ChannelAd::STATUS_ACTIVE)->count())
                ->badgeColor('success'),
            
            'paused' => Tab::make('Paused')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChannelAd::STATUS_PAUSED))
                ->badge(ChannelAd::where('status', ChannelAd::STATUS_PAUSED)->count())
                ->badgeColor('warning'),
            
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChannelAd::STATUS_COMPLETED))
                ->badge(ChannelAd::where('status', ChannelAd::STATUS_COMPLETED)->count())
                ->badgeColor('info'),
            
            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChannelAd::STATUS_EXPIRED))
                ->badge(ChannelAd::where('status', ChannelAd::STATUS_EXPIRED)->count())
                ->badgeColor('danger'),
            
            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChannelAd::STATUS_CANCELLED))
                ->badge(ChannelAd::where('status', ChannelAd::STATUS_CANCELLED)->count())
                ->badgeColor('gray'),
        ];
    }
}