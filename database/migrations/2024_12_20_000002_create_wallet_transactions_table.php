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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('wallet_type', ['credits', 'naira', 'earnings']);
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->enum('category', ['admin_funding', 'admin_deduction', 'task_payment', 'withdrawal', 'refund']);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('description')->nullable();
            $table->string('reference')->unique();
            $table->json('metadata')->nullable();
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'wallet_type']);
            $table->index(['admin_user_id']);
            $table->index(['type']);
            $table->index(['category']);
            $table->index(['status']);
            $table->index(['reference']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};