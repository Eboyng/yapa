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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('method', ['bank_transfer', 'paystack', 'mobile_money'])->default('bank_transfer');
            $table->json('account_details')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('reference')->unique();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index('created_at');
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};