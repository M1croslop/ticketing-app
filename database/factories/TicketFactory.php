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

        $tickets = [
            [
                'title' => 'Error al iniciar sesión',
                'description' => 'El usuario reporta que no puede acceder al sistema con sus credenciales.',
                'category' => 'Autenticación',
            ],
            [
                'title' => 'Problema con impresora',
                'description' => 'La impresora no responde al enviar documentos.',
                'category' => 'Hardware',
            ],
            [
                'title' => 'No carga el dashboard',
                'description' => 'El dashboard principal no carga correctamente.',
                'category' => 'Software',
            ],
            [
                'title' => 'Fallo en sistema de tickets',
                'description' => 'Se detectó una falla al crear tickets nuevos.',
                'category' => 'Software',
            ],
            [
                'title' => 'Error de conexión',
                'description' => 'El sistema presenta problemas de conexión intermitente.',
                'category' => 'Redes',
            ],
            [
                'title' => 'Problema de red',
                'description' => 'La red institucional está presentando lentitud.',
                'category' => 'Redes',
            ],
            [
                'title' => 'Acceso denegado',
                'description' => 'El usuario recibe mensaje de acceso denegado.',
                'category' => 'Seguridad',
            ],
            [
                'title' => 'Correo no sincroniza',
                'description' => 'El correo institucional no sincroniza correctamente.',
                'category' => 'Correo Electrónico',
            ],
            [
                'title' => 'Servidor fuera de línea',
                'description' => 'El servidor principal dejó de responder inesperadamente.',
                'category' => 'Servidores',
            ],
            [
                'title' => 'Actualización fallida',
                'description' => 'La actualización del sistema no se completó correctamente.',
                'category' => 'Software',
            ],
        ];

        $ticket = fake()->randomElement($tickets);

        $category = Category::where('name', $ticket['category'])->first();

        return [
            'title' => $ticket['title'],
            'description' => $ticket['description'],
            'status' => fake()->randomElement(Ticket::STATUSES),
            'priority' => fake()->randomElement(Ticket::PRIORITIES),
            'user_id' => $user ? $user->id : User::factory(),
            'agent_id' => $agent ? $agent->id : User::factory(),
            'category_id' => $category?->id ?? Category::factory(),
            'due_date' => now()->addDays(rand(1, 10)),
        ];
    }
}
