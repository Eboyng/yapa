<?php

namespace App\Filament\Resources\ChannelAdApplicationResource\Pages;

use App\Filament\Resources\ChannelAdApplicationResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditChannelAdApplication extends EditRecord
{
    protected static string $resource = ChannelAdApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldData = $record->toArray();
        $updatedRecord = parent::handleRecordUpdate($record, $data);
        
        // Log the update in audit log
        AuditLog::log(
            auth()->id(),
            $record->channel->user_id,
            'channel_ad_application_updated',
            $oldData,
            $updatedRecord->toArray(),
            "Updated channel ad application for: {$updatedRecord->channelAd->title}"
        );
        
        return $updatedRecord;
    }
}