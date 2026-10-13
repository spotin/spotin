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
        ];
    }

    /**
     * Indicate that the creator of the spot is gone.
     */
    public function withoutCreator(): static
    {
        return $this->state(fn () => ['created_by' => null]);
    }
}
