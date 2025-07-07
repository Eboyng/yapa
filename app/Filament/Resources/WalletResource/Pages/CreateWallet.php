<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Filament\Resources\WalletResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWallet extends CreateRecord
{
    protected static string $resource = WalletResource::class;

    protected function afterCreate(): void
    {
        // Log the wallet creation in audit log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'wallet_created',
            'details' => 'Created ' . $this->record->type . ' wallet for user: ' . $this->record->user->name . ' (ID: ' . $this->record->user_id . ')',
            'timestamp' => now(),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set initial version
        $data['version'] = 1;
        
        // Set currency based on wallet type if not provided
        if (empty($data['currency'])) {
            $data['currency'] = $data['type'] === 'credits' ? 'CREDITS' : 'NGN';
        }
        
        return $data;
    }
}