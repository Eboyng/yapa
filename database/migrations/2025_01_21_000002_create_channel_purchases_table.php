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
        Schema::create('channel_purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('channel_sale_id')->constrained('channel_sales')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->foreignId('escrow_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->enum('status', ['pending', 'in_escrow', 'completed', 'failed', 'refunded'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['buyer_id']);
            $table->index(['channel_sale_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_purchases');
    }
};