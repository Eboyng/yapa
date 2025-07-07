<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Filament\Resources\WalletResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditWallet extends EditRecord
{
    protected static string $resource = WalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Log the wallet update in audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'wallet_updated',
            'details' => [
                'wallet_id' => $this->record->id,
                'user_id' => $this->record->user_id,
                'type' => $this->record->type,
                'balance' => $this->record->balance,
                'currency' => $this->record->currency,
                'is_active' => $this->record->is_active,
                'updated_by' => Auth::user()->name,
            ],
        ]);
    }
}