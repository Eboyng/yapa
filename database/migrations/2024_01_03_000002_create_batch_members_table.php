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
        Schema::create('batch_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('user_id');
            $table->string('whatsapp_number');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['batch_id', 'user_id']);
            $table->index('whatsapp_number');
            $table->index('joined_at');
            $table->index('notified_at');
            
            // Unique constraints
            $table->unique(['batch_id', 'user_id'], 'batch_user_unique');
            $table->unique(['batch_id', 'whatsapp_number'], 'batch_whatsapp_unique');
            
            // Foreign keys
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_members');
    }
};