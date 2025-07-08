<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed interests first
        $this->call([
            InterestSeeder::class,
            RolePermissionSeeder::class,
            BatchSeeder::class,
            ChannelSeeder::class,
            AdSeeder::class,
        ]);

        // Create test user with enhanced fields if not exists
        if (!User::where('email', 'admin@yapa.ng')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@yapa.ng',
                'password' => bcrypt('password'),
                'whatsapp_number' => '+2348012345678',
                'credits_balance' => 100, // Free credits upon registration
                'naira_balance' => 0,
                'earnings_balance' => 0,
                'location' => 'Lagos, Nigeria',
                'email_verification_enabled' => true,
                'whatsapp_verified_at' => now(),
                'email_verified_at' => now(),
            ]);
        }

        // Create additional test users if needed
        // Additional users can be created manually without faker
    }
}
