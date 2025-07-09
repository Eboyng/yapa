<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\SettingService;
use Carbon\Carbon;

class MaintenanceCountdown extends Component
{
    public $endTime;
    public $timeRemaining = [];
    public $isMaintenanceEnded = false;
    
    protected $settingService;
    
    public function mount()
    {
        $this->settingService = app(SettingService::class);
        $this->endTime = $this->settingService->getMaintenanceEndTime();
        $this->updateCountdown();
    }
    
    public function updateCountdown()
    {
        if (!$this->endTime) {
            $this->timeRemaining = [];
            return;
        }
        
        $now = Carbon::now();
        
        if ($now->greaterThanOrEqualTo($this->endTime)) {
            $this->isMaintenanceEnded = true;
            $this->timeRemaining = [];
            return;
        }
        
        $diff = $now->diff($this->endTime);
        
        $this->timeRemaining = [
            'days' => $diff->days,
            'hours' => $diff->h,
            'minutes' => $diff->i,
            'seconds' => $diff->s,
        ];
    }
    
    public function checkMaintenanceStatus()
    {
        // Check if maintenance mode has been disabled
        if (!$this->settingService->isMaintenanceMode()) {
            return redirect()->to('/');
        }
        
        // Check if maintenance period has ended
        if ($this->settingService->isMaintenancePeriodEnded()) {
            return redirect()->to('/');
        }
        
        $this->updateCountdown();
    }
    
    public function render()
    {
        return view('livewire.maintenance-countdown');
    }
}