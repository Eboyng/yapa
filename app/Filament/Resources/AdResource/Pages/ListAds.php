<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use App\Models\Ad;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAds extends ListRecords
{
    protected static string $resource = AdResource::class;

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
                ->badge(Ad::count()),
            
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft'))
                ->badge(Ad::where('status', 'draft')->count())
                ->badgeColor('gray'),
            
            'pending' => Tab::make('Pending Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(Ad::where('status', 'pending')->count())
                ->badgeColor('warning'),
            
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved'))
                ->badge(Ad::where('status', 'approved')->count())
                ->badgeColor('info'),
            
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active'))
                ->badge(Ad::where('status', 'active')->count())
                ->badgeColor('success'),
            
            'paused' => Tab::make('Paused')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paused'))
                ->badge(Ad::where('status', 'paused')->count())
                ->badgeColor('warning'),
            
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected'))
                ->badge(Ad::where('status', 'rejected')->count())
                ->badgeColor('danger'),
            
            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'expired'))
                ->badge(Ad::where('status', 'expired')->count())
                ->badgeColor('danger'),
        ];
    }
}