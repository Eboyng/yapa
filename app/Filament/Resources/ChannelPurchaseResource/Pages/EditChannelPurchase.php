<?php

namespace App\Filament\Resources\ChannelPurchaseResource\Pages;

use App\Filament\Resources\ChannelPurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChannelPurchase extends EditRecord
{
    protected static string $resource = ChannelPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}