<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        //MÉTRICAS

        $openCount = Ticket::where('status', 'open')->count();

        $resolvedToday = Ticket::where('status', 'resolved')
            ->whereDate('updated_at', Carbon::today())
            ->count();

        // Promedio de resolución en horas
        $avgResolutionTime = Ticket::whereNotNull('resolved_at')
            ->get()
            ->avg(function ($ticket) {
                return $ticket->created_at->diffInHours($ticket->resolved_at);
            });

        //KANBAN

        $newTickets = Ticket::with('user')
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        $inProgressTickets = Ticket::with('agent')
            ->where('status', 'in_progress')
            ->latest()
            ->take(5)
            ->get();

        $resolvedTickets = Ticket::with('user')
            ->where('status', 'resolved')
            ->latest()
            ->take(5)
            ->get();

        $closedTickets = Ticket::with('user')
        ->where('status', 'closed')
        ->latest()
        ->take(5)
        ->get();
        //RETORNO

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