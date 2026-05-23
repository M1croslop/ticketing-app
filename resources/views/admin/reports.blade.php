@extends('layouts.app')

@section('content')
{{-- ── Guard: solo admin ──────────────────────────────────────────────────── --}}
@if(auth()->user()->role !== 'admin')
    <div class="min-h-screen flex items-center justify-center bg-synapso-bg">
        <div class="text-center">
            <p class="text-6xl font-extrabold text-slate-200">403</p>
            <p class="text-slate-500 mt-2 font-medium">Acceso restringido a administradores.</p>
            <a href="{{ route('dashboard') }}" class="mt-4 inline-block text-indigo-600 text-sm font-bold hover:underline">
                Volver al Dashboard
            </a>
        </div>
    </div>
@else

@php
    $priorityMeta = [
        'urgent' => ['label' => 'Crítico', 'bar' => 'bg-red-500',    'text' => 'text-red-600',    'bg' => 'bg-red-50 border border-red-200'],
        'high'   => ['label' => 'Alta',    'bar' => 'bg-orange-400',  'text' => 'text-orange-600', 'bg' => 'bg-orange-50 border border-orange-200'],
        'medium' => ['label' => 'Media',   'bar' => 'bg-amber-400',   'text' => 'text-amber-600',  'bg' => 'bg-amber-50 border border-amber-200'],
        'low'    => ['label' => 'Baja',    'bar' => 'bg-blue-400',    'text' => 'text-blue-600',   'bg' => 'bg-blue-50 border border-blue-200'],
    ];
    $statusMeta = [
        'open'        => ['label' => 'Abierto',     'classes' => 'bg-blue-100 text-blue-700'],
        'in_progress' => ['label' => 'En Progreso', 'classes' => 'bg-amber-100 text-amber-800'],
        'resolved'    => ['label' => 'Resuelto',    'classes' => 'bg-emerald-100 text-emerald-800'],
        'closed'      => ['label' => 'Cerrado',     'classes' => 'bg-slate-100 text-slate-700'],
    ];

    $weekTrend = $createdLastWeek > 0
        ? round((($createdThisWeek - $createdLastWeek) / $createdLastWeek) * 100)
        : 0;
@endphp

