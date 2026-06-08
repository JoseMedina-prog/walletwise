<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $types = ['income', 'expense'];

        return [
            'user_id' => User::factory(),
            'name'    => fake()->unique()->word() . ' ' . fake()->randomElement(['Salary', 'Food', 'Rent', 'Bonus', 'Transport']),
            'type'    => fake()->randomElement($types),
        ];
    }

    public function income(): static
    {
        return $this->state(fn () => ['type' => 'income']);
    }

    public function expense(): static
    {
        return $this->state(fn () => ['type' => 'expense']);
    }
}
