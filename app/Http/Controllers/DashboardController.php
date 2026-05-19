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
        // Admin sees all, agent sees only their assigned tickets, client sees only their created tickets
        $baseQuery = Ticket::query()
            ->when($user->role === 'client', fn($q) => $q->where('user_id', $user->id))
            ->when($user->role === 'agent', fn($q) => $q->where('agent_id', $user->id));

        // MÉTRICAS INDIVIDUALES POR ESTADO
        $openCount = (clone $baseQuery)->where('status', 'open')->count();
        $inProgressCount = (clone $baseQuery)->where('status', 'in_progress')->count();
        $resolvedCount = (clone $baseQuery)->where('status', 'resolved')->count();
        $closedCount = (clone $baseQuery)->where('status', 'closed')->count();

        // MÉTRICAS AGRUPADAS Y TOTALES
        $activeCount = $openCount + $inProgressCount;
        $resolvedTotalCount = $resolvedCount + $closedCount;
        $totalTicketsCount = (clone $baseQuery)->count();

        $resolvedToday = (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])
            ->whereDate('updated_at', Carbon::today())
            ->count();

        $resolvedYesterday = (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])
            ->whereDate('updated_at', Carbon::yesterday())
            ->count();

        $resolvedTrend = $resolvedToday - $resolvedYesterday;

        // Promedio de resolución en horas
        $avgResolutionTime = (clone $baseQuery)->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        // PERCENTAGES FOR ADMIN CARD PROGRESS BARS
        $openPercentage = $totalTicketsCount > 0 ? round(($openCount / $totalTicketsCount) * 100) : 0;
        $resolvedPercentage = $totalTicketsCount > 0 ? round(($resolvedTotalCount / $totalTicketsCount) * 100) : 0;
        // SLA target is 72 hours. Efficiency bar fills higher when resolution time is faster.
        $resolutionProgress = $avgResolutionTime > 0 ? min(100, max(15, round((72 / max(1, $avgResolutionTime)) * 100))) : 85;

        // ADMIN DYNAMIC TREND CALCULATIONS
        // 1. Total Open Tickets Daily Percentage Trend
        $openCountYesterday = (clone $baseQuery)->where('status', 'open')
            ->whereDate('created_at', '<', Carbon::today())
            ->count();
        $openTrendPercent = 0;
        if ($openCountYesterday > 0) {
            $openTrendPercent = round((($openCount - $openCountYesterday) / $openCountYesterday) * 100);
        }

        // 2. Avg. Resolution Time Weekly Trend (This Week vs Last Week in hours)
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

        // 3. Resolved Tickets Count this month (strictly within current month)
        $resolvedThisMonthCount = (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // Average Response Time based on boss's suggested formula (due_date - resolved_at, converted to hours)
        $avgResponseTimeRaw = (clone $baseQuery)->whereNotNull('resolved_at')
            ->whereNotNull('due_date')
            ->whereMonth('resolved_at', now()->month)
            ->whereYear('resolved_at', now()->year)
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, resolved_at, due_date)) as avg_min')
            ->value('avg_min');

        if ($avgResponseTimeRaw !== null) {
            $avgResponseTime = round(abs($avgResponseTimeRaw) / 60, 1);
        } else {
            $avgResponseTime = 4.2; // Default mockup fallback in hours
        }

        // Average Response Time trend (This Week vs Last Week in hours)
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

        // ÚLTIMOS 5 TICKETS (según el rol)
        $recentTickets = (clone $baseQuery)->with(['user', 'agent', 'category'])
            ->latest()
            ->take(5)
            ->get();

        // SLA Breaches calculation (Overdue open/in-progress tickets relative to now)
        $slaBreachesCount = (clone $baseQuery)
            ->whereIn('status', ['open', 'in_progress'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::now())
            ->count();

        // SLA Breaches Yesterday calculation (Overdue open/in-progress tickets relative to yesterday)
        $slaBreachesYesterdayCount = (clone $baseQuery)
            ->whereIn('status', ['open', 'in_progress'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::yesterday())
            ->count();

        $slaBreachesTrend = $slaBreachesCount - $slaBreachesYesterdayCount;

        // Agent Kanban Workflow collections
        $agentNewTickets = (clone $baseQuery)->where('status', 'open')
            ->with(['user', 'category'])
            ->latest()
            ->get();
        $agentInProgressTickets = (clone $baseQuery)->where('status', 'in_progress')
            ->with(['user', 'category'])
            ->latest()
            ->get();
        $agentResolvedTickets = (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])
            ->with(['user', 'category'])
            ->latest()
            ->get();

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
            'agentResolvedTickets'
        ));
    }
}