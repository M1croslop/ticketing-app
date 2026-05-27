<?php

namespace Database\Seeders;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agents  = User::whereIn('role', ['agent', 'admin'])->pluck('id');
        $clients = User::where('role', 'client')->pluck('id');
        $categories = Category::pluck('id');

        Ticket::factory()->count(3)->create([
            'status'      => 'open',
            'priority'    => 'medium',
            'agent_id'    => null,
            'due_date'    => null,
            'resolved_at' => null,
            'user_id'     => $clients->random(),
            'category_id' => $categories->random(),
        ]);

        Ticket::factory()->count(2)->create([
            'status'      => 'open',
            'priority'    => 'urgent',
            'agent_id'    => null,
            'due_date'    => null,
            'resolved_at' => null,
            'user_id'     => $clients->random(),
            'category_id' => $categories->random(),
        ]);

        foreach (range(1, 3) as $i) {
            $assignedAt = now()->subHours(rand(2, 10));
            Ticket::factory()->create([
                'status'      => 'in_progress',
                'priority'    => collect(['medium', 'high'])->random(),
                'agent_id'    => $agents->random(),
                'due_date'    => $assignedAt->copy()->addHours(24),
                'resolved_at' => null,
                'user_id'     => $clients->random(),
                'category_id' => $categories->random(),
                'created_at'  => $assignedAt->copy()->subHours(1),
            ]);
        }

        foreach (range(1, 4) as $i) {
            $createdAt  = now()->subDays(rand(3, 10));
            $assignedAt = $createdAt->copy()->addHours(rand(1, 3));
            $resolvedAt = $assignedAt->copy()->addHours(rand(4, 20));

            Ticket::factory()->create([
                'status'      => 'resolved',
                'priority'    => collect(['medium', 'high'])->random(),
                'agent_id'    => $agents->random(),
                'due_date'    => $assignedAt->copy()->addHours(24),
                'resolved_at' => $resolvedAt,
                'user_id'     => $clients->random(),
                'category_id' => $categories->random(),
                'created_at'  => $createdAt,
                'updated_at'  => $resolvedAt,
            ]);
        }

        foreach (range(1, 2) as $i) {
            $createdAt  = now()->subDays(rand(5, 15));
            $assignedAt = $createdAt->copy()->addHours(2);
            $resolvedAt = $assignedAt->copy()->addHours(rand(30, 72));

            Ticket::factory()->create([
                'status'      => 'resolved',
                'priority'    => 'urgent',
                'agent_id'    => $agents->random(),
                'due_date'    => $assignedAt->copy()->addHours(4),
                'resolved_at' => $resolvedAt,
                'user_id'     => $clients->random(),
                'category_id' => $categories->random(),
                'created_at'  => $createdAt,
                'updated_at'  => $resolvedAt,
            ]);
        }

        Ticket::factory()->count(2)->create([
            'status'      => 'closed',
            'priority'    => 'low',
            'agent_id'    => $agents->random(),
            'due_date'    => now()->subDays(2),
            'resolved_at' => now()->subDays(1),
            'user_id'     => $clients->random(),
            'category_id' => $categories->random(),
        ]);
    }
}
