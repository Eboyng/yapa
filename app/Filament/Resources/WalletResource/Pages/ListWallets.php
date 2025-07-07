<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Filament\Resources\WalletResource;
use App\Models\Wallet;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListWallets extends ListRecords
{
    protected static string $resource = WalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->badge(Wallet::query()->count()),
            'credits' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', Wallet::TYPE_CREDITS))
                ->badge(Wallet::query()->where('type', Wallet::TYPE_CREDITS)->count()),
            'naira' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', Wallet::TYPE_NAIRA))
                ->badge(Wallet::query()->where('type', Wallet::TYPE_NAIRA)->count()),
            'earnings' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', Wallet::TYPE_EARNINGS))
                ->badge(Wallet::query()->where('type', Wallet::TYPE_EARNINGS)->count()),
            'active' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge(Wallet::query()->where('is_active', true)->count()),
            'with_balance' => Tab::make('With Balance')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('balance', '>', 0))
                ->badge(Wallet::query()->where('balance', '>', 0)->count()),
        ];
    }
}