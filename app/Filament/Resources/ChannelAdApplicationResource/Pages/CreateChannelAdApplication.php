<?php

namespace App\Filament\Resources\ChannelAdApplicationResource\Pages;

use App\Filament\Resources\ChannelAdApplicationResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateChannelAdApplication extends CreateRecord
{
    protected static string $resource = ChannelAdApplicationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation($data);
        
        // Log the creation in audit log
        AuditLog::log(
            auth()->id(),
            $record->channel->user_id,
            'channel_ad_application_created',
            null,
            $record->toArray(),
            "Created channel ad application for: {$record->channelAd->title}"
        );
        
        return $record;
    }
}