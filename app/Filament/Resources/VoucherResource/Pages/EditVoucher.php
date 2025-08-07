<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use App\Filament\Resources\VoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Voucher;

class EditVoucher extends EditRecord
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        try {
            Log::info('Viewing voucher for edit', [
                'voucher_id' => $this->record->id,
                'code' => $this->record->code,
                'admin_id' => Auth::id(),
            ]);
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Error preparing voucher edit form', [
                'voucher_id' => $this->record->id ?? null,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);
            
            return $data;
        }
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        try {
            // Prevent editing of redeemed vouchers
            if ($this->record->status === Voucher::STATUS_REDEEMED) {
                throw new \Exception('Cannot edit a redeemed voucher');
            }
            
            // Log the changes being made
            $changes = [];
            foreach ($data as $key => $value) {
                if ($this->record->getAttribute($key) != $value) {
                    $changes[$key] = [
                        'from' => $this->record->getAttribute($key),
                        'to' => $value,
                    ];
                }
            }
            
            if (!empty($changes)) {
                Log::info('Updating voucher', [
                    'voucher_id' => $this->record->id,
                    'code' => $this->record->code,
                    'changes' => $changes,
                    'admin_id' => Auth::id(),
                ]);
            }
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Failed to prepare voucher data for update', [
                'voucher_id' => $this->record->id,
                'data' => $data,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);
            
            throw $e;
        }
    }
    
    protected function afterSave(): void
    {
        try {
            Log::info('Voucher updated successfully', [
                'voucher_id' => $this->record->id,
                'code' => $this->record->code,
                'status' => $this->record->status,
                'admin_id' => Auth::id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging voucher update', [
                'voucher_id' => $this->record->id ?? null,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);
        }
    }
}