<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['income', 'expense']);

        return [
            'user_id'          => User::factory(),
            'category_id'      => Category::factory()->for(User::factory())->state(['type' => $type]),
            'type'             => $type,
            'amount'           => fake()->randomFloat(2, 1, 9999),
            'description'      => fake()->optional(0.7)->sentence(3),
            'transaction_date' => fake()->dateTimeBetween('-60 days', 'now'),
        ];
    }

    public function income(): static
    {
        return $this->state(fn () => [
            'type'   => 'income',
            'amount' => fake()->randomFloat(2, 100, 9999),
        ]);
    }

    public function expense(): static
    {
        return $this->state(fn () => [
            'type'   => 'expense',
            'amount' => fake()->randomFloat(2, 1, 1000),
        ]);
    }
}
