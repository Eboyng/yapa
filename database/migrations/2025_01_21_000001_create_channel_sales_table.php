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
        Schema::create('channel_sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('channel_name');
            $table->string('whatsapp_number');
            $table->string('category');
            $table->integer('audience_size');
            $table->decimal('engagement_rate', 5, 2)->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->json('screenshots')->nullable();
            $table->enum('status', ['listed', 'under_review', 'sold', 'removed'])->default('under_review');
            $table->boolean('visibility')->default(true);
            $table->timestamps();
            
            $table->index(['status', 'visibility']);
            $table->index(['category']);
            $table->index(['price']);
            $table->index(['audience_size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_sales');
    }
};