<div class="min-h-screen bg-synapso-bg py-8">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- ── Encabezado ───────────────────────────────────────────────────── --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold text-synapso-gold uppercase tracking-widest mb-1">Panel de Administración</p>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Reportes y Métricas</h1>
            <p class="mt-1.5 text-sm text-slate-500 font-medium">
                Métricas operacionales en tiempo real del sistema de ticketing.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users') }}"
               class="inline-flex items-center gap-2 border border-slate-200 bg-white hover:bg-slate-50
                      text-slate-700 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition duration-150">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Gestión de Usuarios
            </a>
            <button type="button"
                    class="inline-flex items-center gap-2 border border-slate-200 bg-white hover:bg-slate-50
                           text-slate-700 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition duration-150">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar
            </button>
        </div>
    </div>

    {{-- ── Fila 1: KPI Cards ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- Total Tickets --}}
        <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between h-32">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-indigo-50/50 pointer-events-none"></div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tickets</p>
                <p class="text-3xl font-extrabold text-slate-800 mt-1.5 tracking-tight">{{ number_format($totalTickets) }}</p>
            </div>
            <div class="flex items-center gap-1.5 text-[10px] font-bold">
                @if($weekTrend > 0)
                    <span class="text-red-500">▲ +{{ $weekTrend }}% esta semana</span>
                @elseif($weekTrend < 0)
                    <span class="text-emerald-500">▼ {{ $weekTrend }}% esta semana</span>
                @else
                    <span class="text-slate-400">— Sin cambios esta semana</span>
                @endif
            </div>
        </div>

        {{-- Tiempo Promedio Resolución --}}
        <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between h-32">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-amber-50/50 pointer-events-none"></div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tiempo Prom. Resolución</p>
                <p class="text-3xl font-extrabold text-slate-800 mt-1.5 tracking-tight">
                    {{ $avgResolutionTime ?? '—' }}
                    @if($avgResolutionTime)
                        <span class="text-xl font-bold text-slate-500">hrs</span>
                    @endif
                </p>
            </div>
            <p class="text-[10px] font-semibold text-slate-400">Basado en tickets resueltos</p>
        </div>

        {{-- Tickets Vencidos --}}
        <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between h-32
                    {{ $overdueTickets->count() > 0 ? 'border-l-4 border-l-red-400' : '' }}">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tickets Vencidos</p>
                <p class="text-3xl font-extrabold {{ $overdueTickets->count() > 0 ? 'text-red-600' : 'text-slate-800' }} mt-1.5 tracking-tight">
                    {{ $overdueTickets->count() }}
                </p>
            </div>
            @if($overdueTickets->count() > 0)
                <p class="text-[10px] font-bold text-red-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    Requiere atención inmediata
                </p>
            @else
                <p class="text-[10px] font-semibold text-emerald-500">✓ Sin vencimientos activos</p>
            @endif
        </div>

        {{-- Cumplimiento SLA --}}
        <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between h-32">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cumplimiento SLA</p>
                <p class="text-3xl font-extrabold {{ $slaComplianceRate >= 80 ? 'text-emerald-600' : ($slaComplianceRate >= 60 ? 'text-amber-600' : 'text-red-600') }} mt-1.5 tracking-tight">
                    {{ $slaComplianceRate }}<span class="text-xl font-bold">%</span>
                </p>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700
                            {{ $slaComplianceRate >= 80 ? 'bg-emerald-500' : ($slaComplianceRate >= 60 ? 'bg-amber-400' : 'bg-red-500') }}"
                     style="width: {{ $slaComplianceRate }}%"></div>
            </div>
        </div>

    </div>

    {{-- ── Fila 2: Volumen semanal + Distribución por estado ────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Sparkline: tickets por día (últimos 7 días) --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Volumen de Tickets — Últimos 7 Días</h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ $createdThisWeek }} creados esta semana
                        @if($weekTrend !== 0)
                            <span class="{{ $weekTrend > 0 ? 'text-red-500' : 'text-emerald-500' }} font-bold">
                                ({{ $weekTrend > 0 ? '+' : '' }}{{ $weekTrend }}% vs semana anterior)
                            </span>
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-400">Resueltos esta semana</p>
                    <p class="text-xl font-extrabold text-emerald-600">{{ $resolvedThisWeek }}</p>
                </div>
            </div>

            {{-- Barras CSS inline --}}
            <div class="flex items-end gap-2 h-28">
                @foreach($last7Days as $day)
                    @php
                        $heightPct = $maxDayCount > 0 ? ($day['count'] / $maxDayCount) * 100 : 0;
                        $isToday   = $day['date'] === now()->format('d M');
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1.5">
                        <span class="text-[9px] font-bold {{ $day['count'] > 0 ? 'text-slate-600' : 'text-slate-300' }}">
                            {{ $day['count'] ?: '' }}
                        </span>
                        <div class="w-full rounded-t-md transition-all duration-500
                                    {{ $isToday ? 'bg-synapso-gold' : 'bg-indigo-200 hover:bg-indigo-400' }}"
                             style="height: {{ max(4, $heightPct) }}%; min-height: 4px; max-height: 100%;">
                        </div>
                        <span class="text-[9px] font-semibold {{ $isToday ? 'text-synapso-gold' : 'text-slate-400' }} whitespace-nowrap">
                            {{ $day['date'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Distribución por estado --}}
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
            <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-5">Estado Actual</h2>
            @php
                $stateData = [
                    ['label' => 'Abiertos',     'count' => $openCount,       'bar' => 'bg-blue-500',    'text' => 'text-blue-600'],
                    ['label' => 'En Progreso',  'count' => $inProgressCount, 'bar' => 'bg-amber-400',   'text' => 'text-amber-600'],
                    ['label' => 'Resueltos',    'count' => $resolvedCount,   'bar' => 'bg-emerald-500', 'text' => 'text-emerald-600'],
                    ['label' => 'Vencidos',     'count' => $overdueTickets->count(), 'bar' => 'bg-red-500', 'text' => 'text-red-600'],
                ];
                $stateMax = max(array_column($stateData, 'count')) ?: 1;
            @endphp
            <div class="space-y-4">
                @foreach($stateData as $s)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-slate-600">{{ $s['label'] }}</span>
                            <span class="text-xs font-extrabold {{ $s['text'] }}">{{ number_format($s['count']) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="{{ $s['bar'] }} h-full rounded-full transition-all duration-700"
                                 style="width: {{ $stateMax > 0 ? round(($s['count'] / $stateMax) * 100) : 0 }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Distribución por prioridad activos --}}
            <div class="mt-6 pt-5 border-t border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Activos por Prioridad</p>
                <div class="flex items-end gap-2">
                    @foreach(['urgent','high','medium','low'] as $p)
                        @php
                            $cnt  = $byPriority[$p] ?? 0;
                            $meta = $priorityMeta[$p];
                            $maxP = max($byPriority->toArray() ?: [1]);
                            $h    = $maxP > 0 ? max(8, round(($cnt / $maxP) * 48)) : 8;
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <span class="text-[9px] font-bold {{ $cnt > 0 ? $meta['text'] : 'text-slate-300' }}">{{ $cnt }}</span>
                            <div class="{{ $meta['bar'] }} w-full rounded-t" style="height: {{ $h }}px; opacity: {{ $cnt > 0 ? '1' : '0.2' }};"></div>
                            <span class="text-[9px] text-slate-400 font-semibold">{{ strtoupper(substr($meta['label'], 0, 3)) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- ── Fila 3: Tickets por Categoría + Tickets Vencidos ──────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- ── Tickets por Categoría (requerido) ───────────────────────── --}}
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800 tracking-tight">Tickets por Categoría</h2>
                <p class="text-xs text-slate-400 mt-0.5">Desglose por estado dentro de cada categoría</p>
            </div>
            <div class="overflow-x-auto">
                @if($ticketsByCategory->isEmpty())
                    <div class="py-10 text-center text-slate-400 text-sm">Sin categorías registradas.</div>
                @else
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Distribución</th>
                                <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @foreach($ticketsByCategory as $cat)
                                @php
                                    $barWidth = $maxCategoryCount > 0
                                        ? round(($cat->tickets_count / $maxCategoryCount) * 100)
                                        : 0;
                                    $openW  = $cat->tickets_count > 0 ? round(($cat->open_count / $cat->tickets_count) * 100) : 0;
                                    $inPW   = $cat->tickets_count > 0 ? round(($cat->in_progress_count / $cat->tickets_count) * 100) : 0;
                                    $resW   = $cat->tickets_count > 0 ? round(($cat->resolved_count / $cat->tickets_count) * 100) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50 transition duration-100">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-slate-800">{{ $cat->name }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{-- Barra segmentada por estado --}}
                                        <div class="flex h-2 rounded-full overflow-hidden bg-slate-100 w-full min-w-[120px]">
                                            @if($openW > 0)
                                                <div class="bg-blue-400 h-full" style="width: {{ $openW }}%"
                                                     title="Abiertos: {{ $cat->open_count }}"></div>
                                            @endif
                                            @if($inPW > 0)
                                                <div class="bg-amber-400 h-full" style="width: {{ $inPW }}%"
                                                     title="En Progreso: {{ $cat->in_progress_count }}"></div>
                                            @endif
                                            @if($resW > 0)
                                                <div class="bg-emerald-400 h-full" style="width: {{ $resW }}%"
                                                     title="Resueltos: {{ $cat->resolved_count }}"></div>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 mt-1.5 text-[9px] font-semibold text-slate-400">
                                            @if($cat->open_count > 0)
                                                <span class="flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>{{ $cat->open_count }}
                                                </span>
                                            @endif
                                            @if($cat->in_progress_count > 0)
                                                <span class="flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>{{ $cat->in_progress_count }}
                                                </span>
                                            @endif
                                            @if($cat->resolved_count > 0)
                                                <span class="flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>{{ $cat->resolved_count }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-extrabold text-slate-800">{{ $cat->tickets_count }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- ── Tickets Vencidos (requerido) ────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Tickets Vencidos</h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <code class="bg-slate-100 px-1 rounded text-[10px] font-mono">now() &gt; due_date</code>
                        y estado activo
                    </p>
                </div>
                @if($overdueTickets->count() > 0)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                                 bg-red-50 text-red-600 border border-red-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                        {{ $overdueTickets->count() }} activos
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                                 bg-emerald-50 text-emerald-600 border border-emerald-200">
                        ✓ Sin vencimientos
                    </span>
                @endif
            </div>

            @if($overdueTickets->isEmpty())
                <div class="py-12 text-center">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-slate-400 text-sm font-medium">Todos los tickets están dentro del SLA.</p>
                </div>
            @else
                <div class="overflow-y-auto max-h-96">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50 sticky top-0">
                            <tr>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ticket</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden sm:table-cell">Agente</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Vencido hace</th>
                                <th class="px-5 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @foreach($overdueTickets as $ticket)
                                @php
                                    $pb        = $priorityMeta[$ticket->priority] ?? $priorityMeta['low'];
                                    $hoursOver = abs(now()->diffInHours($ticket->due_date));
                                    $overLabel = $hoursOver >= 24
                                        ? round($hoursOver / 24, 1).'d'
                                        : $hoursOver.'h';
                                    $urgency   = $hoursOver >= 72 ? 'text-red-600 font-extrabold' : ($hoursOver >= 24 ? 'text-orange-500 font-bold' : 'text-amber-500 font-bold');
                                @endphp
                                <tr class="hover:bg-red-50/30 transition duration-100">
                                    <td class="px-5 py-3">
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                           class="text-sm font-semibold text-slate-800 hover:text-indigo-600 transition line-clamp-1">
                                            {{ $ticket->title }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[9px] font-bold text-slate-400">#{{ sprintf('%04d', $ticket->id) }}</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold tracking-wider {{ $pb['bg'] }} {{ $pb['text'] }}">
                                                {{ strtoupper($pb['label']) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 hidden sm:table-cell">
                                        @if($ticket->agent)
                                            <div class="flex items-center gap-1.5">
                                                <div class="w-5 h-5 rounded-full bg-synapso-gold flex items-center justify-center text-white text-[9px] font-bold">
                                                    {{ strtoupper(substr($ticket->agent->name, 0, 2)) }}
                                                </div>
                                                <span class="text-xs text-slate-600 font-medium">{{ $ticket->agent->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <span class="text-sm {{ $urgency }}">{{ $overLabel }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                           class="p-1.5 inline-flex rounded-lg text-slate-400 hover:text-indigo-600
                                                  hover:bg-indigo-50 transition-colors duration-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                      d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    {{-- ── Fila 4: Top Agentes ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800 tracking-tight">Rendimiento de Agentes</h2>
            <p class="text-xs text-slate-400 mt-0.5">Top 5 agentes por tickets resueltos (acumulado)</p>
        </div>
        <div class="overflow-x-auto">
            @if($topAgents->isEmpty())
                <div class="py-10 text-center text-slate-400 text-sm">Sin agentes registrados.</div>
            @else
                @php $maxResolved = $topAgents->max('resolved_count') ?: 1; @endphp
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Agente</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden md:table-cell">Activos</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Resueltos</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Rendimiento</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach($topAgents as $i => $agent)
                            <tr class="hover:bg-slate-50 transition duration-100">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-extrabold {{ $i === 0 ? 'text-synapso-gold' : 'text-slate-400' }}">
                                        #{{ $i + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-synapso-gold flex items-center justify-center
                                                    text-white text-xs font-bold flex-shrink-0
                                                    {{ $i === 0 ? 'ring-2 ring-amber-300 ring-offset-1' : '' }}">
                                            {{ strtoupper(substr($agent->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $agent->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $agent->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm font-bold text-slate-700">{{ $agent->active_count }}</span>
                                        @if($agent->active_count > 0)
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-extrabold text-emerald-600">{{ $agent->resolved_count }}</span>
                                </td>
                                <td class="px-6 py-4 hidden lg:table-cell">
                                    @php
                                        $pct = $maxResolved > 0 ? round(($agent->resolved_count / $maxResolved) * 100) : 0;
                                    @endphp
                                    <div class="flex items-center gap-2 min-w-[140px]">
                                        <div class="flex-1 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                            <div class="{{ $i === 0 ? 'bg-synapso-gold' : 'bg-emerald-400' }} h-full rounded-full transition-all duration-700"
                                                 style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-400 w-8 text-right">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
</div>

@endif
@endsection