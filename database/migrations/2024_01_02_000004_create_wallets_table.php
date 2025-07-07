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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['credits', 'naira', 'earnings']);
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('version')->default(1); // For optimistic locking
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['user_id', 'type']);
            $table->index(['user_id', 'type']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};