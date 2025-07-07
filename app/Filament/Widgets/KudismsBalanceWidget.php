<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Services\OtpService;
use Illuminate\Support\Facades\Cache;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class KudismsBalanceWidget extends Widget
{
    protected static string $view = 'filament.widgets.kudisms-balance-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    public $balance = null;
    public $lastUpdated = null;
    public $isLoading = false;
    
    public function mount(): void
    {
        $this->loadBalance();
    }
    
    public function loadBalance(): void
    {
        $this->isLoading = true;
        
        // Try to get cached balance first
        $cachedData = Cache::get('kudisms_balance');
        
        if ($cachedData) {
            $this->balance = $cachedData['balance'];
            $this->lastUpdated = $cachedData['updated_at'];
        }
        
        // If no cache or cache is older than 5 minutes, fetch fresh data
        if (!$cachedData || now()->diffInMinutes($cachedData['updated_at']) > 5) {
            $this->refreshBalance();
        }
        
        $this->isLoading = false;
    }
    
    public function refreshBalance(): void
    {
        $this->isLoading = true;
        
        try {
            $otpService = app(OtpService::class);
            $result = $otpService->getKudismsBalance();
            
            if ($result['success']) {
                $this->balance = $result['balance'];
                $this->lastUpdated = now();
                
                // Cache the result for 5 minutes
                Cache::put('kudisms_balance', [
                    'balance' => $this->balance,
                    'updated_at' => $this->lastUpdated,
                ], 300);
                
                Notification::make()
                    ->title('Balance Updated')
                    ->body('Kudisms balance has been refreshed successfully.')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Balance Update Failed')
                    ->body($result['message'])
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to fetch balance: ' . $e->getMessage())
                ->danger()
                ->send();
        }
        
        $this->isLoading = false;
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh Balance')
                ->icon('heroicon-m-arrow-path')
                ->action('refreshBalance')
                ->disabled(fn () => $this->isLoading),
        ];
    }
    
    public function getBalanceAmount(): string
    {
        if (!$this->balance) {
            return 'N/A';
        }
        
        // Handle different possible response formats
        if (is_array($this->balance)) {
            return $this->balance['balance'] ?? $this->balance['amount'] ?? 'N/A';
        }
        
        return (string) $this->balance;
    }
    
    public function getBalanceStatus(): string
    {
        $amount = $this->getBalanceAmount();
        
        if ($amount === 'N/A') {
            return 'unknown';
        }
        
        $numericAmount = (float) str_replace(',', '', $amount);
        
        if ($numericAmount > 1000) {
            return 'high';
        } elseif ($numericAmount > 100) {
            return 'medium';
        } elseif ($numericAmount > 0) {
            return 'low';
        } else {
            return 'empty';
        }
    }
    
    public function getLastUpdatedFormatted(): string
    {
        if (!$this->lastUpdated) {
            return 'Never';
        }
        
        return $this->lastUpdated->diffForHumans();
    }
}