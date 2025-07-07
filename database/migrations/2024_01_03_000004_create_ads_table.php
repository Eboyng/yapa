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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('url')->nullable();
            $table->string('banner')->nullable();
            $table->enum('status', ['draft', 'active', 'paused', 'completed', 'expired'])->default('draft');
            $table->decimal('earnings_per_view', 8, 2)->default(0.30);
            $table->integer('max_participants')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->unsignedBigInteger('created_by_admin_id');
            $table->text('instructions')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index(['start_date', 'end_date']);
            $table->index('created_by_admin_id');
            $table->index('earnings_per_view');
            
            // Foreign keys
            $table->foreign('created_by_admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};