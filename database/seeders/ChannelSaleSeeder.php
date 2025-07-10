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
        $users = User::factory()->count(5)->create();
        
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
        
        // Create additional random channel sales
        ChannelSale::factory()->count(15)->create();
        
        $this->command->info('Channel sales seeded successfully!');
    }
}