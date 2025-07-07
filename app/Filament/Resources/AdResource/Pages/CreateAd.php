<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAd extends CreateRecord
{
    protected static string $resource = AdResource::class;

    protected function afterCreate(): void
    {
        // Log the ad creation in audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'ad_created',
            'details' => [
                'ad_id' => $this->record->id,
                'title' => $this->record->title,
                'type' => $this->record->type,
                'status' => $this->record->status,
                'budget' => $this->record->budget,
                'cost_per_view' => $this->record->cost_per_view,
                'created_by' => Auth::user()->name,
            ],
        ]);
    }
}