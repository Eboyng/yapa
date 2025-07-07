<?php

namespace Database\Seeders;

use App\Models\Interest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interests = [
            [
                'name' => 'Technology',
                'description' => 'Software development, gadgets, AI, and tech innovations',
            ],
            [
                'name' => 'Business',
                'description' => 'Entrepreneurship, startups, marketing, and business strategies',
            ],
            [
                'name' => 'Education',
                'description' => 'Learning, courses, academic content, and educational resources',
            ],
            [
                'name' => 'Health & Fitness',
                'description' => 'Wellness, exercise, nutrition, and healthy lifestyle',
            ],
            [
                'name' => 'Finance',
                'description' => 'Investment, cryptocurrency, banking, and financial planning',
            ],
            [
                'name' => 'Entertainment',
                'description' => 'Movies, music, games, and entertainment content',
            ],
            [
                'name' => 'Sports',
                'description' => 'Football, basketball, athletics, and sports news',
            ],
            [
                'name' => 'Fashion',
                'description' => 'Style, clothing, beauty, and fashion trends',
            ],
            [
                'name' => 'Food & Cooking',
                'description' => 'Recipes, restaurants, culinary arts, and food culture',
            ],
            [
                'name' => 'Travel',
                'description' => 'Tourism, destinations, travel tips, and adventure',
            ],
            [
                'name' => 'Real Estate',
                'description' => 'Property investment, housing market, and real estate tips',
            ],
            [
                'name' => 'Automotive',
                'description' => 'Cars, motorcycles, automotive news, and vehicle reviews',
            ],
            [
                'name' => 'Politics',
                'description' => 'Government, elections, political news, and civic engagement',
            ],
            [
                'name' => 'Religion & Spirituality',
                'description' => 'Faith, spiritual growth, religious content, and inspiration',
            ],
            [
                'name' => 'Art & Design',
                'description' => 'Creative arts, graphic design, photography, and visual content',
            ],
            [
                'name' => 'Science',
                'description' => 'Research, discoveries, scientific news, and innovation',
            ],
            [
                'name' => 'Parenting',
                'description' => 'Child care, family life, parenting tips, and child development',
            ],
            [
                'name' => 'Relationships',
                'description' => 'Dating, marriage, friendship, and relationship advice',
            ],
            [
                'name' => 'Personal Development',
                'description' => 'Self-improvement, motivation, productivity, and life skills',
            ],
            [
                'name' => 'News & Current Affairs',
                'description' => 'Breaking news, current events, and global affairs',
            ],
            [
                'name' => 'Agriculture',
                'description' => 'Farming, livestock, agricultural technology, and rural development',
            ],
            [
                'name' => 'Environment',
                'description' => 'Climate change, sustainability, conservation, and green living',
            ],
            [
                'name' => 'Photography',
                'description' => 'Photo techniques, equipment, editing, and visual storytelling',
            ],
            [
                'name' => 'Music',
                'description' => 'Artists, albums, concerts, and music industry news',
            ],
            [
                'name' => 'Books & Literature',
                'description' => 'Reading, authors, book reviews, and literary discussions',
            ],
            [
                'name' => 'Gaming',
                'description' => 'Video games, mobile games, gaming news, and esports',
            ],
            [
                'name' => 'Cryptocurrency',
                'description' => 'Bitcoin, blockchain, DeFi, and digital currency trading',
            ],
            [
                'name' => 'Social Media',
                'description' => 'Platform updates, social media marketing, and online trends',
            ],
            [
                'name' => 'Comedy & Humor',
                'description' => 'Jokes, memes, funny content, and entertainment',
            ],
            [
                'name' => 'DIY & Crafts',
                'description' => 'Do-it-yourself projects, crafting, and creative tutorials',
            ],
        ];

        foreach ($interests as $interestData) {
            Interest::firstOrCreate(
                ['slug' => Str::slug($interestData['name'])],
                [
                    'name' => $interestData['name'],
                    'description' => $interestData['description'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Interests seeded successfully!');
    }
}