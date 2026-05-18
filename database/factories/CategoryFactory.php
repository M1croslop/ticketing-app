<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            [
                'name' => 'Hardware',
                'description' => 'Problemas relacionados con equipos físicos',
            ],
            [
                'name' => 'Software',
                'description' => 'Errores o fallos del sistema',
            ],
            [
                'name' => 'Redes',
                'description' => 'Incidentes de red y conectividad',
            ],
            [
                'name' => 'Autenticación',
                'description' => 'Problemas de autenticación y acceso',
            ],
            [
                'name' => 'Base de Datos',
                'description' => 'Consultas relacionadas con bases de datos',
            ],
            [
                'name' => 'UI/UX',
                'description' => 'Problemas de interfaz y experiencia de usuario',
            ],
            [
                'name' => 'Seguridad',
                'description' => 'Incidentes relacionados con seguridad informática',
            ],
            [
                'name' => 'Correo Electrónico',
                'description' => 'Problemas relacionados con correos y notificaciones',
            ],
            [
                'name' => 'Servidores',
                'description' => 'Incidentes relacionados con servidores y hosting',
            ],
            [
                'name' => 'Soporte Técnico',
                'description' => 'Solicitudes generales de soporte técnico',
            ],
        ];

        return fake()->randomElement($categories);
    }
}