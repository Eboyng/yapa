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
        Schema::create('pending_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('whatsapp_number')->unique();
            $table->string('password');
            $table->string('otp_code')->nullable();
            $table->integer('otp_attempts')->default(0);
            $table->timestamp('otp_expires_at')->nullable();
            $table->integer('resend_attempts')->default(0);
            $table->timestamp('last_resend_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('expires_at')->nullable(); // 24 hours from creation
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_users');
    }
};