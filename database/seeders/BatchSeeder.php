<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Interest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all interests for random assignment
        $interests = Interest::all();
        
        // Get admin user for some batches
        $adminUser = User::where('email', 'admin@yapa.ng')->first();
        
        // Nigerian locations for realistic data
        $locations = [
            'Lagos, Nigeria',
            'Abuja, Nigeria',
            'Port Harcourt, Nigeria',
            'Kano, Nigeria',
            'Ibadan, Nigeria',
            'Benin City, Nigeria',
            'Kaduna, Nigeria',
            'Jos, Nigeria',
            'Warri, Nigeria',
            'Enugu, Nigeria',
            'Calabar, Nigeria',
            'Ilorin, Nigeria',
            'Aba, Nigeria',
            'Onitsha, Nigeria',
            'Akure, Nigeria'
        ];
        
        $batchData = [
            // Trial batches (5)
            [
                'name' => 'Lagos Tech Enthusiasts - Trial',
                'type' => Batch::TYPE_TRIAL,
                'limit' => 30,
                'location' => 'Lagos, Nigeria',
                'cost_in_credits' => 0,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Connect with tech professionals in Lagos',
                'created_by_admin' => true,
                'admin_user_id' => $adminUser?->id,
                'auto_close_at' => now()->addDays(7),
                'interest_names' => ['Technology', 'Business & Entrepreneurship']
            ],
            [
                'name' => 'Abuja Business Network - Trial',
                'type' => Batch::TYPE_TRIAL,
                'limit' => 30,
                'location' => 'Abuja, Nigeria',
                'cost_in_credits' => 0,
                'status' => Batch::STATUS_FULL,
                'description' => 'Business networking in the capital',
                'created_by_admin' => false,
                'auto_close_at' => now()->addDays(5),
                'interest_names' => ['Business & Entrepreneurship', 'Finance & Investment']
            ],
            [
                'name' => 'Port Harcourt Oil & Gas - Trial',
                'type' => Batch::TYPE_TRIAL,
                'limit' => 30,
                'location' => 'Port Harcourt, Nigeria',
                'cost_in_credits' => 0,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Oil and gas professionals networking',
                'created_by_admin' => true,
                'admin_user_id' => $adminUser?->id,
                'auto_close_at' => now()->addDays(6),
                'interest_names' => ['Business & Entrepreneurship', 'Technology']
            ],
            [
                'name' => 'Kano Agriculture Hub - Trial',
                'type' => Batch::TYPE_TRIAL,
                'limit' => 30,
                'location' => 'Kano, Nigeria',
                'cost_in_credits' => 0,
                'status' => Batch::STATUS_CLOSED,
                'description' => 'Agricultural business networking',
                'created_by_admin' => false,
                'auto_close_at' => now()->subDays(1),
                'interest_names' => ['Business & Entrepreneurship', 'Health & Wellness']
            ],
            [
                'name' => 'Ibadan University Connect - Trial',
                'type' => Batch::TYPE_TRIAL,
                'limit' => 30,
                'location' => 'Ibadan, Nigeria',
                'cost_in_credits' => 0,
                'status' => Batch::STATUS_OPEN,
                'description' => 'University students and alumni network',
                'created_by_admin' => true,
                'admin_user_id' => $adminUser?->id,
                'auto_close_at' => now()->addDays(8),
                'interest_names' => ['Education & Learning', 'Technology']
            ],
            
            // Regular batches (15)
            [
                'name' => 'Lagos Fintech Professionals',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 100,
                'location' => 'Lagos, Nigeria',
                'cost_in_credits' => 50,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Financial technology professionals and enthusiasts',
                'created_by_admin' => true,
                'admin_user_id' => $adminUser?->id,
                'auto_close_at' => now()->addDays(10),
                'interest_names' => ['Technology', 'Finance & Investment', 'Business & Entrepreneurship']
            ],
            [
                'name' => 'Abuja Real Estate Network',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 80,
                'location' => 'Abuja, Nigeria',
                'cost_in_credits' => 40,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Real estate investors and professionals',
                'created_by_admin' => false,
                'auto_close_at' => now()->addDays(12),
                'interest_names' => ['Finance & Investment', 'Business & Entrepreneurship']
            ],
            [
                'name' => 'Nigerian Fashion Designers',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 75,
                'location' => 'Lagos, Nigeria',
                'cost_in_credits' => 35,
                'status' => Batch::STATUS_FULL,
                'description' => 'Fashion designers and style enthusiasts',
                'created_by_admin' => false,
                'auto_close_at' => now()->addDays(8),
                'interest_names' => ['Fashion & Style', 'Business & Entrepreneurship', 'Art & Creativity']
            ],
            [
                'name' => 'Health & Wellness Practitioners',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 90,
                'location' => 'Abuja, Nigeria',
                'cost_in_credits' => 45,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Healthcare professionals and wellness coaches',
                'created_by_admin' => true,
                'admin_user_id' => $adminUser?->id,
                'auto_close_at' => now()->addDays(14),
                'interest_names' => ['Health & Wellness', 'Fitness & Sports']
            ],
            [
                'name' => 'Crypto Traders Nigeria',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 120,
                'location' => 'Lagos, Nigeria',
                'cost_in_credits' => 60,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Cryptocurrency traders and blockchain enthusiasts',
                'created_by_admin' => false,
                'auto_close_at' => now()->addDays(9),
                'interest_names' => ['Cryptocurrency', 'Finance & Investment', 'Technology']
            ],
            [
                'name' => 'Nigerian Food Entrepreneurs',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 85,
                'location' => 'Port Harcourt, Nigeria',
                'cost_in_credits' => 40,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Food business owners and culinary professionals',
                'created_by_admin' => true,
                'admin_user_id' => $adminUser?->id,
                'auto_close_at' => now()->addDays(11),
                'interest_names' => ['Food & Cooking', 'Business & Entrepreneurship']
            ],
            [
                'name' => 'Digital Marketing Experts',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 95,
                'location' => 'Lagos, Nigeria',
                'cost_in_credits' => 50,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Digital marketers and social media specialists',
                'created_by_admin' => false,
                'auto_close_at' => now()->addDays(13),
                'interest_names' => ['Social Media', 'Business & Entrepreneurship', 'Technology']
            ],
            [
                'name' => 'Nigerian Musicians & Artists',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 70,
                'location' => 'Lagos, Nigeria',
                'cost_in_credits' => 35,
                'status' => Batch::STATUS_EXPIRED,
                'description' => 'Musicians, artists, and creative professionals',
                'created_by_admin' => false,
                'auto_close_at' => now()->subDays(2),
                'interest_names' => ['Music', 'Art & Creativity', 'Entertainment']
            ],
            [
                'name' => 'Travel & Tourism Nigeria',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 80,
                'location' => 'Abuja, Nigeria',
                'cost_in_credits' => 40,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Travel enthusiasts and tourism professionals',
                'created_by_admin' => true,
                'admin_user_id' => $adminUser?->id,
                'auto_close_at' => now()->addDays(15),
                'interest_names' => ['Travel & Tourism', 'Photography']
            ],
            [
                'name' => 'Nigerian Gamers Community',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 100,
                'location' => 'Lagos, Nigeria',
                'cost_in_credits' => 30,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Gaming enthusiasts and esports players',
                'created_by_admin' => false,
                'auto_close_at' => now()->addDays(7),
                'interest_names' => ['Gaming', 'Technology', 'Entertainment']
            ],
            [
                'name' => 'Fitness Trainers Network',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 75,
                'location' => 'Abuja, Nigeria',
                'cost_in_credits' => 35,
                'status' => Batch::STATUS_FULL,
                'description' => 'Personal trainers and fitness professionals',
                'created_by_admin' => true,
                'admin_user_id' => $adminUser?->id,
                'auto_close_at' => now()->addDays(6),
                'interest_names' => ['Fitness & Sports', 'Health & Wellness']
            ],
            [
                'name' => 'Nigerian Parents Network',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 90,
                'location' => 'Lagos, Nigeria',
                'cost_in_credits' => 25,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Parents sharing experiences and advice',
                'created_by_admin' => false,
                'auto_close_at' => now()->addDays(16),
                'interest_names' => ['Parenting', 'Education & Learning']
            ],
            [
                'name' => 'Tech Startup Founders',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 60,
                'location' => 'Lagos, Nigeria',
                'cost_in_credits' => 70,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Startup founders and tech entrepreneurs',
                'created_by_admin' => true,
                'admin_user_id' => $adminUser?->id,
                'auto_close_at' => now()->addDays(20),
                'interest_names' => ['Technology', 'Business & Entrepreneurship', 'Finance & Investment']
            ],
            [
                'name' => 'Nigerian Comedy Network',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 85,
                'location' => 'Lagos, Nigeria',
                'cost_in_credits' => 30,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Comedians and entertainment professionals',
                'created_by_admin' => false,
                'auto_close_at' => now()->addDays(12),
                'interest_names' => ['Comedy & Humor', 'Entertainment']
            ],
            [
                'name' => 'Photography Enthusiasts',
                'type' => Batch::TYPE_REGULAR,
                'limit' => 70,
                'location' => 'Abuja, Nigeria',
                'cost_in_credits' => 40,
                'status' => Batch::STATUS_OPEN,
                'description' => 'Professional and amateur photographers',
                'created_by_admin' => true,
                'admin_user_id' => $adminUser?->id,
                'auto_close_at' => now()->addDays(18),
                'interest_names' => ['Photography', 'Art & Creativity']
            ]
        ];
        
        foreach ($batchData as $data) {
            // Extract interest names for later use
            $interestNames = $data['interest_names'];
            unset($data['interest_names']);
            
            // Create the batch
            $batch = Batch::create($data);
            
            // Attach interests
            $batchInterests = $interests->whereIn('name', $interestNames)->pluck('id');
            if ($batchInterests->isNotEmpty()) {
                $batch->interests()->attach($batchInterests);
            }
        }
        
        $this->command->info('Created 20 batches with realistic data!');
    }
}