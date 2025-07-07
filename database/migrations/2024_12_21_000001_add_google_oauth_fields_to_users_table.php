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
            // Google OAuth fields
            $table->text('google_access_token')->nullable()->after('google_people_cached_at');
            $table->text('google_refresh_token')->nullable()->after('google_access_token');
            
            // Index for performance
            $table->index('google_access_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['google_access_token']);
            $table->dropColumn([
                'google_access_token',
                'google_refresh_token',
            ]);
        });
    }
};