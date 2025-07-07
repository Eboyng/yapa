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
            // Admin and notification fields
            $table->boolean('is_admin')->default(false)->after('google_people_cached_at');
            $table->boolean('whatsapp_notifications_enabled')->default(true)->after('is_admin');
            $table->boolean('email_notifications_enabled')->default(true)->after('whatsapp_notifications_enabled');
            $table->string('avatar')->nullable()->after('email_notifications_enabled');
            
            // Indexes
            $table->index('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_admin']);
            
            $table->dropColumn([
                'is_admin',
                'whatsapp_notifications_enabled',
                'email_notifications_enabled',
                'avatar',
            ]);
        });
    }
};