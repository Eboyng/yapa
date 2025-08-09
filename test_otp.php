<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Otp;
use Illuminate\Support\Facades\Log;

echo "Testing OTP functionality...\n";

// Clear existing OTPs for this number
$deleted = Otp::where('identifier', '+2348158733135')->delete();
echo "Deleted {$deleted} existing OTP records\n";

// Generate a new OTP
$result = Otp::generate('+2348158733135', 'registration');
echo "Generated OTP: {$result['otp']}\n";
echo "OTP Record ID: {$result['record']->id}\n";

// Try to verify with the correct OTP
$verifyResult = Otp::verify('+2348158733135', $result['otp'], 'registration');
echo "Verification result: " . json_encode($verifyResult) . "\n";

// Check if OTP record exists
$otpRecord = Otp::where('identifier', '+2348158733135')
    ->where('context', 'registration')
    ->where('verified', false)
    ->first();

if ($otpRecord) {
    echo "Found unverified OTP record:\n";
    echo "ID: {$otpRecord->id}\n";
    echo "OTP Code (hashed): {$otpRecord->otp_code}\n";
    echo "Context: {$otpRecord->context}\n";
    echo "Verified: " . ($otpRecord->verified ? 'Yes' : 'No') . "\n";
    echo "Attempts: {$otpRecord->attempts}\n";
    echo "Expires at: {$otpRecord->expires_at}\n";
} else {
    echo "No unverified OTP record found\n";
}

echo "Test completed.\n";