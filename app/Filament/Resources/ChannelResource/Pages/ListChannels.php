<?php

namespace App\Filament\Resources\ChannelResource\Pages;

use App\Filament\Resources\ChannelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Channel;

class ListChannels extends ListRecords
{
    protected static string $resource = ChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Channels'),
            'pending' => Tab::make('Pending Approval')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Channel::STATUS_PENDING))
                ->badge(Channel::where('status', Channel::STATUS_PENDING)->count())
                ->badgeColor('warning'),
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Channel::STATUS_APPROVED))
                ->badge(Channel::where('status', Channel::STATUS_APPROVED)->count())
                ->badgeColor('success'),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Channel::STATUS_REJECTED))
                ->badge(Channel::where('status', Channel::STATUS_REJECTED)->count())
                ->badgeColor('danger'),
            'featured' => Tab::make('Featured')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_featured', true))
                ->badge(Channel::where('is_featured', true)->count())
                ->badgeColor('info'),
        ];
    }
}