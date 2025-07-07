<?php

namespace App\Filament\Resources\AuditLogResource\Pages;

use App\Filament\Resources\AuditLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action for audit logs
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => $this->getModel()::count())
                ->badgeColor('gray'),
            'user_actions' => Tab::make('User Actions')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('action', 'like', 'user_%'))
                ->badge(fn () => $this->getModel()::where('action', 'like', 'user_%')->count())
                ->badgeColor('primary'),
            'batch_actions' => Tab::make('Batch Actions')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('action', 'like', 'batch_%'))
                ->badge(fn () => $this->getModel()::where('action', 'like', 'batch_%')->count())
                ->badgeColor('info'),
            'ad_actions' => Tab::make('Ad Actions')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('action', 'like', 'ad_%'))
                ->badge(fn () => $this->getModel()::where('action', 'like', 'ad_%')->count())
                ->badgeColor('warning'),
            'transaction_actions' => Tab::make('Transaction Actions')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('action', 'like', 'transaction_%'))
                ->badge(fn () => $this->getModel()::where('action', 'like', 'transaction_%')->count())
                ->badgeColor('success'),
            'today' => Tab::make('Today')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', today()))
                ->badge(fn () => $this->getModel()::whereDate('created_at', today())->count())
                ->badgeColor('danger'),
        ];
    }
}