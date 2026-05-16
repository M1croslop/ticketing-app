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
        return [
            'name' => fake()->randomElement([
                'Hardware',
                'Software',
                'Redes',
                'Autenticación',
                'Base de Datos',
                'UI/UX',
                'Seguridad'
            ]),
            'description' => fake()->randomElement([
                'Problemas relacionados con equipos físicos',
                'Errores o fallos del sistema',
                'Incidentes de red y conectividad',
                'Problemas de autenticación y acceso',
                'Consultas relacionadas con bases de datos',
                'Problemas de interfaz y experiencia de usuario',
                'Incidentes relacionados con seguridad informática',
            ]),
        ];
    }
}
