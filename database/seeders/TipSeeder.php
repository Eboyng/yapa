<?php

namespace Database\Seeders;

use App\Models\Tip;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create an admin user
        $admin = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        if (!$admin) {
            // If no admin exists, create one
            $admin = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
            ]);
            // Note: You might need to assign admin role here depending on your role system
        }

        // Create specific tips with meaningful content
        $tips = [
            [
                'title' => '5 Essential WhatsApp Business Features Every Entrepreneur Should Use',
                'content' => "WhatsApp Business has revolutionized how entrepreneurs connect with their customers. Here are five game-changing features that can transform your business communication:\n\n1. **Business Profile**: Create a professional presence with your business description, contact information, and website link. This builds trust and credibility with potential customers.\n\n2. **Automated Messages**: Set up welcome messages, away messages, and quick replies to ensure customers always receive timely responses, even when you're busy.\n\n3. **Catalog Feature**: Showcase your products or services directly within WhatsApp. Customers can browse, ask questions, and make purchases without leaving the app.\n\n4. **Labels and Organization**: Use labels to categorize your chats - new customers, pending orders, completed sales. This helps you stay organized and follow up effectively.\n\n5. **WhatsApp Web**: Manage your business conversations from your computer, making it easier to type longer messages and handle multiple conversations simultaneously.\n\nImplementing these features can significantly improve your customer service and boost sales. Start with one feature at a time and gradually incorporate others as you become more comfortable.",
                'status' => Tip::STATUS_PUBLISHED,
                'published_at' => now()->subDays(2),
                'claps' => 45,
            ],
            [
                'title' => 'Building Your Personal Brand: Why Authenticity Beats Perfection',
                'content' => "In today's digital world, personal branding isn't just for celebrities and influencers - it's essential for every professional and entrepreneur. But here's the secret: authenticity trumps perfection every time.\n\n**Why Authenticity Matters:**\n\nPeople connect with real stories, not polished facades. When you share your genuine experiences, including failures and lessons learned, you create deeper connections with your audience.\n\n**Practical Steps to Build Authentic Personal Brand:**\n\n• Share your journey, including the ups and downs\n• Be consistent in your messaging across all platforms\n• Engage genuinely with your community\n• Provide value through your expertise and experiences\n• Stay true to your values, even when it's challenging\n\n**The Power of Vulnerability:**\n\nSharing your struggles and how you overcame them makes you relatable. It shows that success isn't about being perfect - it's about persistence, learning, and growth.\n\n**Remember:** Your personal brand is not about creating a perfect image. It's about being genuinely helpful, consistently valuable, and authentically you. People will remember how you made them feel, not how perfect you appeared.\n\nStart today by sharing one authentic story about your professional journey. You'll be surprised by the positive response you receive.",
                'status' => Tip::STATUS_PUBLISHED,
                'published_at' => now()->subDays(5),
                'claps' => 78,
            ],
            [
                'title' => 'The Psychology of Pricing: How to Price Your Services for Maximum Profit',
                'content' => "Pricing your services correctly is both an art and a science. Understanding the psychology behind pricing can dramatically impact your business success.\n\n**The Anchoring Effect:**\n\nAlways present your highest-priced option first. This creates an 'anchor' that makes your other options seem more reasonable by comparison.\n\n**The Power of 9:**\n\nPrices ending in 9 ($99, $199) are perceived as significantly lower than round numbers ($100, $200), even though the difference is minimal.\n\n**Value-Based Pricing Strategy:**\n\nInstead of pricing based on time, price based on the value you provide. Ask yourself: 'What is the result worth to my client?' A $5,000 strategy that saves a business $50,000 is incredibly valuable.\n\n**The Decoy Effect:**\n\nOffer three pricing tiers. Make the middle option your target sale - it should offer the best value. The highest tier makes the middle seem reasonable, while the lowest tier makes the middle seem premium.\n\n**Confidence in Your Pricing:**\n\nYour confidence in your pricing affects how clients perceive value. If you're uncomfortable with your prices, clients will sense it. Practice stating your prices with conviction.\n\n**Testing and Adjusting:**\n\nDon't be afraid to test different price points. Start higher than you think - you can always come down, but it's much harder to raise prices later.\n\nRemember: Cheap prices attract cheap clients. Price for the clients you want to work with, not the ones you think you deserve.",
                'status' => Tip::STATUS_PUBLISHED,
                'published_at' => now()->subDays(7),
                'claps' => 92,
            ],
        ];

        foreach ($tips as $tipData) {
            $tipData['author_id'] = $admin->id;
            $tipData['slug'] = Str::slug($tipData['title']);
            
            Tip::create($tipData);
        }

        // Create additional random tips using factory
        Tip::factory()
            ->count(7)
            ->published()
            ->create(['author_id' => $admin->id]);

        Tip::factory()
            ->count(3)
            ->draft()
            ->create(['author_id' => $admin->id]);

        Tip::factory()
            ->count(2)
            ->archived()
            ->create(['author_id' => $admin->id]);
    }
}