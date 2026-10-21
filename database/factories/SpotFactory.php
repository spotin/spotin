<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Spot>
 */
class SpotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'created_by' => User::factory(),
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->paragraphs(2, true),
            // Around Switzerland, where the default map is centered
            'latitude' => fake()->latitude(45.8, 47.8),
            'longitude' => fake()->longitude(5.9, 10.5),
            'website' => fake()->optional()->url(),
        ];
    }

    /**
     * Indicate that the spot is only listed for the members of its organization.
     */
    public function hidden(): static
    {
        return $this->state(fn () => ['is_hidden' => true]);
    }

    /**
     * Indicate that the visitors of the spot are redirected to its website.
     */
    public function redirecting(): static
    {
        return $this->state(fn () => ['website' => fake()->url(), 'redirect_to_website' => true]);
    }

    /**
     * Indicate that the creator of the spot is gone.
     */
    public function withoutCreator(): static
    {
        return $this->state(fn () => ['created_by' => null]);
    }
}
