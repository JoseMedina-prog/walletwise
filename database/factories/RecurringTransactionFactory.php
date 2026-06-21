<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecurringTransaction>
 */
class RecurringTransactionFactory extends Factory
{
    protected $model = RecurringTransaction::class;

    public function definition(): array
    {
        $start = CarbonImmutable::now()->startOfMonth();
        return [
            'user_id'         => User::factory(),
            'category_id'     => Category::factory()->for(User::factory())->expense(),
            'type'            => 'expense',
            'amount'          => fake()->randomFloat(2, 10, 500),
            'description'     => fake()->optional()->sentence(3),
            'frequency'       => 'monthly',
            'interval'        => 1,
            'start_date'      => $start->toDateString(),
            'next_occurrence' => $start->toDateString(),
            'end_date'        => null,
            'is_active'       => true,
        ];
    }

    public function income(): static
    {
        return $this->state(fn () => [
            'type'     => 'income',
            'amount'   => fake()->randomFloat(2, 500, 5000),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function weekly(): static
    {
        return $this->state(fn () => ['frequency' => 'weekly']);
    }
}