<?php

namespace App\Filament\Resources\ChannelAdResource\Pages;

use App\Filament\Resources\ChannelAdResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateChannelAd extends CreateRecord
{
    protected static string $resource = ChannelAdResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the admin user who created the ad
        $data['admin_user_id'] = auth()->id();
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation($data);
        
        // Log the creation in audit log
        \App\Models\AuditLog::log(
            auth()->id(),
            null,
            'channel_ad_created',
            null,
            $record->toArray(),
            "Created channel ad: {$record->title}"
        );
        
        return $record;
    }
}