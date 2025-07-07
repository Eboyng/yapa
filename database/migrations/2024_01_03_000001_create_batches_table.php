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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('limit')->default(100);
            $table->string('location')->nullable();
            $table->enum('status', ['open', 'closed', 'full', 'expired'])->default('open');
            $table->enum('type', ['trial', 'regular'])->default('regular');
            $table->integer('cost_in_credits')->default(0);
            $table->string('download_vcf_path')->nullable();
            $table->boolean('created_by_admin')->default(false);
            $table->timestamp('auto_close_at')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('admin_user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'type']);
            $table->index('location');
            $table->index('auto_close_at');
            $table->index('created_by_admin');
            
            // Foreign keys
            $table->foreign('admin_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};