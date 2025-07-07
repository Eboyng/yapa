<?php

namespace App\Filament\Resources\BatchResource\Pages;

use App\Filament\Resources\BatchResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBatch extends EditRecord
{
    protected static string $resource = BatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Log the batch update in audit log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'batch_updated',
            'details' => 'Updated batch: ' . $this->record->name . ' (ID: ' . $this->record->id . ')',
            'timestamp' => now(),
        ]);
    }
}