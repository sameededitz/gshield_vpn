<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => fake()->boolean(70) ? now() : null, // 70% chance to have a verified email
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'user',
            'last_login' => fake()->optional()->dateTimeBetween('-1 year', 'now'), // Random time or null
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Define an admin state.
     */
    public function admin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
            ];
        });
    }

    /**
     * Define a user state.
     */
    public function user(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'user',
                'email' => 'user@gmail.com',
                'password' => Hash::make('user12345'),
                'role' => 'user',
            ];
        });
    }
}
