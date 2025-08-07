<?php

namespace Database\Factories;

use App\Models\Voucher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Voucher::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currency = $this->faker->randomElement([Voucher::CURRENCY_NGN, Voucher::CURRENCY_CREDITS]);
        $amount = $currency === Voucher::CURRENCY_NGN 
            ? $this->faker->randomFloat(2, 100, 10000) 
            : $this->faker->numberBetween(10, 1000);

        return [
            'code' => 'VCH-' . strtoupper(Str::random(8)),
            'amount' => $amount,
            'currency' => $currency,
            'status' => $this->faker->randomElement([
                Voucher::STATUS_ACTIVE,
                Voucher::STATUS_REDEEMED,
                Voucher::STATUS_EXPIRED,
                Voucher::STATUS_CANCELLED,
            ]),
            'expires_at' => $this->faker->optional(0.7)->dateTimeBetween('now', '+1 year'),
            'description' => $this->faker->optional()->sentence(),
            'created_by' => User::factory(),
            'metadata' => [
                'generated_by' => 'factory',
                'test_data' => true,
            ],
            'batch_id' => $this->faker->optional(0.3)->uuid(),
        ];
    }

    /**
     * Indicate that the voucher is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Voucher::STATUS_ACTIVE,
            'expires_at' => $this->faker->dateTimeBetween('+1 day', '+1 year'),
            'redeemed_at' => null,
            'redeemed_by' => null,
        ]);
    }

    /**
     * Indicate that the voucher is redeemed.
     */
    public function redeemed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Voucher::STATUS_REDEEMED,
            'redeemed_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'redeemed_by' => User::factory(),
        ]);
    }

    /**
     * Indicate that the voucher is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Voucher::STATUS_EXPIRED,
            'expires_at' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
            'redeemed_at' => null,
            'redeemed_by' => null,
        ]);
    }

    /**
     * Indicate that the voucher is for NGN currency.
     */
    public function naira(): static
    {
        return $this->state(fn (array $attributes) => [
            'currency' => Voucher::CURRENCY_NGN,
            'amount' => $this->faker->randomFloat(2, 100, 10000),
        ]);
    }

    /**
     * Indicate that the voucher is for credits currency.
     */
    public function credits(): static
    {
        return $this->state(fn (array $attributes) => [
            'currency' => Voucher::CURRENCY_CREDITS,
            'amount' => $this->faker->numberBetween(10, 1000),
        ]);
    }

    /**
     * Create vouchers in a batch.
     */
    public function batch(string $batchId = null): static
    {
        $batchId = $batchId ?? Str::uuid();
        
        return $this->state(fn (array $attributes) => [
            'batch_id' => $batchId,
            'metadata' => array_merge($attributes['metadata'] ?? [], [
                'batch_id' => $batchId,
                'batch_created_at' => now()->toISOString(),
            ]),
        ]);
    }

    /**
     * Create vouchers with specific amount.
     */
    public function withAmount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }

    /**
     * Create vouchers that expire soon.
     */
    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('now', '+7 days'),
            'status' => Voucher::STATUS_ACTIVE,
        ]);
    }
}