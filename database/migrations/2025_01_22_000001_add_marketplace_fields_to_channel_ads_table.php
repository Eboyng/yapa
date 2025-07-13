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
        Schema::table('channel_ads', function (Blueprint $table) {
            // Add marketplace-specific fields
            $table->string('channel_name')->nullable()->after('title');
            $table->foreignId('owner_id')->nullable()->after('created_by_admin_id')->constrained('users')->onDelete('cascade');
            $table->decimal('price_per_ad', 10, 2)->nullable()->after('payment_per_channel');
            $table->integer('subscriber_count')->nullable()->after('min_followers');
            $table->string('niche')->nullable()->after('target_niches');
            $table->string('location')->nullable()->after('niche');
            
            // Add indexes for better performance
            $table->index('owner_id');
            $table->index('price_per_ad');
            $table->index('subscriber_count');
            $table->index('niche');
            $table->index('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('channel_ads', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropIndex(['owner_id']);
            $table->dropIndex(['price_per_ad']);
            $table->dropIndex(['subscriber_count']);
            $table->dropIndex(['niche']);
            $table->dropIndex(['location']);
            
            $table->dropColumn([
                'channel_name',
                'owner_id',
                'price_per_ad',
                'subscriber_count',
                'niche',
                'location'
            ]);
        });
    }
};