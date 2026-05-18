<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
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
        $firstName = fake()->randomElement([
            'Carlos',
            'Juan',
            'Pedro',
            'Ana',
            'Luis',
            'Marta',
            'Miguel',
            'Valeria',
            'Fernando',
            'Daniela'
        ]);

        $lastName = fake()->randomElement([
            'Alfaro',
            'Flamengo',
            'Iraheta',
            'Castro',
            'Marroquin',
            'Flores',
            'Castro',
            'Morales',
            'Rivas',
            'Villalobos'
        ]);

        $fullName = $firstName . ' ' . $lastName;
        $firstName = explode(' ', $fullName)[0];

        return [
            'name' => $fullName,
            'email' => strtolower($firstName) . fake()->unique()->numberBetween(1, 999) . '@ticketing.com',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement(['admin', 'agent', 'client']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
