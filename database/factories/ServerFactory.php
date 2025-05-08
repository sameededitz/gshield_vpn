<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Server>
 */
class ServerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'android' => $this->faker->boolean(),
            'ios' => $this->faker->boolean(),
            'macos' => $this->faker->boolean(),
            'windows' => $this->faker->boolean(),
            'longitude' => $this->faker->longitude(),
            'latitude' => $this->faker->latitude(),
            'type' => $this->faker->randomElement(['free', 'premium']),
            'status' => $this->faker->boolean(60),
        ];
    }
}
