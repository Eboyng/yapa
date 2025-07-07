<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users for channels if they don't exist
        $users = [];
        for ($i = 1; $i <= 20; $i++) {
            $email = "channel_owner_{$i}@yapa.ng";
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Channel Owner {$i}",
                    'whatsapp_number' => '+234' . str_pad(rand(8000000000, 8999999999), 10, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password'),
                    'credits_balance' => rand(50, 200),
                    'naira_balance' => rand(1000, 10000),
                    'earnings_balance' => rand(500, 5000),
                    'location' => collect(['Lagos', 'Abuja', 'Port Harcourt', 'Kano', 'Ibadan', 'Benin City'])->random() . ', Nigeria',
                    'email_verification_enabled' => true,
                    'whatsapp_verified_at' => now(),
                    'email_verified_at' => now(),
                ]
            );
            $users[] = $user;
        }

        // Sample channel names and descriptions
        $channelData = [
            [
                'name' => 'Tech Nigeria Updates',
                'niche' => 'technology',
                'description' => 'Latest technology news and updates from Nigeria and around the world.',
                'follower_count' => rand(5000, 50000),
            ],
            [
                'name' => 'Business Insider NG',
                'niche' => 'business',
                'description' => 'Business news, entrepreneurship tips, and market insights for Nigerian entrepreneurs.',
                'follower_count' => rand(10000, 80000),
            ],
            [
                'name' => 'Naija Entertainment Hub',
                'niche' => 'entertainment',
                'description' => 'Your go-to source for Nigerian entertainment news, celebrity gossip, and Nollywood updates.',
                'follower_count' => rand(15000, 100000),
            ],
            [
                'name' => 'Sports Arena Nigeria',
                'niche' => 'sports',
                'description' => 'Comprehensive coverage of Nigerian and international sports news.',
                'follower_count' => rand(8000, 60000),
            ],
            [
                'name' => 'Daily News Nigeria',
                'niche' => 'news',
                'description' => 'Breaking news and current affairs from Nigeria and beyond.',
                'follower_count' => rand(20000, 120000),
            ],
            [
                'name' => 'EduHub Nigeria',
                'niche' => 'education',
                'description' => 'Educational resources, scholarship opportunities, and academic news for Nigerian students.',
                'follower_count' => rand(12000, 70000),
            ],
            [
                'name' => 'Lagos Lifestyle',
                'niche' => 'lifestyle',
                'description' => 'Lifestyle tips, fashion trends, and social events in Lagos and Nigeria.',
                'follower_count' => rand(18000, 90000),
            ],
            [
                'name' => 'Health Plus Nigeria',
                'niche' => 'health',
                'description' => 'Health tips, medical news, and wellness advice for Nigerians.',
                'follower_count' => rand(7000, 45000),
            ],
            [
                'name' => 'Naira Finance Tips',
                'niche' => 'finance',
                'description' => 'Financial literacy, investment tips, and money management advice.',
                'follower_count' => rand(9000, 55000),
            ],
            [
                'name' => 'Travel Nigeria',
                'niche' => 'travel',
                'description' => 'Discover beautiful destinations in Nigeria and travel tips for Nigerians.',
                'follower_count' => rand(6000, 40000),
            ],
            [
                'name' => 'Naija Food Lovers',
                'niche' => 'food',
                'description' => 'Nigerian recipes, food reviews, and culinary adventures.',
                'follower_count' => rand(11000, 65000),
            ],
            [
                'name' => 'Fashion Forward NG',
                'niche' => 'fashion',
                'description' => 'Latest fashion trends, style tips, and Nigerian fashion designers spotlight.',
                'follower_count' => rand(13000, 75000),
            ],
            [
                'name' => 'Afrobeats Central',
                'niche' => 'music',
                'description' => 'Latest Afrobeats, Nigerian music news, and artist interviews.',
                'follower_count' => rand(16000, 85000),
            ],
            [
                'name' => 'Gaming Nigeria',
                'niche' => 'gaming',
                'description' => 'Gaming news, reviews, and the Nigerian gaming community.',
                'follower_count' => rand(5000, 35000),
            ],
            [
                'name' => 'Startup Nigeria',
                'niche' => 'business',
                'description' => 'Nigerian startup ecosystem, funding news, and entrepreneur stories.',
                'follower_count' => rand(8000, 50000),
            ],
            [
                'name' => 'Crypto Nigeria',
                'niche' => 'finance',
                'description' => 'Cryptocurrency news, blockchain technology, and digital finance in Nigeria.',
                'follower_count' => rand(7000, 45000),
            ],
            [
                'name' => 'Nollywood Insider',
                'niche' => 'entertainment',
                'description' => 'Behind-the-scenes Nollywood content, movie reviews, and actor interviews.',
                'follower_count' => rand(14000, 80000),
            ],
            [
                'name' => 'Nigerian Politics Today',
                'niche' => 'news',
                'description' => 'Political analysis, election updates, and governance news in Nigeria.',
                'follower_count' => rand(12000, 70000),
            ],
            [
                'name' => 'Fitness Nigeria',
                'niche' => 'health',
                'description' => 'Fitness tips, workout routines, and healthy living for Nigerians.',
                'follower_count' => rand(6000, 40000),
            ],
            [
                'name' => 'Nigerian Culture Hub',
                'niche' => 'other',
                'description' => 'Celebrating Nigerian culture, traditions, and heritage.',
                'follower_count' => rand(9000, 55000),
            ],
        ];

        // Create channels
        foreach ($channelData as $index => $data) {
            $user = $users[$index];
            
            Channel::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'niche' => $data['niche'],
                'follower_count' => $data['follower_count'],
                'description' => $data['description'],
                'whatsapp_link' => 'https://wa.me/' . str_replace('+', '', $user->whatsapp_number),
                'sample_screenshot' => 'channels/sample_' . ($index + 1) . '.jpg',
                'status' => collect([Channel::STATUS_APPROVED, Channel::STATUS_PENDING])->random(),
                'is_featured' => rand(1, 5) === 1, // 20% chance of being featured
                'featured_priority' => rand(1, 10),
                'approved_by' => rand(1, 3) === 1 ? 1 : null, // Some approved by admin user
                'approved_at' => rand(1, 2) === 1 ? now()->subDays(rand(1, 30)) : null,
            ]);
        }
    }
}