<?php

namespace App\Filament\Resources\BatchShareResource\Pages;

use App\Filament\Resources\BatchShareResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\BatchShare;

class ListBatchShares extends ListRecords
{
    protected static string $resource = BatchShareResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Shares')
                ->badge(BatchShare::count()),
                
            'pending_rewards' => Tab::make('Pending Rewards')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('share_count', '>=', 10)->where('rewarded', false))
                ->badge(BatchShare::where('share_count', '>=', 10)->where('rewarded', false)->count())
                ->badgeColor('warning'),
                
            'rewarded' => Tab::make('Rewarded')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rewarded', true))
                ->badge(BatchShare::where('rewarded', true)->count())
                ->badgeColor('success'),
                
            'high_performers' => Tab::make('High Performers')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('share_count', '>=', 5))
                ->badge(BatchShare::where('share_count', '>=', 5)->count())
                ->badgeColor('info'),
                
            'whatsapp' => Tab::make('WhatsApp')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('platform', BatchShare::PLATFORM_WHATSAPP))
                ->badge(BatchShare::where('platform', BatchShare::PLATFORM_WHATSAPP)->count())
                ->badgeColor('success'),
                
            'facebook' => Tab::make('Facebook')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('platform', BatchShare::PLATFORM_FACEBOOK))
                ->badge(BatchShare::where('platform', BatchShare::PLATFORM_FACEBOOK)->count())
                ->badgeColor('primary'),
        ];
    }
}