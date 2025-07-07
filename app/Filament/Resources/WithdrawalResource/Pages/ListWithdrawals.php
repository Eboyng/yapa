<?php

namespace App\Filament\Resources\WithdrawalResource\Pages;

use App\Filament\Resources\WithdrawalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;

class ListWithdrawals extends ListRecords
{
    protected static string $resource = WithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Remove create action since withdrawals are created by users, not admins
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Requests')
                ->badge(fn () => Transaction::where('category', Transaction::CATEGORY_WITHDRAWAL)->count()),
                
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Transaction::STATUS_PENDING))
                ->badge(fn () => Transaction::where('category', Transaction::CATEGORY_WITHDRAWAL)
                    ->where('status', Transaction::STATUS_PENDING)->count())
                ->badgeColor('warning'),
                
            'processing' => Tab::make('Processing')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Transaction::STATUS_PROCESSING))
                ->badge(fn () => Transaction::where('category', Transaction::CATEGORY_WITHDRAWAL)
                    ->where('status', Transaction::STATUS_PROCESSING)->count())
                ->badgeColor('info'),
                
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Transaction::STATUS_COMPLETED))
                ->badge(fn () => Transaction::where('category', Transaction::CATEGORY_WITHDRAWAL)
                    ->where('status', Transaction::STATUS_COMPLETED)->count())
                ->badgeColor('success'),
                
            'failed' => Tab::make('Failed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Transaction::STATUS_FAILED))
                ->badge(fn () => Transaction::where('category', Transaction::CATEGORY_WITHDRAWAL)
                    ->where('status', Transaction::STATUS_FAILED)->count())
                ->badgeColor('danger'),
                
            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Transaction::STATUS_CANCELLED))
                ->badge(fn () => Transaction::where('category', Transaction::CATEGORY_WITHDRAWAL)
                    ->where('status', Transaction::STATUS_CANCELLED)->count())
                ->badgeColor('gray'),
        ];
    }
}