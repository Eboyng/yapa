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
        Schema::create('channel_ad_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->onDelete('cascade');
            $table->foreignId('channel_ad_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected', 'running', 'completed', 'disputed'])->default('pending');
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('proof_screenshot')->nullable();
            $table->timestamp('proof_submitted_at')->nullable();
            $table->timestamp('proof_approved_at')->nullable();
            $table->decimal('escrow_amount', 8, 2)->nullable();
            $table->enum('escrow_status', ['pending', 'held', 'released', 'refunded'])->default('pending');
            $table->timestamp('escrow_released_at')->nullable();
            $table->text('dispute_reason')->nullable();
            $table->enum('dispute_status', ['none', 'pending', 'resolved'])->default('none');
            $table->timestamp('dispute_resolved_at')->nullable();
            $table->text('dispute_resolution')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('escrow_status');
            $table->index('dispute_status');
            $table->index(['channel_id', 'channel_ad_id']);
            $table->index('applied_at');
            $table->index('proof_submitted_at');
            
            // Unique constraint to prevent duplicate applications
            $table->unique(['channel_id', 'channel_ad_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_ad_applications');
    }
};