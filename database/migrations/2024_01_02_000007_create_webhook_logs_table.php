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
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source'); // e.g., 'paystack', 'kudisms'
            $table->string('event_type'); // e.g., 'charge.success', 'transfer.failed'
            $table->string('reference')->nullable(); // Transaction reference from webhook
            $table->json('payload'); // Full webhook payload
            $table->json('headers')->nullable(); // Request headers
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->text('processing_result')->nullable();
            $table->string('signature')->nullable(); // Webhook signature for verification
            $table->boolean('verified')->default(false);
            $table->timestamps();
            
            $table->index(['source', 'event_type']);
            $table->index(['reference']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};