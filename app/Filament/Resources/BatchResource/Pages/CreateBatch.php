<?php

namespace App\Filament\Resources\BatchResource\Pages;

use App\Filament\Resources\BatchResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBatch extends CreateRecord
{
    protected static string $resource = BatchResource::class;

    protected function afterCreate(): void
    {
        // Log the batch creation in audit log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'batch_created',
            'details' => 'Created batch: ' . $this->record->name . ' (ID: ' . $this->record->id . ')',
            'timestamp' => now(),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set created_by_admin to true since this is created through admin panel
        $data['created_by_admin'] = true;
        
        return $data;
    }
}