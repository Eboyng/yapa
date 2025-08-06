<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Otp;

class ForceOtpCleanup extends Command
{
    protected $signature = 'otp:force-cleanup {phone} {context=whatsapp_change}';
    protected $description = 'Force cleanup OTP records for a specific phone number and context';

    public function handle()
    {
        $phone = $this->argument('phone');
        $context = $this->argument('context');
        
        $deleted = Otp::forceCleanup($phone, $context);
        
        $this->info("Force cleaned up {$deleted} OTP records for {$phone} with context '{$context}'.");
        
        return 0;
    }
}