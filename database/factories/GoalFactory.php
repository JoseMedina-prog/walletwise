<?php

namespace Database\Factories;

use App\Models\Goal;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Goal>
 */
class GoalFactory extends Factory
{
    protected $model = Goal::class;

    public function definition(): array
    {
        $start = CarbonImmutable::now()->startOfMonth();
        return [
            'user_id'         => User::factory(),
            'name'            => fake()->randomElement(['Vacaciones', 'Coche', 'Fondo emergencia', 'Ordenador', 'Boda']),
            'description'     => fake()->optional()->sentence(),
            'target_amount'   => fake()->randomFloat(2, 500, 10000),
            'current_amount'  => 0,
            'start_date'      => $start->toDateString(),
            'target_date'     => $start->addMonths(6)->toDateString(),
            'color'           => '#10b981',
            'icon'            => null,
            'is_completed'    => false,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'is_completed' => true,
            'completed_at' => now()->toDateString(),
        ]);
    }
}