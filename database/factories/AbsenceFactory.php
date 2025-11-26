<?php

namespace Database\Factories;

use App\Models\Absence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Absence>
 */
class AbsenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Absence::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->word(),
            'start_time' => $this->faker->word(),
            'end_time' => $this->faker->word(),
            'reason' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'approved_at' => $this->faker->word()
        ];
    }

    /**
     * Indicate that the Absence is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the Absence is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Create a Absence with specific attributes.
     */
    public function withAttributes(array $attributes): static
    {
        return $this->state(fn (array $factoryAttributes) => $attributes);
    }
}