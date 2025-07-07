<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditAd extends EditRecord
{
    protected static string $resource = AdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Log the ad update in audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'ad_updated',
            'details' => [
                'ad_id' => $this->record->id,
                'title' => $this->record->title,
                'type' => $this->record->type,
                'status' => $this->record->status,
                'budget' => $this->record->budget,
                'cost_per_view' => $this->record->cost_per_view,
                'is_active' => $this->record->is_active,
                'updated_by' => Auth::user()->name,
            ],
        ]);
    }
}