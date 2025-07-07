<?php

namespace App\Filament\Resources\BatchResource\Pages;

use App\Filament\Resources\BatchResource;
use App\Models\Batch;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBatches extends ListRecords
{
    protected static string $resource = BatchResource::class;

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
                ->badge(Batch::query()->count()),
            'active' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active'))
                ->badge(Batch::query()->where('status', 'active')->count()),
            'closed' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'closed'))
                ->badge(Batch::query()->where('status', 'closed')->count()),
            'full' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'full'))
                ->badge(Batch::query()->where('status', 'full')->count()),
            'admin_created' => Tab::make('Admin Created')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_by_admin', true))
                ->badge(Batch::query()->where('created_by_admin', true)->count()),
        ];
    }
}