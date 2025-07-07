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
        Schema::create('channel_ads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('content');
            $table->string('media_url')->nullable();
            $table->integer('duration_days');
            $table->decimal('budget', 10, 2);
            $table->decimal('payment_per_channel', 8, 2);
            $table->integer('max_channels')->nullable();
            $table->json('target_niches')->nullable();
            $table->integer('min_followers')->nullable();
            $table->enum('status', ['draft', 'active', 'paused', 'completed', 'expired', 'cancelled'])->default('draft');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->foreignId('created_by_admin_id')->constrained('users')->onDelete('cascade');
            $table->text('instructions')->nullable();
            $table->text('requirements')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('min_followers');
            $table->index('created_by_admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_ads');
    }
};