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
        Schema::table('users', function (Blueprint $table) {
            // Ad campaign fields
            $table->integer('ad_rejection_count')->default(0)->after('earnings_balance');
            $table->boolean('is_flagged_for_ads')->default(false)->after('ad_rejection_count');
            $table->timestamp('flagged_at')->nullable()->after('is_flagged_for_ads');
            $table->text('appeal_message')->nullable()->after('flagged_at');
            $table->timestamp('appeal_submitted_at')->nullable()->after('appeal_message');
            
            // Google People API cache fields
            $table->json('google_people_cache')->nullable()->after('appeal_submitted_at');
            $table->timestamp('google_people_cached_at')->nullable()->after('google_people_cache');
            
            // Indexes
            $table->index('is_flagged_for_ads');
            $table->index('flagged_at');
            $table->index('appeal_submitted_at');
            $table->index('google_people_cached_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_flagged_for_ads']);
            $table->dropIndex(['flagged_at']);
            $table->dropIndex(['appeal_submitted_at']);
            $table->dropIndex(['google_people_cached_at']);
            
            $table->dropColumn([
                'ad_rejection_count',
                'is_flagged_for_ads',
                'flagged_at',
                'appeal_message',
                'appeal_submitted_at',
                'google_people_cache',
                'google_people_cached_at',
            ]);
        });
    }
};