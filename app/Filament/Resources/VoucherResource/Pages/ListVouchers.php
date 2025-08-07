<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use App\Filament\Resources\VoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Voucher;

class ListVouchers extends ListRecords
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Vouchers')
                ->badge(Voucher::count()),
            
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Voucher::STATUS_ACTIVE))
                ->badge(Voucher::where('status', Voucher::STATUS_ACTIVE)->count())
                ->badgeColor('success'),
            
            'redeemed' => Tab::make('Redeemed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Voucher::STATUS_REDEEMED))
                ->badge(Voucher::where('status', Voucher::STATUS_REDEEMED)->count())
                ->badgeColor('warning'),
            
            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Voucher::STATUS_EXPIRED))
                ->badge(Voucher::where('status', Voucher::STATUS_EXPIRED)->count())
                ->badgeColor('danger'),
            
            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Voucher::STATUS_CANCELLED))
                ->badge(Voucher::where('status', Voucher::STATUS_CANCELLED)->count())
                ->badgeColor('gray'),
        ];
    }
}