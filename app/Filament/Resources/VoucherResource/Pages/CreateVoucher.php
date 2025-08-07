<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use App\Filament\Resources\VoucherResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\VoucherService;

class CreateVoucher extends CreateRecord
{
    protected static string $resource = VoucherResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            // Auto-generate code if not provided
            if (empty($data['code'])) {
                $voucherService = app(VoucherService::class);
                $data['code'] = $voucherService->generateUniqueCode();
            }
            
            // Set created_by to current admin
            $data['created_by'] = Auth::id();
            
            Log::info('Creating new voucher', [
                'code' => $data['code'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'admin_id' => Auth::id(),
            ]);
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Failed to prepare voucher data for creation', [
                'data' => $data,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);
            
            throw $e;
        }
    }
    
    protected function afterCreate(): void
    {
        try {
            Log::info('Voucher created successfully', [
                'voucher_id' => $this->record->id,
                'code' => $this->record->code,
                'amount' => $this->record->amount,
                'admin_id' => Auth::id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging voucher creation', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);
        }
    }
}