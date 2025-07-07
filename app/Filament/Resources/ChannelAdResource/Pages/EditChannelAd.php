<?php

namespace App\Filament\Resources\ChannelAdResource\Pages;

use App\Filament\Resources\ChannelAdResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditChannelAd extends EditRecord
{
    protected static string $resource = ChannelAdResource::class;

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
            null,
            'channel_ad_updated',
            $oldData,
            $updatedRecord->toArray(),
            "Updated channel ad: {$updatedRecord->title}"
        );
        
        return $updatedRecord;
    }
}