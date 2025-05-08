<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Creates a user using the User factory
            'plan_id' => Plan::factory(), // Creates a plan using the Plan factory
            'amount_paid' => $this->faker->randomFloat(2, 10, 100), // Amount paid between 10 and 100
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'), // Random start date within the past year
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'), // Optional end date within the next year
            'status' => $this->faker->randomElement(['active', 'expired', 'cancelled']), // Random status
            'created_at' => $this->faker->dateTimeBetween('-6 month', 'now'),
        ];
    }
}
