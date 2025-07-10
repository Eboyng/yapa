<?php

namespace Database\Factories;

use App\Models\ChannelPurchase;
use App\Models\ChannelSale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChannelPurchase>
 */
class ChannelPurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $channelSale = ChannelSale::factory()->create();
        
        return [
            'buyer_id' => User::factory(),
            'channel_sale_id' => $channelSale->id,
            'price' => $channelSale->price,
            'escrow_transaction_id' => $this->faker->uuid(),
            'status' => $this->faker->randomElement([
                ChannelPurchase::STATUS_PENDING,
                ChannelPurchase::STATUS_IN_ESCROW,
                ChannelPurchase::STATUS_COMPLETED,
                ChannelPurchase::STATUS_FAILED,
                ChannelPurchase::STATUS_REFUNDED,
            ]),
            'buyer_note' => $this->faker->optional(0.7)->sentence(),
            'admin_note' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the purchase is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ChannelPurchase::STATUS_PENDING,
            'escrow_transaction_id' => null,
        ]);
    }

    /**
     * Indicate that the purchase is in escrow.
     */
    public function inEscrow(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ChannelPurchase::STATUS_IN_ESCROW,
            'escrow_transaction_id' => $this->faker->uuid(),
        ]);
    }

    /**
     * Indicate that the purchase is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ChannelPurchase::STATUS_COMPLETED,
            'escrow_transaction_id' => $this->faker->uuid(),
            'admin_note' => 'Purchase completed successfully',
        ]);
    }

    /**
     * Indicate that the purchase failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ChannelPurchase::STATUS_FAILED,
            'admin_note' => 'Purchase failed due to insufficient funds',
        ]);
    }

    /**
     * Indicate that the purchase was refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ChannelPurchase::STATUS_REFUNDED,
            'escrow_transaction_id' => $this->faker->uuid(),
            'admin_note' => 'Purchase refunded by admin',
        ]);
    }

    /**
     * Create a purchase for a specific channel sale.
     */
    public function forChannelSale(ChannelSale $channelSale): static
    {
        return $this->state(fn (array $attributes) => [
            'channel_sale_id' => $channelSale->id,
            'price' => $channelSale->price,
        ]);
    }

    /**
     * Create a purchase by a specific buyer.
     */
    public function byBuyer(User $buyer): static
    {
        return $this->state(fn (array $attributes) => [
            'buyer_id' => $buyer->id,
        ]);
    }
}