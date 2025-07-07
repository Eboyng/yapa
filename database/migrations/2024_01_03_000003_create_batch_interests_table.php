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
        Schema::create('batch_interests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('interest_id');
            $table->timestamps();

            // Indexes
            $table->index(['batch_id', 'interest_id']);
            $table->index('interest_id');
            
            // Unique constraint
            $table->unique(['batch_id', 'interest_id'], 'batch_interest_unique');
            
            // Foreign keys
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('cascade');
            $table->foreign('interest_id')->references('id')->on('interests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_interests');
    }
};