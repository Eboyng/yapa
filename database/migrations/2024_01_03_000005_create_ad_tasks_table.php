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
        Schema::create('ad_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ad_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['active', 'screenshot_uploaded', 'pending_review', 'approved', 'rejected', 'completed', 'expired'])->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('screenshot_uploaded_at')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->integer('view_count')->nullable();
            $table->decimal('earnings_amount', 8, 2)->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by_admin_id')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('appeal_message')->nullable();
            $table->timestamp('appeal_submitted_at')->nullable();
            $table->timestamp('appeal_reviewed_at')->nullable();
            $table->unsignedBigInteger('appeal_reviewed_by_admin_id')->nullable();
            $table->enum('appeal_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['ad_id', 'user_id']);
            $table->index('status');
            $table->index('started_at');
            $table->index('screenshot_uploaded_at');
            $table->index('reviewed_at');
            $table->index('appeal_status');
            
            // Unique constraint - one task per user per ad
            $table->unique(['ad_id', 'user_id'], 'ad_user_task_unique');
            
            // Foreign keys
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by_admin_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('appeal_reviewed_by_admin_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_tasks');
    }
};