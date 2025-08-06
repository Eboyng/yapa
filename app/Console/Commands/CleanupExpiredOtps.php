<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Otp;

class CleanupExpiredOtps extends Command
{
    protected $signature = 'otp:cleanup';
    protected $description = 'Clean up expired OTP records';

    public function handle()
    {
        $deletedCount = Otp::cleanupExpired();
        
        $this->info("Cleaned up {$deletedCount} expired OTP records.");
        
        return 0;
    }
}