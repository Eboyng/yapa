<?php

namespace Database\Seeders;

use App\Models\ChannelSale;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChannelSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some users if they don't exist
        $users = [];
        
        // Create 5 sample users manually
        for ($i = 1; $i <= 5; $i++) {
            $users[] = User::create([
                'name' => "Channel Owner {$i}",
                'email' => "channelowner{$i}@example.com",
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'whatsapp_number' => '+234801234567' . $i,
                'location' => 'Lagos, Nigeria',
                'referral_code' => 'CHAN' . str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }
        
        // Create sample channel sales with different statuses and categories
        $channelSales = [
            [
                'user_id' => $users[0]->id,
                'channel_name' => 'Tech News Daily',
                'whatsapp_number' => '+2348012345678',
                'category' => 'technology',
                'audience_size' => 15000,
                'engagement_rate' => 18.5,
                'description' => 'Daily technology news and updates. High engagement with tech enthusiasts and professionals.',
                'price' => 150000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[1]->id,
                'channel_name' => 'Comedy Central NG',
                'whatsapp_number' => '+2348023456789',
                'category' => 'entertainment',
                'audience_size' => 25000,
                'engagement_rate' => 22.3,
                'description' => 'Nigerian comedy skits and funny videos. Very active community with daily interactions.',
                'price' => 200000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[2]->id,
                'channel_name' => 'Business Insights',
                'whatsapp_number' => '+2348034567890',
                'category' => 'business',
                'audience_size' => 8000,
                'engagement_rate' => 15.7,
                'description' => 'Business tips, entrepreneurship advice, and market insights for Nigerian entrepreneurs.',
                'price' => 80000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[3]->id,
                'channel_name' => 'Fitness Motivation',
                'whatsapp_number' => '+2348045678901',
                'category' => 'health',
                'audience_size' => 12000,
                'engagement_rate' => 20.1,
                'description' => 'Daily fitness tips, workout routines, and healthy lifestyle content.',
                'price' => 95000.00,
                'status' => ChannelSale::STATUS_UNDER_REVIEW,
                'visibility' => false,
            ],
            [
                'user_id' => $users[4]->id,
                'channel_name' => 'Crypto Updates NG',
                'whatsapp_number' => '+2348056789012',
                'category' => 'finance',
                'audience_size' => 18000,
                'engagement_rate' => 16.8,
                'description' => 'Cryptocurrency news, trading tips, and blockchain technology updates.',
                'price' => 180000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[0]->id,
                'channel_name' => 'Recipe Corner',
                'whatsapp_number' => '+2348067890123',
                'category' => 'lifestyle',
                'audience_size' => 6000,
                'engagement_rate' => 14.2,
                'description' => 'Nigerian and international recipes, cooking tips, and food photography.',
                'price' => 45000.00,
                'status' => ChannelSale::STATUS_SOLD,
                'visibility' => false,
            ],
            [
                'user_id' => $users[1]->id,
                'channel_name' => 'Study Hub Nigeria',
                'whatsapp_number' => '+2348078901234',
                'category' => 'education',
                'audience_size' => 22000,
                'engagement_rate' => 19.5,
                'description' => 'Educational content, exam tips, scholarship opportunities, and academic resources.',
                'price' => 165000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[2]->id,
                'channel_name' => 'Fashion Trends',
                'whatsapp_number' => '+2348089012345',
                'category' => 'lifestyle',
                'audience_size' => 14000,
                'engagement_rate' => 21.7,
                'description' => 'Latest fashion trends, style tips, and outfit inspirations for young Nigerians.',
                'price' => 120000.00,
                'status' => ChannelSale::STATUS_REMOVED,
                'visibility' => false,
            ],
        ];
        
        foreach ($channelSales as $channelData) {
            ChannelSale::create($channelData);
        }
        
        // Create additional channel sales manually
        $additionalChannels = [
            [
                'user_id' => $users[0]->id,
                'channel_name' => 'Sports Central',
                'whatsapp_number' => '+2348090123456',
                'category' => 'sports',
                'audience_size' => 11000,
                'engagement_rate' => 17.3,
                'description' => 'Football news, match updates, and sports analysis.',
                'price' => 85000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[1]->id,
                'channel_name' => 'Music Vibes',
                'whatsapp_number' => '+2348091234567',
                'category' => 'entertainment',
                'audience_size' => 19000,
                'engagement_rate' => 23.1,
                'description' => 'Latest music releases, artist interviews, and music reviews.',
                'price' => 140000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[2]->id,
                'channel_name' => 'Travel Nigeria',
                'whatsapp_number' => '+2348092345678',
                'category' => 'lifestyle',
                'audience_size' => 7500,
                'engagement_rate' => 16.2,
                'description' => 'Travel destinations, tourism tips, and cultural experiences in Nigeria.',
                'price' => 60000.00,
                'status' => ChannelSale::STATUS_UNDER_REVIEW,
                'visibility' => false,
            ],
            [
                'user_id' => $users[3]->id,
                'channel_name' => 'Real Estate Hub',
                'whatsapp_number' => '+2348093456789',
                'category' => 'business',
                'audience_size' => 13500,
                'engagement_rate' => 14.8,
                'description' => 'Property listings, real estate investment tips, and market updates.',
                'price' => 110000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[4]->id,
                'channel_name' => 'Auto World',
                'whatsapp_number' => '+2348094567890',
                'category' => 'technology',
                'audience_size' => 9800,
                'engagement_rate' => 15.5,
                'description' => 'Car reviews, automotive news, and vehicle maintenance tips.',
                'price' => 75000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[0]->id,
                'channel_name' => 'Parenting Tips',
                'whatsapp_number' => '+2348095678901',
                'category' => 'lifestyle',
                'audience_size' => 8200,
                'engagement_rate' => 18.9,
                'description' => 'Parenting advice, child development tips, and family activities.',
                'price' => 65000.00,
                'status' => ChannelSale::STATUS_SOLD,
                'visibility' => false,
            ],
            [
                'user_id' => $users[1]->id,
                'channel_name' => 'Photography Pro',
                'whatsapp_number' => '+2348096789012',
                'category' => 'technology',
                'audience_size' => 5500,
                'engagement_rate' => 20.4,
                'description' => 'Photography tutorials, camera reviews, and photo editing tips.',
                'price' => 50000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[2]->id,
                'channel_name' => 'Gaming Zone',
                'whatsapp_number' => '+2348097890123',
                'category' => 'entertainment',
                'audience_size' => 16500,
                'engagement_rate' => 25.7,
                'description' => 'Gaming news, reviews, tutorials, and esports updates.',
                'price' => 130000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[3]->id,
                'channel_name' => 'Beauty Secrets',
                'whatsapp_number' => '+2348098901234',
                'category' => 'lifestyle',
                'audience_size' => 12800,
                'engagement_rate' => 22.6,
                'description' => 'Beauty tips, skincare routines, makeup tutorials, and product reviews.',
                'price' => 105000.00,
                'status' => ChannelSale::STATUS_UNDER_REVIEW,
                'visibility' => false,
            ],
            [
                'user_id' => $users[4]->id,
                'channel_name' => 'News Update',
                'whatsapp_number' => '+2348099012345',
                'category' => 'news',
                'audience_size' => 21000,
                'engagement_rate' => 19.2,
                'description' => 'Breaking news, current affairs, and political updates.',
                'price' => 155000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[0]->id,
                'channel_name' => 'DIY Crafts',
                'whatsapp_number' => '+2348090012345',
                'category' => 'lifestyle',
                'audience_size' => 6800,
                'engagement_rate' => 17.8,
                'description' => 'Do-it-yourself crafts, home decoration ideas, and creative projects.',
                'price' => 55000.00,
                'status' => ChannelSale::STATUS_REMOVED,
                'visibility' => false,
            ],
            [
                'user_id' => $users[1]->id,
                'channel_name' => 'Investment Guide',
                'whatsapp_number' => '+2348091012345',
                'category' => 'finance',
                'audience_size' => 14200,
                'engagement_rate' => 16.1,
                'description' => 'Investment strategies, financial planning, and wealth building tips.',
                'price' => 115000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[2]->id,
                'channel_name' => 'Pet Care',
                'whatsapp_number' => '+2348092012345',
                'category' => 'lifestyle',
                'audience_size' => 4500,
                'engagement_rate' => 21.3,
                'description' => 'Pet care tips, veterinary advice, and animal welfare information.',
                'price' => 40000.00,
                'status' => ChannelSale::STATUS_SOLD,
                'visibility' => false,
            ],
            [
                'user_id' => $users[3]->id,
                'channel_name' => 'Language Learning',
                'whatsapp_number' => '+2348093012345',
                'category' => 'education',
                'audience_size' => 9200,
                'engagement_rate' => 18.4,
                'description' => 'English language tutorials, grammar tips, and vocabulary building.',
                'price' => 70000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
            [
                'user_id' => $users[4]->id,
                'channel_name' => 'Motivational Quotes',
                'whatsapp_number' => '+2348094012345',
                'category' => 'lifestyle',
                'audience_size' => 17500,
                'engagement_rate' => 24.1,
                'description' => 'Daily motivational quotes, inspirational stories, and personal development content.',
                'price' => 125000.00,
                'status' => ChannelSale::STATUS_LISTED,
                'visibility' => true,
            ],
        ];
        
        foreach ($additionalChannels as $channelData) {
            ChannelSale::create($channelData);
        }
        
        $this->command->info('Channel sales seeded successfully!');
    }
}