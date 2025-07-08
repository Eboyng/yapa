<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create an admin user
        $admin = User::where('email', 'admin@yapa.ng')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@yapa.ng',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'whatsapp_number' => '+2348012345678',
                'credits_balance' => 100,
                'naira_balance' => 0,
                'earnings_balance' => 0,
                'location' => 'Lagos, Nigeria',
                'email_verification_enabled' => true,
                'whatsapp_verified_at' => now(),
            ]);
        }

        $adData = [
            [
                'title' => 'Promote Our New Mobile App',
                'description' => 'Help us spread the word about our revolutionary mobile app that connects people worldwide. Share this on your WhatsApp Status and earn money for every view!',
                'url' => 'https://play.google.com/store/apps/details?id=com.example.app',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.30,
                'max_participants' => 100,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(30),
            ],
            [
                'title' => 'Fashion Brand Launch Campaign',
                'description' => 'Be part of our exclusive fashion brand launch! Share our latest collection and earn while helping fashion enthusiasts discover amazing styles.',
                'url' => 'https://fashionbrand.com/new-collection',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.25,
                'max_participants' => 150,
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(25),
            ],
            [
                'title' => 'Tech Gadget Review Campaign',
                'description' => 'Share our latest tech gadget review and help tech enthusiasts make informed decisions. Great earning opportunity for tech lovers!',
                'url' => 'https://techreview.com/latest-gadget',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.35,
                'max_participants' => 80,
                'start_date' => now(),
                'end_date' => now()->addDays(20),
            ],
            [
                'title' => 'Food Delivery Service Promotion',
                'description' => 'Promote our fast and reliable food delivery service. Help hungry customers discover delicious meals delivered to their doorstep.',
                'url' => 'https://fooddelivery.com/order-now',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.28,
                'max_participants' => 200,
                'start_date' => now()->subHours(12),
                'end_date' => now()->addDays(15),
            ],
            [
                'title' => 'Online Learning Platform',
                'description' => 'Share our comprehensive online learning platform that offers courses in technology, business, and creative skills. Education for everyone!',
                'url' => 'https://learnplatform.com/courses',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.32,
                'max_participants' => 120,
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(45),
            ],
            [
                'title' => 'Fitness App Challenge',
                'description' => 'Join our 30-day fitness challenge! Share this fitness app that helps people achieve their health goals with personalized workout plans.',
                'url' => 'https://fitnessapp.com/challenge',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.27,
                'max_participants' => 300,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
            ],
            [
                'title' => 'Travel Booking Platform',
                'description' => 'Help travelers discover amazing destinations with our travel booking platform. Share and earn while inspiring wanderlust!',
                'url' => 'https://travelbook.com/destinations',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.33,
                'max_participants' => 90,
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(40),
            ],
            [
                'title' => 'Cryptocurrency Exchange Launch',
                'description' => 'Be part of the crypto revolution! Share our secure and user-friendly cryptocurrency exchange platform with your network.',
                'url' => 'https://cryptoexchange.com/signup',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.40,
                'max_participants' => 60,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
            ],
            [
                'title' => 'E-commerce Store Grand Opening',
                'description' => 'Celebrate our e-commerce store grand opening! Share amazing deals and discounts with your friends and family.',
                'url' => 'https://ecommerce.com/grand-opening',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.29,
                'max_participants' => 250,
                'start_date' => now()->subHours(6),
                'end_date' => now()->addDays(14),
            ],
            [
                'title' => 'Music Streaming Service',
                'description' => 'Share the joy of music! Promote our music streaming service that offers millions of songs and exclusive content from top artists.',
                'url' => 'https://musicstream.com/premium',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.26,
                'max_participants' => 180,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(35),
            ],
            [
                'title' => 'Real Estate Investment Platform',
                'description' => 'Help people discover smart real estate investment opportunities. Share our platform that makes property investment accessible to everyone.',
                'url' => 'https://realestateinvest.com/opportunities',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.45,
                'max_participants' => 50,
                'start_date' => now()->subDays(4),
                'end_date' => now()->addDays(60),
            ],
            [
                'title' => 'Gaming Tournament Announcement',
                'description' => 'Gamers unite! Share our epic gaming tournament with massive prize pools. Help fellow gamers discover this amazing opportunity.',
                'url' => 'https://gamingtournament.com/register',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.31,
                'max_participants' => 400,
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(10),
            ],
            [
                'title' => 'Health & Wellness Blog',
                'description' => 'Promote healthy living! Share our health and wellness blog that provides expert tips, nutrition advice, and wellness strategies.',
                'url' => 'https://healthwellness.com/blog',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.24,
                'max_participants' => 160,
                'start_date' => now()->subDays(6),
                'end_date' => now()->addDays(50),
            ],
            [
                'title' => 'Photography Course Launch',
                'description' => 'Capture the moment! Share our comprehensive photography course that teaches everything from basics to advanced techniques.',
                'url' => 'https://photocourse.com/masterclass',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.36,
                'max_participants' => 70,
                'start_date' => now(),
                'end_date' => now()->addDays(28),
            ],
            [
                'title' => 'Sustainable Living Products',
                'description' => 'Go green! Share our eco-friendly products that help people live sustainably while reducing their environmental footprint.',
                'url' => 'https://sustainablelife.com/products',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.30,
                'max_participants' => 110,
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(42),
            ],
            [
                'title' => 'Language Learning App',
                'description' => 'Break language barriers! Share our innovative language learning app that makes learning new languages fun and effective.',
                'url' => 'https://languageapp.com/learn',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.28,
                'max_participants' => 220,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(38),
            ],
            [
                'title' => 'Digital Marketing Agency',
                'description' => 'Boost your business! Share our digital marketing agency that helps businesses grow their online presence and reach more customers.',
                'url' => 'https://digitalmarketing.com/services',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.38,
                'max_participants' => 85,
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(33),
            ],
            [
                'title' => 'Pet Care Service Platform',
                'description' => 'Pet lovers unite! Share our pet care service platform that connects pet owners with trusted veterinarians and pet sitters.',
                'url' => 'https://petcare.com/services',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.27,
                'max_participants' => 140,
                'start_date' => now()->subHours(18),
                'end_date' => now()->addDays(22),
            ],
            [
                'title' => 'Home Renovation Services',
                'description' => 'Transform your space! Share our home renovation services that help homeowners create their dream living spaces.',
                'url' => 'https://homerenovation.com/gallery',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.34,
                'max_participants' => 95,
                'start_date' => now()->subDays(4),
                'end_date' => now()->addDays(55),
            ],
            [
                'title' => 'Career Development Platform',
                'description' => 'Advance your career! Share our career development platform that offers professional courses, mentorship, and job opportunities.',
                'url' => 'https://careerdev.com/opportunities',
                'status' => Ad::STATUS_ACTIVE,
                'earnings_per_view' => 0.32,
                'max_participants' => 130,
                'start_date' => now()->subDays(7),
                'end_date' => now()->addDays(45),
            ],
        ];

        foreach ($adData as $data) {
            Ad::create(array_merge($data, [
                'created_by_admin_id' => $admin->id,
                'instructions' => 'Please share this content on your WhatsApp Status and take a screenshot after 24 hours showing the view count.',
                'terms_and_conditions' => 'By participating in this ad campaign, you agree to follow our community guidelines and provide authentic engagement.',
            ]));
        }
    }
}