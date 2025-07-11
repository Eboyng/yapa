<?php

namespace App\Filament\Resources\BatchShareResource\Pages;

use App\Filament\Resources\BatchShareResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBatchShare extends EditRecord
{
    protected static string $resource = BatchShareResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}