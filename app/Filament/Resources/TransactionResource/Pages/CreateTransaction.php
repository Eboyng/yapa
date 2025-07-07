<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate reference if not provided
        if (empty($data['reference'])) {
            $data['reference'] = 'TXN-' . strtoupper(Str::random(10));
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Log the transaction creation in audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'transaction_created',
            'details' => [
                'transaction_id' => $this->record->id,
                'reference' => $this->record->reference,
                'amount' => $this->record->amount,
                'type' => $this->record->type,
                'category' => $this->record->category,
                'status' => $this->record->status,
                'user_id' => $this->record->user_id,
                'wallet_id' => $this->record->wallet_id,
                'created_by' => Auth::user()->name,
            ],
        ]);
    }
}