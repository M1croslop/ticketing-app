<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $status = request('status');
        $dateRange = request('date_range', 'all');
        $priority = request('priority');
        $orderBy = request('order_by', 'created_at_desc');
        
        // Consulta base aplicando visibilidad según el rol
        // El administrador ve todo, el agente ve solo sus tickets asignados y el cliente ve solo sus tickets creados
        $baseQuery = Ticket::query()
            ->when($user->role === 'client', fn($q) => $q->where('user_id', $user->id))
            ->when($user->role === 'agent', fn($q) => $q->where('agent_id', $user->id));

        // Crear un clon para las métricas y listas filtradas por rango de fecha
        $filteredQuery = clone $baseQuery;

        if ($dateRange === 'today') {
            $filteredQuery->whereDate('created_at', Carbon::today());
        } elseif ($dateRange === 'week') {
            $filteredQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($dateRange === 'month') {
            $filteredQuery->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year);
        }

        // MÉTRICAS INDIVIDUALES POR ESTADO
        $openCount = (clone $filteredQuery)->where('status', 'open')->count();
        $inProgressCount = (clone $filteredQuery)->where('status', 'in_progress')->count();
        $resolvedCount = (clone $filteredQuery)->where('status', 'resolved')->count();
        $closedCount = (clone $filteredQuery)->where('status', 'closed')->count();

        // MÉTRICAS AGRUPADAS Y TOTALES
        $activeCount = $openCount + $inProgressCount;
        $resolvedTotalCount = $resolvedCount + $closedCount;
        $totalTicketsCount = (clone $filteredQuery)->count();

        $resolvedToday = (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])
            ->whereDate('updated_at', Carbon::today())
            ->count();

        $resolvedYesterday = (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])
            ->whereDate('updated_at', Carbon::yesterday())
            ->count();

        $resolvedTrend = $resolvedToday - $resolvedYesterday;

        // Promedio de resolución en horas
        $avgResolutionTime = (clone $filteredQuery)->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        // PORCENTAJES PARA LAS BARRAS DE PROGRESO DE LAS TARJETAS DE ADMINISTRACIÓN
        $openPercentage = $totalTicketsCount > 0 ? round(($openCount / $totalTicketsCount) * 100) : 0;
        $resolvedPercentage = $totalTicketsCount > 0 ? round(($resolvedTotalCount / $totalTicketsCount) * 100) : 0;
        // El objetivo de SLA es 72 horas. La barra de eficiencia se llena más cuando el tiempo de resolución es más rápido.
        $resolutionProgress = $avgResolutionTime > 0 ? min(100, max(15, round((72 / max(1, $avgResolutionTime)) * 100))) : 85;

        // CÁLCULOS DE TENDENCIA DINÁMICA DEL ADMINISTRADOR
        // 1. Tendencia de porcentaje diario del total de tickets abiertos
        $openCountYesterday = (clone $baseQuery)->where('status', 'open')
            ->whereDate('created_at', '<', Carbon::today())
            ->count();
        $openTrendPercent = 0;
        if ($openCountYesterday > 0) {
            $openTrendPercent = round((($openCount - $openCountYesterday) / $openCountYesterday) * 100);
        }

        // 2. Tendencia semanal del tiempo promedio de resolución (Esta semana vs la semana pasada en horas)
        $avgResThisWeek = (clone $baseQuery)->whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        $avgResLastWeek = (clone $baseQuery)->whereNotNull('resolved_at')
            ->whereBetween('resolved_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        $avgResolutionTrend = 0;
        if ($avgResThisWeek !== null && $avgResLastWeek !== null) {
            $avgResolutionTrend = round($avgResThisWeek - $avgResLastWeek, 1);
        }

        // 3. Cantidad de tickets resueltos en el rango activo (reemplaza estrictamente el conteo de este mes)
        $resolvedThisMonthCount = (clone $filteredQuery)->whereIn('status', ['resolved', 'closed'])
            ->count();

        // Tiempo promedio de respuesta basado en la fórmula sugerida por el jefe (due_date - resolved_at, convertido a horas)
        $avgResponseTimeRaw = (clone $filteredQuery)->whereNotNull('resolved_at')
            ->whereNotNull('due_date')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, resolved_at, due_date)) as avg_min')
            ->value('avg_min');

        if ($avgResponseTimeRaw !== null) {
            $avgResponseTime = round(abs($avgResponseTimeRaw) / 60, 1);
        } else {
            $avgResponseTime = null; // Sin valor por defecto estático
        }

        // Tendencia del tiempo promedio de respuesta (Esta semana vs la semana pasada en horas)
        $avgResponseThisWeekRaw = (clone $baseQuery)->whereNotNull('resolved_at')
            ->whereNotNull('due_date')
            ->whereBetween('resolved_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, resolved_at, due_date)) as avg_min')
            ->value('avg_min');

        $avgResponseLastWeekRaw = (clone $baseQuery)->whereNotNull('resolved_at')
            ->whereNotNull('due_date')
            ->whereBetween('resolved_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, resolved_at, due_date)) as avg_min')
            ->value('avg_min');

        $avgResponseTrend = 0;
        if ($avgResponseThisWeekRaw !== null && $avgResponseLastWeekRaw !== null) {
            $thisWeekHours = round(abs($avgResponseThisWeekRaw) / 60, 1);
            $lastWeekHours = round(abs($avgResponseLastWeekRaw) / 60, 1);
            $avgResponseTrend = round($thisWeekHours - $lastWeekHours, 1);
        }

        // ÚLTIMOS TICKETS CON FILTROS APLICADOS (según el rol, estado y rango de fecha)
        $ticketsQuery = (clone $filteredQuery)->with(['user', 'agent', 'category']);

        if ($status && $status !== 'all') {
            $ticketsQuery->where('status', $status);
        }

        $recentTickets = $ticketsQuery->latest()->paginate(5)->withQueryString();

        // Cálculo de infracciones de SLA (Tickets abiertos/en progreso vencidos con respecto a la hora actual)
        $slaBreachesCount = (clone $filteredQuery)
            ->whereIn('status', ['open', 'in_progress'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::now())
            ->count();

        // Cálculo de infracciones de SLA de ayer (Tickets abiertos/en progreso vencidos con respecto al día de ayer)
        $slaBreachesYesterdayCount = (clone $baseQuery)
            ->whereIn('status', ['open', 'in_progress'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::yesterday())
            ->count();

        $slaBreachesTrend = $slaBreachesCount - $slaBreachesYesterdayCount;

        // Agent Kanban Workflow collections
        $newTicketsQuery = (clone $filteredQuery)->where('status', 'open');
        $inProgressTicketsQuery = (clone $filteredQuery)->where('status', 'in_progress');
        $resolvedTicketsQuery = (clone $filteredQuery)->whereIn('status', ['resolved', 'closed']);

        // Aplicar filtro de prioridad
        if ($priority && $priority !== 'all') {
            $newTicketsQuery->where('priority', $priority);
            $inProgressTicketsQuery->where('priority', $priority);
            $resolvedTicketsQuery->where('priority', $priority);
        }

        // Aplicar clasificación / ordenamiento
        if ($orderBy === 'created_at_asc') {
            $newTicketsQuery->oldest();
            $inProgressTicketsQuery->oldest();
            $resolvedTicketsQuery->oldest();
        } elseif ($orderBy === 'due_date_asc') {
            // Colocar los tickets con fecha de vencimiento nula al final
            $newTicketsQuery->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC');
            $inProgressTicketsQuery->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC');
            $resolvedTicketsQuery->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC');
        } else {
            // Por defecto: los más nuevos primero
            $newTicketsQuery->latest();
            $inProgressTicketsQuery->latest();
            $resolvedTicketsQuery->latest();
        }

        $agentNewTickets = $newTicketsQuery->with(['user', 'category'])->get();
        $agentInProgressTickets = $inProgressTicketsQuery->with(['user', 'category'])->get();
        $agentResolvedTickets = $resolvedTicketsQuery->with(['user', 'category'])->get();

        // RETORNO
        return view('dashboard', compact(
            'openCount',
            'inProgressCount',
            'resolvedCount',
            'closedCount',
            'activeCount',
            'resolvedTotalCount',
            'totalTicketsCount',
            'openPercentage',
            'resolvedPercentage',
            'resolutionProgress',
            'openTrendPercent',
            'avgResolutionTrend',
            'resolvedThisMonthCount',
            'resolvedToday',
            'resolvedTrend',
            'avgResolutionTime',
            'avgResponseTime',
            'avgResponseTrend',
            'recentTickets',
            'slaBreachesCount',
            'slaBreachesTrend',
            'agentNewTickets',
            'agentInProgressTickets',
            'agentResolvedTickets',
            'dateRange',
            'status',
            'priority',
            'orderBy'
        ));
    }
}