<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_number')->unique()->nullable()->after('email');
            $table->integer('credits_balance')->default(100)->after('password');
            $table->decimal('naira_balance', 10, 2)->default(0)->after('credits_balance');
            $table->decimal('earnings_balance', 10, 2)->default(0)->after('naira_balance');
            $table->string('location')->nullable()->after('earnings_balance');
            $table->text('bvn')->nullable()->after('location'); // Will be encrypted
            $table->timestamp('whatsapp_verified_at')->nullable()->after('email_verified_at');
            $table->boolean('email_verification_enabled')->default(false)->after('whatsapp_verified_at');
            $table->integer('otp_attempts')->default(0)->after('email_verification_enabled');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_attempts');
            $table->string('pending_whatsapp_number')->nullable()->after('otp_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_number',
                'credits_balance',
                'naira_balance',
                'earnings_balance',
                'location',
                'bvn',
                'whatsapp_verified_at',
                'email_verification_enabled',
                'otp_attempts',
                'otp_expires_at',
                'pending_whatsapp_number'
            ]);
        });
    }
};