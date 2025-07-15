<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OtpService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Log;

class TestOtpCommand extends Command
{
    protected $signature = 'test:otp {phone}';
    protected $description = 'Test OTP sending functionality';

    public function handle()
    {
        $phone = $this->argument('phone');
        
        $this->info('Testing OTP service...');
        
        try {
            $otpService = app(OtpService::class);
            $settingService = app(SettingService::class);
            
            // Check settings first
            $this->info('Checking Kudisms settings...');
            $kudismsSettings = $settingService->getKudismsSettings();
            
            foreach ($kudismsSettings as $key => $value) {
                $displayValue = $value ? (strlen($value) > 10 ? substr($value, 0, 10) . '...' : $value) : 'NULL';
                $this->line("$key: $displayValue");
            }
            
            // Check notification settings
            $this->info('\nChecking notification settings...');
            $notificationSettings = $settingService->getNotificationSettings();
            foreach ($notificationSettings as $key => $value) {
                $this->line("$key: " . ($value ? 'true' : 'false'));
            }
            
            // Test OTP sending
            $this->info('\nSending test OTP...');
            $result = $otpService->sendOtp($phone, 'Your test OTP is {otp}', null, true);
            
            $this->info('Result:');
            $this->line('Success: ' . ($result['success'] ? 'true' : 'false'));
            $this->line('Method: ' . ($result['method'] ?? 'none'));
            $this->line('Message: ' . $result['message']);
            
            if (isset($result['otp'])) {
                $this->line('OTP: ' . $result['otp']);
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
        }
    }
}