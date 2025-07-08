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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('whatsapp_number')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('credits_balance')->default(100);
            $table->decimal('naira_balance', 10, 2)->default(0);
            $table->decimal('earnings_balance', 10, 2)->default(0);
            $table->string('location')->nullable();
            $table->text('bvn')->nullable(); // Will be encrypted
            $table->timestamp('whatsapp_verified_at')->nullable();
            $table->boolean('email_verification_enabled')->default(false);
            $table->integer('otp_attempts')->default(0);
            $table->timestamp('otp_expires_at')->nullable();
            $table->string('pending_whatsapp_number')->nullable();
            $table->string('otp_code')->nullable();
            $table->boolean('notification_enabled')->default(true);
            $table->string('google_id')->nullable();
            $table->string('google_token')->nullable();
             $table->boolean('whatsapp_notifications_enabled')->default(true);
            $table->boolean('email_notifications_enabled')->default(true);
            $table->string('google_refresh_token')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('referral_code', 8)->unique()->nullable();
            $table->unsignedBigInteger('referred_by')->nullable();
            $table->timestamp('referred_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['referral_code']);
            $table->index(['referred_by']);
            $table->index('last_login_at');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
