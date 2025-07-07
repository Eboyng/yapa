<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Log the transaction update in audit log
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'transaction_updated',
            'details' => [
                'transaction_id' => $this->record->id,
                'reference' => $this->record->reference,
                'amount' => $this->record->amount,
                'type' => $this->record->type,
                'category' => $this->record->category,
                'status' => $this->record->status,
                'user_id' => $this->record->user_id,
                'wallet_id' => $this->record->wallet_id,
                'updated_by' => Auth::user()->name,
            ],
        ]);
    }
}