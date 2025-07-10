<?php

namespace Database\Seeders;

use App\Models\ChannelAd;
use App\Models\User;
use App\Models\Channel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ChannelAdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin users for channel ads if they don't exist
        $admins = [];
        for ($i = 1; $i <= 5; $i++) {
            $email = "admin_{$i}@yapa.ng";
            $admin = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Admin User {$i}",
                    'whatsapp_number' => '+234' . str_pad(rand(8000000000, 8999999999), 10, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password'),
                    // Wallets are automatically created with default balances via User::boot()
                    'location' => collect(['Lagos', 'Abuja', 'Port Harcourt', 'Kano', 'Ibadan'])->random() . ', Nigeria',
                    'email_verification_enabled' => true,
                    'whatsapp_verified_at' => now(),
                    'email_verified_at' => now(),
                ]
            );
            $admins[] = $admin;
        }

        // Sample channel ad data
        $channelAdsData = [
            [
                'title' => 'Promote Your Tech Startup',
                'description' => 'Looking for tech channels to promote our new startup accelerator program. Great opportunity for tech enthusiasts.',
                'content' => '<p>We are launching a new startup accelerator program and need tech channels to help spread the word. This is a great opportunity to earn while promoting innovation in Nigeria.</p><p>Requirements:</p><ul><li>Post our promotional content</li><li>Share with your audience</li><li>Provide engagement metrics</li></ul>',
                'duration_days' => 7,
                'budget' => 50000,
                'payment_per_channel' => 2500,
                'max_channels' => 20,
                'target_niches' => ['technology', 'business'],
                'min_followers' => 5000,
                'status' => 'active',
            ],
            [
                'title' => 'Fashion Brand Launch Campaign',
                'description' => 'New fashion brand launching in Nigeria. Looking for fashion and lifestyle channels for promotion.',
                'content' => '<p>Exciting new fashion brand launching with unique African-inspired designs. We need fashion influencers to showcase our products.</p><p>What we offer:</p><ul><li>High-quality promotional materials</li><li>Competitive compensation</li><li>Long-term partnership opportunities</li></ul>',
                'duration_days' => 14,
                'budget' => 75000,
                'payment_per_channel' => 3750,
                'max_channels' => 20,
                'target_niches' => ['fashion', 'lifestyle'],
                'min_followers' => 8000,
                'status' => 'active',
            ],
            [
                'title' => 'Cryptocurrency Education Campaign',
                'description' => 'Educational campaign about cryptocurrency and blockchain technology for Nigerian audience.',
                'content' => '<p>Help us educate Nigerians about cryptocurrency and blockchain technology. This campaign focuses on financial literacy and safe crypto practices.</p><p>Campaign includes:</p><ul><li>Educational infographics</li><li>Video content</li><li>Interactive Q&A sessions</li></ul>',
                'duration_days' => 10,
                'budget' => 60000,
                'payment_per_channel' => 4000,
                'max_channels' => 15,
                'target_niches' => ['finance', 'technology'],
                'min_followers' => 10000,
                'status' => 'active',
            ],
            [
                'title' => 'Health and Wellness Awareness',
                'description' => 'Promoting healthy lifestyle choices and wellness products for Nigerian families.',
                'content' => '<p>Join our health and wellness awareness campaign. Help promote healthy living and quality wellness products to Nigerian families.</p><p>Campaign features:</p><ul><li>Health tips and advice</li><li>Product demonstrations</li><li>Community engagement</li></ul>',
                'duration_days' => 21,
                'budget' => 90000,
                'payment_per_channel' => 3000,
                'max_channels' => 30,
                'target_niches' => ['health', 'lifestyle'],
                'min_followers' => 6000,
                'status' => 'active',
            ],
            [
                'title' => 'Nigerian Music Promotion',
                'description' => 'Promoting upcoming Nigerian artists and their latest releases across music channels.',
                'content' => '<p>Support the Nigerian music industry by promoting talented upcoming artists. Help showcase the best of Afrobeats and other Nigerian music genres.</p><p>Promotion includes:</p><ul><li>Artist spotlights</li><li>Music video premieres</li><li>Behind-the-scenes content</li></ul>',
                'duration_days' => 5,
                'budget' => 40000,
                'payment_per_channel' => 2000,
                'max_channels' => 20,
                'target_niches' => ['music', 'entertainment'],
                'min_followers' => 4000,
                'status' => 'active',
            ],
            [
                'title' => 'Educational Scholarship Program',
                'description' => 'Promoting scholarship opportunities for Nigerian students across educational channels.',
                'content' => '<p>Help Nigerian students access quality education by promoting our scholarship program. This initiative supports academic excellence and career development.</p><p>Program benefits:</p><ul><li>Full and partial scholarships</li><li>Mentorship opportunities</li><li>Career guidance</li></ul>',
                'duration_days' => 30,
                'budget' => 120000,
                'payment_per_channel' => 4000,
                'max_channels' => 30,
                'target_niches' => ['education'],
                'min_followers' => 7000,
                'status' => 'active',
            ],
            [
                'title' => 'Food Delivery Service Launch',
                'description' => 'New food delivery service launching in major Nigerian cities. Looking for food and lifestyle channels.',
                'content' => '<p>Revolutionary food delivery service launching across Nigeria. We offer fast, reliable delivery with a wide variety of local and international cuisines.</p><p>Service features:</p><ul><li>30-minute delivery guarantee</li><li>Wide restaurant selection</li><li>Affordable pricing</li></ul>',
                'duration_days' => 12,
                'budget' => 80000,
                'payment_per_channel' => 3200,
                'max_channels' => 25,
                'target_niches' => ['food', 'lifestyle'],
                'min_followers' => 9000,
                'status' => 'active',
            ],
            [
                'title' => 'Gaming Tournament Sponsorship',
                'description' => 'Sponsoring major gaming tournament. Looking for gaming and entertainment channels for promotion.',
                'content' => '<p>Be part of Nigeria\'s biggest gaming tournament! We\'re sponsoring this exciting event and need gaming channels to help promote it.</p><p>Tournament highlights:</p><ul><li>₦5 million prize pool</li><li>Multiple game categories</li><li>Live streaming coverage</li></ul>',
                'duration_days' => 8,
                'budget' => 45000,
                'payment_per_channel' => 2250,
                'max_channels' => 20,
                'target_niches' => ['gaming', 'entertainment'],
                'min_followers' => 5500,
                'status' => 'active',
            ],
            [
                'title' => 'Real Estate Investment Opportunity',
                'description' => 'Promoting real estate investment opportunities in Lagos and Abuja for potential investors.',
                'content' => '<p>Exclusive real estate investment opportunities in prime locations across Lagos and Abuja. Perfect for both first-time and experienced investors.</p><p>Investment benefits:</p><ul><li>High ROI potential</li><li>Prime locations</li><li>Flexible payment plans</li></ul>',
                'duration_days' => 15,
                'budget' => 100000,
                'payment_per_channel' => 5000,
                'max_channels' => 20,
                'target_niches' => ['business', 'finance'],
                'min_followers' => 12000,
                'status' => 'active',
            ],
            [
                'title' => 'Travel and Tourism Campaign',
                'description' => 'Promoting Nigerian tourist destinations and travel packages for local and international tourists.',
                'content' => '<p>Discover the beauty of Nigeria! Join our campaign to promote amazing tourist destinations and affordable travel packages across the country.</p><p>Campaign includes:</p><ul><li>Destination showcases</li><li>Travel tips and guides</li><li>Special discount offers</li></ul>',
                'duration_days' => 20,
                'budget' => 70000,
                'payment_per_channel' => 3500,
                'max_channels' => 20,
                'target_niches' => ['travel', 'lifestyle'],
                'min_followers' => 8500,
                'status' => 'active',
            ],
            [
                'title' => 'Fintech App Launch',
                'description' => 'New fintech app launching with innovative payment solutions for Nigerian businesses.',
                'content' => '<p>Revolutionary fintech app launching with cutting-edge payment solutions designed specifically for Nigerian businesses and individuals.</p><p>App features:</p><ul><li>Instant transfers</li><li>Low transaction fees</li><li>Advanced security</li></ul>',
                'duration_days' => 9,
                'budget' => 55000,
                'payment_per_channel' => 2750,
                'max_channels' => 20,
                'target_niches' => ['finance', 'technology'],
                'min_followers' => 7500,
                'status' => 'active',
            ],
            [
                'title' => 'Fitness Challenge Campaign',
                'description' => 'National fitness challenge promoting healthy living and exercise among Nigerians.',
                'content' => '<p>Join Nigeria\'s biggest fitness challenge! Promote healthy living and inspire your audience to adopt active lifestyles.</p><p>Challenge includes:</p><ul><li>Daily workout routines</li><li>Nutrition guidance</li><li>Community support</li></ul>',
                'duration_days' => 28,
                'budget' => 85000,
                'payment_per_channel' => 3400,
                'max_channels' => 25,
                'target_niches' => ['health', 'lifestyle'],
                'min_followers' => 6500,
                'status' => 'active',
            ],
            [
                'title' => 'E-commerce Platform Promotion',
                'description' => 'Promoting new e-commerce platform with focus on supporting local Nigerian businesses.',
                'content' => '<p>Support local Nigerian businesses through our new e-commerce platform. Help promote a marketplace that prioritizes local entrepreneurs and quality products.</p><p>Platform benefits:</p><ul><li>Support for local businesses</li><li>Competitive pricing</li><li>Fast delivery nationwide</li></ul>',
                'duration_days' => 11,
                'budget' => 65000,
                'payment_per_channel' => 3250,
                'max_channels' => 20,
                'target_niches' => ['business', 'technology'],
                'min_followers' => 8000,
                'status' => 'active',
            ],
            [
                'title' => 'Entertainment News Platform',
                'description' => 'New entertainment news platform focusing on Nollywood and Nigerian celebrity news.',
                'content' => '<p>Stay updated with the latest in Nigerian entertainment! Our new platform covers Nollywood, music industry news, and celebrity updates.</p><p>Platform features:</p><ul><li>Exclusive interviews</li><li>Breaking entertainment news</li><li>Behind-the-scenes content</li></ul>',
                'duration_days' => 6,
                'budget' => 35000,
                'payment_per_channel' => 1750,
                'max_channels' => 20,
                'target_niches' => ['entertainment', 'news'],
                'min_followers' => 5000,
                'status' => 'active',
            ],
            [
                'title' => 'Agricultural Innovation Campaign',
                'description' => 'Promoting modern agricultural techniques and technologies for Nigerian farmers.',
                'content' => '<p>Transform Nigerian agriculture with modern techniques and technologies. Help promote innovative farming solutions that increase productivity and sustainability.</p><p>Campaign covers:</p><ul><li>Modern farming techniques</li><li>Agricultural technology</li><li>Sustainable practices</li></ul>',
                'duration_days' => 25,
                'budget' => 95000,
                'payment_per_channel' => 3800,
                'max_channels' => 25,
                'target_niches' => ['business', 'other'],
                'min_followers' => 7000,
                'status' => 'active',
            ],
            [
                'title' => 'Online Learning Platform',
                'description' => 'Promoting comprehensive online learning platform with courses for Nigerian professionals.',
                'content' => '<p>Advance your career with our comprehensive online learning platform. Featuring courses designed specifically for Nigerian professionals across various industries.</p><p>Platform offers:</p><ul><li>Industry-relevant courses</li><li>Expert instructors</li><li>Certification programs</li></ul>',
                'duration_days' => 18,
                'budget' => 75000,
                'payment_per_channel' => 3750,
                'max_channels' => 20,
                'target_niches' => ['education', 'business'],
                'min_followers' => 9500,
                'status' => 'active',
            ],
            [
                'title' => 'Sports Equipment Brand',
                'description' => 'New sports equipment brand launching with focus on affordable, quality gear for Nigerian athletes.',
                'content' => '<p>Quality sports equipment designed for Nigerian athletes at affordable prices. Help promote gear that supports local sports development and athletic excellence.</p><p>Product range:</p><ul><li>Football equipment</li><li>Basketball gear</li><li>Athletic wear</li></ul>',
                'duration_days' => 13,
                'budget' => 60000,
                'payment_per_channel' => 3000,
                'max_channels' => 20,
                'target_niches' => ['sports'],
                'min_followers' => 6000,
                'status' => 'active',
            ],
            [
                'title' => 'Mobile App Development Service',
                'description' => 'Promoting mobile app development services for Nigerian businesses and entrepreneurs.',
                'content' => '<p>Professional mobile app development services tailored for Nigerian businesses. Help promote affordable, high-quality app development solutions.</p><p>Services include:</p><ul><li>Custom app development</li><li>UI/UX design</li><li>App store optimization</li></ul>',
                'duration_days' => 16,
                'budget' => 70000,
                'payment_per_channel' => 3500,
                'max_channels' => 20,
                'target_niches' => ['technology', 'business'],
                'min_followers' => 8500,
                'status' => 'active',
            ],
            [
                'title' => 'Beauty and Skincare Line',
                'description' => 'New beauty and skincare line featuring products made specifically for African skin.',
                'content' => '<p>Revolutionary beauty and skincare products formulated specifically for African skin. Help promote natural, effective beauty solutions for Nigerian women.</p><p>Product benefits:</p><ul><li>Natural ingredients</li><li>African skin-focused formulas</li><li>Affordable luxury</li></ul>',
                'duration_days' => 22,
                'budget' => 88000,
                'payment_per_channel' => 4400,
                'max_channels' => 20,
                'target_niches' => ['fashion', 'lifestyle'],
                'min_followers' => 10000,
                'status' => 'active',
            ],
            [
                'title' => 'Digital Marketing Agency',
                'description' => 'Full-service digital marketing agency helping Nigerian businesses grow their online presence.',
                'content' => '<p>Comprehensive digital marketing solutions for Nigerian businesses. Help promote services that drive online growth and business success.</p><p>Services offered:</p><ul><li>Social media marketing</li><li>SEO optimization</li><li>Content creation</li></ul>',
                'duration_days' => 14,
                'budget' => 65000,
                'payment_per_channel' => 3250,
                'max_channels' => 20,
                'target_niches' => ['business', 'technology'],
                'min_followers' => 7500,
                'status' => 'active',
            ]
        ];

        // Create channel ads
        foreach ($channelAdsData as $data) {
            $admin = $admins[array_rand($admins)];
            
            ChannelAd::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'content' => $data['content'],
                'duration_days' => $data['duration_days'],
                'budget' => $data['budget'],
                'payment_per_channel' => $data['payment_per_channel'],
                'max_channels' => $data['max_channels'],
                'target_niches' => json_encode($data['target_niches']),
                'min_followers' => $data['min_followers'],
                'status' => $data['status'],
                'created_by_admin_id' => $admin->id,
                'start_date' => now()->subDays(rand(1, 5)),
                'end_date' => now()->addDays(rand(10, 60)),
                'instructions' => '<p>Please follow the campaign guidelines and maintain professional standards when promoting this content.</p>',
                'requirements' => '<p>Channel must be active, have engaged audience, and provide proof of posting within 24 hours.</p>',
            ]);
        }
    }
}