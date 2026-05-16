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
        $user  = User::where('role', 'client')->inRandomOrder()->first()
            ?? User::factory()->create(['role' => 'client']);
        $agent = User::whereIn('role', ['agent', 'admin'])->inRandomOrder()->first()
            ?? User::factory()->create(['role' => 'agent']);
        $category = Category::inRandomOrder()->first();

        return [
            'title' => fake()->randomElement([
                'Error al iniciar sesión',
                'Problema con impresora',
                'No carga el dashboard',
                'Fallo en sistema de tickets',
                'Error de conexión',
                'Problema de red',
                'Acceso denegado',
            ]),
            'description' => fake()->randomElement([
                'El usuario reporta que no puede acceder al sistema con sus credenciales.',
                'La impresora no responde al enviar documentos.',
                'El dashboard principal no carga correctamente.',
                'Se detectó una falla al crear tickets nuevos.',
                'El sistema presenta problemas de conexión intermitente.',
                'La red institucional está presentando lentitud.',
                'El usuario recibe mensaje de acceso denegado.',
            ]),
            'status' => fake()->randomElement(Ticket::STATUSES),
            'priority' => fake()->randomElement(Ticket::PRIORITIES),
            'user_id' => $user ? $user->id : User::factory(),
            'agent_id' => $agent ? $agent->id : User::factory(),
            'category_id' => $category ? $category->id : Category::factory(),
            'due_date' => now()->addDays(rand(1, 10)),
        ];
    }
}
