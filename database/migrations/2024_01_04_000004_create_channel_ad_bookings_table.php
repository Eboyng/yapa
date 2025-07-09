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
        Schema::create('channel_ad_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('channel_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->json('images')->nullable();
            $table->integer('duration_hours');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'accepted', 'rejected', 'running', 'proof_submitted', 'completed', 'cancelled', 'disputed'])->default('pending');
            $table->enum('payment_method', ['wallet', 'paystack']);
            $table->string('payment_reference')->nullable();
            $table->foreignId('escrow_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->enum('escrow_status', ['pending', 'held', 'released', 'refunded'])->default('pending');
            $table->timestamp('booked_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('proof_screenshot')->nullable();
            $table->timestamp('proof_submitted_at')->nullable();
            $table->timestamp('proof_approved_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('auto_cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'booked_at']);
            $table->index(['channel_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['escrow_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_ad_bookings');
    }
};