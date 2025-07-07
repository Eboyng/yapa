<?php

namespace App\Filament\Resources\NotificationLogResource\Pages;

use App\Filament\Resources\NotificationLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListNotificationLogs extends ListRecords
{
    protected static string $resource = NotificationLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Disable create action as logs are auto-generated
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => $this->getModel()::count())
                ->badgeColor('gray'),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => $this->getModel()::where('status', 'pending')->count())
                ->badgeColor('warning'),
            'sent' => Tab::make('Sent')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'sent'))
                ->badge(fn () => $this->getModel()::where('status', 'sent')->count())
                ->badgeColor('info'),
            'delivered' => Tab::make('Delivered')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'delivered'))
                ->badge(fn () => $this->getModel()::where('status', 'delivered')->count())
                ->badgeColor('success'),
            'failed' => Tab::make('Failed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'failed'))
                ->badge(fn () => $this->getModel()::where('status', 'failed')->count())
                ->badgeColor('danger'),
            'read' => Tab::make('Read')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'read'))
                ->badge(fn () => $this->getModel()::where('status', 'read')->count())
                ->badgeColor('success'),
        ];
    }
}