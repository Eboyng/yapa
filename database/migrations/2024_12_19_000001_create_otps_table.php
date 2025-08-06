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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // phone number, email, etc.
            $table->string('otp_code');
            $table->string('context'); // 'whatsapp_change', 'login', 'registration'
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index(['identifier', 'context']);
            $table->index('expires_at');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};