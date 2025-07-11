<?php

namespace App\Filament\Resources\AdTaskResource\Pages;

use App\Filament\Resources\AdTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdTasks extends ListRecords
{
    protected static string $resource = AdTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}