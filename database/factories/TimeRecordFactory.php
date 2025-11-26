<?php

namespace Database\Factories;

use App\Models\TimeRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeRecord>
 */
class TimeRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TimeRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->word(),
            'entry_time' => $this->faker->word(),
            'exit_time' => $this->faker->word(),
            'lunch_start' => $this->faker->word(),
            'lunch_end' => $this->faker->word(),
            'worked_minutes' => $this->faker->numberBetween(0, 100),
            'expected_minutes' => $this->faker->numberBetween(0, 100),
            'notes' => $this->faker->paragraph(),
            'entry_time_recorded_at' => $this->faker->word(),
            'exit_time_recorded_at' => $this->faker->word(),
            'lunch_start_recorded_at' => $this->faker->word(),
            'lunch_end_recorded_at' => $this->faker->word()
        ];
    }

    /**
     * Indicate that the TimeRecord is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the TimeRecord is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Create a TimeRecord with specific attributes.
     */
    public function withAttributes(array $attributes): static
    {
        return $this->state(fn (array $factoryAttributes) => $attributes);
    }
}