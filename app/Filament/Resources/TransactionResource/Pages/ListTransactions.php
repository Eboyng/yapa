<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

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
                ->badge(Transaction::count()),
            
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(Transaction::where('status', 'pending')->count())
                ->badgeColor('warning'),
            
            'processing' => Tab::make('Processing')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processing'))
                ->badge(Transaction::where('status', 'processing')->count())
                ->badgeColor('info'),
            
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->badge(Transaction::where('status', 'completed')->count())
                ->badgeColor('success'),
            
            'failed' => Tab::make('Failed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'failed'))
                ->badge(Transaction::where('status', 'failed')->count())
                ->badgeColor('danger'),
            
            'credits' => Tab::make('Credits')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'credit'))
                ->badge(Transaction::where('type', 'credit')->count())
                ->badgeColor('success'),
            
            'debits' => Tab::make('Debits')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'debit'))
                ->badge(Transaction::where('type', 'debit')->count())
                ->badgeColor('danger'),
        ];
    }
}