<?php

namespace Database\Factories;

use App\Models\Goal;
use App\Models\GoalContribution;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GoalContribution>
 */
class GoalContributionFactory extends Factory
{
    protected $model = GoalContribution::class;

    public function definition(): array
    {
        return [
            'goal_id'           => Goal::factory(),
            'transaction_id'    => null,
            'amount'            => fake()->randomFloat(2, 10, 500),
            'contribution_date' => CarbonImmutable::now()->subDays(fake()->numberBetween(0, 60))->toDateString(),
            'note'              => fake()->optional()->sentence(3),
        ];
    }
}