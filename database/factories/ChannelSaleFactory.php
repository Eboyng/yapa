<?php

namespace Database\Factories;

use App\Models\ChannelSale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChannelSale>
 */
class ChannelSaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'channel_name' => $this->faker->words(3, true) . ' Channel',
            'whatsapp_number' => '+234' . $this->faker->numerify('##########'),
            'category' => $this->faker->randomElement(array_keys(ChannelSale::CATEGORIES)),
            'audience_size' => $this->faker->numberBetween(100, 50000),
            'engagement_rate' => $this->faker->randomFloat(2, 1, 25),
            'description' => $this->faker->paragraph(3),
            'price' => $this->faker->randomFloat(2, 5000, 500000),
            'screenshots' => null,
            'status' => $this->faker->randomElement([
                ChannelSale::STATUS_LISTED,
                ChannelSale::STATUS_UNDER_REVIEW,
                ChannelSale::STATUS_SOLD,
                ChannelSale::STATUS_REMOVED,
            ]),
            'visibility' => $this->faker->boolean(80), // 80% chance of being visible
        ];
    }

    /**
     * Indicate that the channel sale is listed.
     */
    public function listed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ChannelSale::STATUS_LISTED,
            'visibility' => true,
        ]);
    }

    /**
     * Indicate that the channel sale is under review.
     */
    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ChannelSale::STATUS_UNDER_REVIEW,
            'visibility' => false,
        ]);
    }

    /**
     * Indicate that the channel sale is sold.
     */
    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ChannelSale::STATUS_SOLD,
            'visibility' => false,
        ]);
    }

    /**
     * Indicate that the channel sale is removed.
     */
    public function removed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ChannelSale::STATUS_REMOVED,
            'visibility' => false,
        ]);
    }

    /**
     * Indicate that the channel sale has high engagement.
     */
    public function highEngagement(): static
    {
        return $this->state(fn (array $attributes) => [
            'engagement_rate' => $this->faker->randomFloat(2, 15, 30),
            'audience_size' => $this->faker->numberBetween(10000, 100000),
            'price' => $this->faker->randomFloat(2, 50000, 1000000),
        ]);
    }

    /**
     * Indicate that the channel sale is affordable.
     */
    public function affordable(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $this->faker->randomFloat(2, 1000, 10000),
            'audience_size' => $this->faker->numberBetween(100, 5000),
        ]);
    }
}