<?php

namespace App\Filament\Resources\ChannelSaleResource\Pages;

use App\Filament\Resources\ChannelSaleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChannelSale extends EditRecord
{
    protected static string $resource = ChannelSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}