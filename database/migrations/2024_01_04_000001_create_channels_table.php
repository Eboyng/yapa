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
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('niche');
            $table->integer('follower_count');
            $table->string('sample_screenshot')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('featured_priority')->default(0);
            $table->string('whatsapp_link')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('niche');
            $table->index('is_featured');
            $table->index(['is_featured', 'featured_priority']);
            $table->index('follower_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};