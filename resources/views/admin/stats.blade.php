@extends('layouts.app')

@section('content')

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
    // Badge helpers — usando los colores custom del tailwind.config.js
    $priorityMeta = [
        'urgent' => ['label' => 'Crítico', 'badge' => 'bg-synapso-priority-urgent-bg text-synapso-priority-urgent-text'],
        'high'   => ['label' => 'Alta',    'badge' => 'bg-synapso-priority-high-bg text-synapso-priority-high-text'],
        'medium' => ['label' => 'Media',   'badge' => 'bg-synapso-priority-mid-bg text-synapso-priority-mid-text'],
        'low'    => ['label' => 'Baja',    'badge' => 'bg-synapso-priority-low-bg text-synapso-priority-low-text'],
    ];
@endphp

{{-- ═══════════════════════════════════════════════════════════
     MODAL DE EXPORTACIÓN — Alpine.js
     ═══════════════════════════════════════════════════════════ --}}
<div x-data="{ exportOpen: false }" class="min-h-screen bg-synapso-bg py-8">

    {{-- Overlay + Modal --}}
    <div x-show="exportOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display:none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
             @click="exportOpen = false"></div>

        {{-- Panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-800 tracking-tight">Generar Reporte</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Selecciona las secciones a incluir</p>
                </div>
                <button @click="exportOpen = false"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.stats.export') }}" target="_blank">
                @csrf
                <div class="px-6 py-5 space-y-3">

                    @php
                        $exportSections = [
                            'kpis'       => ['label' => 'KPIs generales',            'desc' => 'Total tickets, tiempo resolución, SLA, vencidos'],
                            'categories' => ['label' => 'Tickets por categoría',     'desc' => 'Desglose por estado en cada categoría'],
                            'overdue'    => ['label' => 'Tickets vencidos',          'desc' => 'Listado de tickets fuera de SLA'],
                            'agents'     => ['label' => 'Rendimiento de agentes',    'desc' => 'Top agentes por tickets resueltos'],
                            'users'      => ['label' => 'Directorio de usuarios',    'desc' => 'Lista de usuarios (sin contraseñas)'],
                        ];
                    @endphp

                    @foreach($exportSections as $key => $section)
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-indigo-200
                                      hover:bg-indigo-50/30 cursor-pointer transition-all duration-150 group">
                            <input type="checkbox" name="sections[]" value="{{ $key }}"
                                   checked
                                   class="mt-0.5 w-4 h-4 rounded text-indigo-600 border-slate-300
                                          focus:ring-indigo-500 cursor-pointer flex-shrink-0">
                            <div>
                                <p class="text-sm font-semibold text-slate-800 group-hover:text-indigo-700 transition">
                                    {{ $section['label'] }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $section['desc'] }}</p>
                            </div>
                        </label>
                    @endforeach

                </div>

                <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between gap-3 bg-slate-50 rounded-b-2xl">
                    <p class="text-[10px] text-slate-400 font-medium leading-relaxed">
                        Se abrirá en una nueva pestaña.<br>Usa <strong>Ctrl+P → Guardar como PDF</strong>.
                    </p>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="exportOpen = false"
                                class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800
                                       border border-slate-200 bg-white hover:bg-slate-50 rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-synapso-gold hover:bg-synapso-amber
                                       text-white text-sm font-bold rounded-lg shadow-sm transition-all duration-150
                                       hover:-translate-y-px active:translate-y-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Generar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
         CONTENIDO PRINCIPAL
         ════════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Encabezado --}}
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

                {{-- Botón que abre el modal de exportación --}}
                <button type="button" @click="exportOpen = true"
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

        {{-- ── KPI Cards ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

            <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between h-32">
                <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-indigo-50/60 pointer-events-none"></div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tickets</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1.5 tracking-tight">{{ number_format($totalTickets) }}</p>
                </div>
                <div class="flex items-center gap-3 text-[10px] font-semibold text-slate-400">
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>{{ $openCount }} abiertos</span>
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>{{ $resolvedCount }} resueltos</span>
                </div>
            </div>

            <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between h-32">
                <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-amber-50/60 pointer-events-none"></div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tiempo Prom. Resolución</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1.5 tracking-tight">
                        {{ $avgResolutionTime ?? '—' }}
                        @if($avgResolutionTime)<span class="text-xl font-bold text-slate-500">hrs</span>@endif
                    </p>
                </div>
                <p class="text-[10px] font-semibold text-slate-400">Desde creación hasta resolución</p>
            </div>

            <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between h-32
                        {{ $overdueTickets->count() > 0 ? 'border-l-4 !border-l-red-400' : '' }}">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tickets Vencidos</p>
                    <p class="text-3xl font-extrabold {{ $overdueTickets->count() > 0 ? 'text-red-600' : 'text-slate-800' }} mt-1.5 tracking-tight">
                        {{ $overdueTickets->count() }}
                    </p>
                </div>
                @if($overdueTickets->count() > 0)
                    <p class="text-[10px] font-bold text-red-500 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                        Requiere atención inmediata
                    </p>
                @else
                    <p class="text-[10px] font-semibold text-emerald-500">✓ Todos dentro del SLA</p>
                @endif
            </div>

            <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col justify-between h-32">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cumplimiento SLA</p>
                    <p class="text-3xl font-extrabold mt-1.5 tracking-tight
                              {{ $slaComplianceRate >= 80 ? 'text-emerald-600' : ($slaComplianceRate >= 60 ? 'text-amber-600' : 'text-red-600') }}">
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

        {{-- ── Tickets por Categoría + Tickets Vencidos ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Tickets por Categoría --}}
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Tickets por Categoría</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Desglose por estado dentro de cada categoría</p>
                </div>
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
                                    $openW = $cat->tickets_count > 0 ? round(($cat->open_count        / $cat->tickets_count) * 100) : 0;
                                    $inPW  = $cat->tickets_count > 0 ? round(($cat->in_progress_count / $cat->tickets_count) * 100) : 0;
                                    $resW  = $cat->tickets_count > 0 ? round(($cat->resolved_count    / $cat->tickets_count) * 100) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50 transition duration-100">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-slate-800">{{ $cat->name }}</p>
                                    </td>
                                    <td class="px-6 py-4 min-w-[140px]">
                                        <div class="flex h-2 rounded-full overflow-hidden bg-slate-100">
                                            @if($openW > 0)<div class="bg-blue-400 h-full" style="width:{{ $openW }}%"></div>@endif
                                            @if($inPW  > 0)<div class="bg-amber-400 h-full" style="width:{{ $inPW }}%"></div>@endif
                                            @if($resW  > 0)<div class="bg-emerald-400 h-full" style="width:{{ $resW }}%"></div>@endif
                                        </div>
                                        <div class="flex gap-3 mt-1.5 text-[9px] font-semibold text-slate-400">
                                            @if($cat->open_count > 0)
                                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>{{ $cat->open_count }}</span>
                                            @endif
                                            @if($cat->in_progress_count > 0)
                                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>{{ $cat->in_progress_count }}</span>
                                            @endif
                                            @if($cat->resolved_count > 0)
                                                <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>{{ $cat->resolved_count }}</span>
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

            {{-- Tickets Vencidos --}}
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 tracking-tight">Tickets Vencidos</h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            <code class="bg-slate-100 px-1 rounded text-[10px] font-mono">now() &gt; due_date</code> y estado activo
                        </p>
                    </div>
                    @if($overdueTickets->count() > 0)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                            {{ $overdueTickets->count() }} activos
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                            ✓ Sin vencimientos
                        </span>
                    @endif
                </div>

                @if($overdueTickets->isEmpty())
                    <div class="py-12 text-center">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-slate-400 text-sm font-medium">Todos los tickets están dentro del SLA.</p>
                    </div>
                @else
                    <div class="overflow-y-auto max-h-80">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50 sticky top-0">
                                <tr>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ticket</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden sm:table-cell">Agente</th>
                                    <th class="px-5 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Vencido hace</th>
                                    <th class="px-5 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($overdueTickets as $ticket)
                                    @php
                                        $pb        = $priorityMeta[$ticket->priority] ?? $priorityMeta['low'];
                                        $hoursOver = (int) abs(now()->diffInHours($ticket->due_date));
                                        $overLabel = $hoursOver >= 24 ? round($hoursOver / 24, 1).'d' : $hoursOver.'h';
                                        $urgency   = $hoursOver >= 72
                                            ? 'text-red-600 font-extrabold'
                                            : ($hoursOver >= 24 ? 'text-orange-500 font-bold' : 'text-amber-500 font-bold');
                                    @endphp
                                    <tr class="hover:bg-red-50/30 transition duration-100">
                                        <td class="px-5 py-3">
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                               class="text-sm font-semibold text-slate-800 hover:text-indigo-600 transition line-clamp-1">
                                                {{ $ticket->title }}
                                            </a>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[9px] font-bold text-slate-400">#{{ sprintf('%04d', $ticket->id) }}</span>
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold tracking-wider {{ $pb['badge'] }}">
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
                                               class="p-1.5 inline-flex rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors duration-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
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
    </div>
</div>

@endif
@endsection