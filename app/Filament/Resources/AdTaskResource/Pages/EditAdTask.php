<?php

namespace App\Filament\Resources\AdTaskResource\Pages;

use App\Filament\Resources\AdTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdTask extends EditRecord
{
    protected static string $resource = AdTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}