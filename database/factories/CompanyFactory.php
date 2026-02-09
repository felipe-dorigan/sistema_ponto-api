<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'cnpj' => $this->faker->words(3, true),
            'email' => $this->faker->words(3, true),
            'phone' => $this->faker->words(3, true),
            'address' => $this->faker->paragraph(),
            'city' => $this->faker->words(3, true),
            'state' => $this->faker->words(3, true),
            'zip_code' => $this->faker->words(3, true),
            'max_users' => $this->faker->numberBetween(1, 10),
            'active' => $this->faker->boolean(80)
        ];
    }

    /**
     * Indicate that the Company is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the Company is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Create a Company with specific attributes.
     */
    public function withAttributes(array $attributes): static
    {
        return $this->state(fn (array $factoryAttributes) => $attributes);
    }
}