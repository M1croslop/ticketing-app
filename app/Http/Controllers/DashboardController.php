<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Base query applying role-based visibility
        $baseQuery = Ticket::query()->when($user->role === 'client', fn($q) => $q->where('user_id', $user->id));

        // MÉTRICAS
        $openCount = (clone $baseQuery)->where('status', 'open')->count();

        $resolvedToday = (clone $baseQuery)->where('status', 'resolved')
            ->whereDate('updated_at', Carbon::today())
            ->count();

        // Promedio de resolución en horas
        $avgResolutionTime = (clone $baseQuery)->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        // KANBAN
        $newTickets = (clone $baseQuery)->with('user')
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        $inProgressTickets = (clone $baseQuery)->with('agent')
            ->where('status', 'in_progress')
            ->latest()
            ->take(5)
            ->get();

        $resolvedTickets = (clone $baseQuery)->with('user')
            ->where('status', 'resolved')
            ->latest()
            ->take(5)
            ->get();

        $closedTickets = (clone $baseQuery)->with('user')
            ->where('status', 'closed')
            ->latest()
            ->take(5)
            ->get();

        // RETORNO
        return view('dashboard', compact(
            'openCount',
            'resolvedToday',
            'avgResolutionTime',
            'newTickets',
            'inProgressTickets',
            'resolvedTickets',
            'closedTickets',
        ));
    }
}