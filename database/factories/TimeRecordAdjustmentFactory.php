<?php

namespace Database\Factories;

use App\Models\TimeRecordAdjustment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeRecordAdjustment>
 */
class TimeRecordAdjustmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TimeRecordAdjustment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'time_record_id' => $this->faker->numberBetween(1, 10),
            'user_id' => $this->faker->numberBetween(1, 10),
            'current_value' => $this->faker->paragraph(),
            'requested_value' => $this->faker->paragraph(),
            'reason' => $this->faker->paragraph(),
            'reviewed_by' => $this->faker->numberBetween(1, 10),
            'reviewed_at' => $this->faker->word(),
            'admin_notes' => $this->faker->paragraph()
        ];
    }

    /**
     * Indicate that the TimeRecordAdjustment is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the TimeRecordAdjustment is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Create a TimeRecordAdjustment with specific attributes.
     */
    public function withAttributes(array $attributes): static
    {
        return $this->state(fn (array $factoryAttributes) => $attributes);
    }
}