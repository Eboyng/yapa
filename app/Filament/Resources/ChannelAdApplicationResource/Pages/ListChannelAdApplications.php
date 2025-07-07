<?php

namespace App\Filament\Resources\ChannelAdApplicationResource\Pages;

use App\Filament\Resources\ChannelAdApplicationResource;
use App\Models\ChannelAdApplication;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListChannelAdApplications extends ListRecords
{
    protected static string $resource = ChannelAdApplicationResource::class;

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
                ->badge(ChannelAdApplication::count()),
            
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChannelAdApplication::STATUS_PENDING))
                ->badge(ChannelAdApplication::where('status', ChannelAdApplication::STATUS_PENDING)->count())
                ->badgeColor('warning'),
            
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChannelAdApplication::STATUS_APPROVED))
                ->badge(ChannelAdApplication::where('status', ChannelAdApplication::STATUS_APPROVED)->count())
                ->badgeColor('success'),
            
            'proof_submitted' => Tab::make('Proof Submitted')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChannelAdApplication::STATUS_PROOF_SUBMITTED))
                ->badge(ChannelAdApplication::where('status', ChannelAdApplication::STATUS_PROOF_SUBMITTED)->count())
                ->badgeColor('info'),
            
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChannelAdApplication::STATUS_COMPLETED))
                ->badge(ChannelAdApplication::where('status', ChannelAdApplication::STATUS_COMPLETED)->count())
                ->badgeColor('success'),
            
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChannelAdApplication::STATUS_REJECTED))
                ->badge(ChannelAdApplication::where('status', ChannelAdApplication::STATUS_REJECTED)->count())
                ->badgeColor('danger'),
            
            'disputed' => Tab::make('Disputed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', ChannelAdApplication::STATUS_DISPUTED))
                ->badge(ChannelAdApplication::where('status', ChannelAdApplication::STATUS_DISPUTED)->count())
                ->badgeColor('danger'),
        ];
    }
}