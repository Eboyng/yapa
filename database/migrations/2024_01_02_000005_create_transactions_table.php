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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reference')->unique(); // For tracking and idempotency
            $table->enum('type', ['credit', 'debit', 'refund', 'transfer', 'naira', 'earnings']);
            $table->enum('category', [
                'credit_purchase',
                'whatsapp_message',
                'sms_message',
                'refund',
                'earnings',
                'withdrawal',
                'bonus',
                'penalty',
                'whatsapp_change_fee',
                'batch_join',
                'ad_earning',
                'manual_adjustment'
            ]);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2)->nullable();
            $table->decimal('balance_after', 15, 2)->nullable();
            $table->text('description');
            $table->json('metadata')->nullable(); // Store additional data
            $table->enum('status', ['pending', 'processing', 'completed', 'confirmed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // paystack, bank_transfer, wallet, system
            $table->string('payment_reference')->nullable();
            $table->json('gateway_response')->nullable(); // Store gateway responses
            $table->integer('retry_count')->default(0);
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('related_id')->nullable(); // Links to batch_id/ad_id etc
            $table->string('source')->nullable(); // e.g., batch_join, ad_approval, paystack
            $table->unsignedBigInteger('parent_transaction_id')->nullable(); // For refunds/reversals
            $table->timestamp('retry_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'category']);
            $table->index(['wallet_id', 'status']);
            $table->index(['reference']);
            $table->index(['status', 'retry_until']);
            $table->index(['payment_reference']);
            $table->foreign('parent_transaction_id')->references('id')->on('transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};