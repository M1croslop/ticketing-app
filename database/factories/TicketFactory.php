<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        $agent = User::inRandomOrder()->first();
        $category = Category::inRandomOrder()->first();

        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['open', 'in_progress', 'closed']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'user_id' => $user ? $user->id : User::factory(),
            'agent_id' => $agent ? $agent->id : User::factory(),
            'category_id' => $category ? $category->id : Category::factory(),
            'due_date' => now()->addDays(5),
        ];
    }
}
