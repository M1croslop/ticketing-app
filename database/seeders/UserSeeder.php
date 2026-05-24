<?php

namespace Database\Seeders;
use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@synapso.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Agente Soporte',
            'email'    => 'agente@synapso.com',
            'password' => bcrypt('password'),
            'role'     => 'agent',
        ]);

        User::create([
            'name'     => 'Cliente Demo',
            'email'    => 'cliente@synapso.com',
            'password' => bcrypt('password'),
            'role'     => 'client',
        ]);

        // Usuarios faker adicionales para volumen
        User::factory()->count(1)->create(['role' => 'admin']);
        User::factory()->count(2)->create(['role' => 'agent']);
        User::factory()->count(3)->create(['role' => 'client']);
    }
}
