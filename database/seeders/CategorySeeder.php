<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Category::insert([
            ['name' => 'Hardware',  'description' => 'Equipos físicos'],
            ['name' => 'Software',  'description' => 'Aplicaciones y programas'],
            ['name' => 'Redes',     'description' => 'Conexión e internet'],
            ['name' => 'Accesos',   'description' => 'Contraseñas y permisos'],
            ['name' => 'Otro',      'description' => 'Otros problemas'],
        ]);
    }
}