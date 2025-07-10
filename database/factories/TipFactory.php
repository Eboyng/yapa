<?php

namespace Database\Factories;

use App\Models\Tip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tip>
 */
class TipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(rand(4, 8));
        $content = $this->faker->paragraphs(rand(3, 6), true);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'content' => $content,
            'author_id' => User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->inRandomOrder()->first()?->id ?? User::factory()->create()->id,
            'status' => $this->faker->randomElement([Tip::STATUS_DRAFT, Tip::STATUS_PUBLISHED, Tip::STATUS_ARCHIVED]),
            'published_at' => $this->faker->optional(0.7)->dateTimeBetween('-30 days', '+7 days'),
            'claps' => $this->faker->numberBetween(0, 500),
        ];
    }

    /**
     * Indicate that the tip is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Tip::STATUS_PUBLISHED,
            'published_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Indicate that the tip is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Tip::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the tip is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Tip::STATUS_ARCHIVED,
            'published_at' => $this->faker->dateTimeBetween('-60 days', '-30 days'),
        ]);
    }
}