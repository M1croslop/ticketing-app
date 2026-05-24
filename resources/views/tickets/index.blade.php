@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();

    // ── Badge helpers (consistentes con dashboard.blade.php) ──────────────
    $priorityBadge = [
        'urgent' => ['label' => 'CRÍTICO', 'classes' => 'bg-red-50 text-red-600 border border-red-200'],
        'high'   => ['label' => 'ALTA',    'classes' => 'bg-red-50 text-orange-600 border border-orange-200'],
        'medium' => ['label' => 'MEDIA',   'classes' => 'bg-amber-50 text-amber-600 border border-amber-200'],
        'low'    => ['label' => 'BAJA',    'classes' => 'bg-blue-50 text-blue-600 border border-blue-200'],
    ];
    $statusBadge = [
        'open'        => ['label' => 'Abierto',     'classes' => 'bg-blue-100 text-blue-700'],
        'in_progress' => ['label' => 'En Progreso', 'classes' => 'bg-amber-100 text-amber-800'],
        'resolved'    => ['label' => 'Resuelto',    'classes' => 'bg-emerald-100 text-emerald-800'],
        'closed'      => ['label' => 'Cerrado',     'classes' => 'bg-slate-100 text-slate-700'],
    ];
@endphp

<div class="min-h-screen bg-synapso-bg py-8">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">


{{-- ═══════════════════════════════════════════════════════════════════════
     LAYOUT: CLIENTE
     ═══════════════════════════════════════════════════════════════════════ --}}
@if($user->role === 'client')

    {{-- Encabezado --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold text-synapso-gold uppercase tracking-widest mb-1">Portal del Cliente</p>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Tus Tickets de Soporte</h1>
            <p class="mt-1.5 text-sm text-slate-500 font-medium">Gestiona y hace seguimiento de tus solicitudes activas.</p>
        </div>
        <a href="{{ route('tickets.create') }}"
           class="inline-flex items-center gap-2 bg-synapso-gold hover:bg-synapso-amber text-white
                  px-5 py-2.5 rounded-lg font-semibold text-sm shadow-md transition-all duration-200
                  hover:shadow-lg hover:-translate-y-px active:translate-y-0 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Ticket
        </a>
    </div>

    {{-- Filtros cliente --}}
    <form id="filter-form" method="GET" action="{{ route('tickets.index') }}"
          class="mb-5 flex flex-wrap gap-2 items-center">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input id="search-input" type="text" name="search" value="{{ $search }}"
                   placeholder="Buscar ticket…" autocomplete="off"
                   class="pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-700
                          bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300
                          transition w-52 placeholder-slate-400">
        </div>
        <select name="status" id="status-select" onchange="doSearch()"
                class="border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 bg-white
                       focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
            <option value="">Todos los estados</option>
            <option value="open"        {{ $status === 'open'        ? 'selected' : '' }}>Abierto</option>
            <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>En Progreso</option>
            <option value="resolved"    {{ $status === 'resolved'    ? 'selected' : '' }}>Resuelto</option>
            <option value="closed"      {{ $status === 'closed'      ? 'selected' : '' }}>Cerrado</option>
        </select>
        @if($search || $status || $categoryId)
            <a href="{{ route('tickets.index') }}"
               class="px-3 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-500
                      rounded-lg text-sm font-medium transition">
                Limpiar
            </a>
        @endif
    </form>

    {{-- Tabla cliente --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 tracking-tight">Solicitudes Activas</h2>
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h6"/>
                </svg>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($tickets->isEmpty())
                <div class="py-12 text-center text-slate-400 text-sm">Sin tickets registrados.</div>
            @else
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Título</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden sm:table-cell">Fecha de Creación</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tickets-tbody" class="bg-white divide-y divide-slate-100">
                        @foreach($tickets as $ticket)
                            @php
                                $sb = $statusBadge[$ticket->status] ?? ['label' => $ticket->status, 'classes' => 'bg-slate-100 text-slate-700'];
                                $canEdit = in_array($ticket->status, ['open']);
                            @endphp
                            <tr class="hover:bg-slate-50 transition duration-100">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-slate-800">{{ $ticket->title }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">ID: #TKT-{{ sprintf('%04d', $ticket->id) }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sb['classes'] }}">
                                        {{ $sb['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 hidden sm:table-cell">
                                    {{ $ticket->created_at->format('d M Y, g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($canEdit)
                                        <a href="{{ route('tickets.edit', $ticket) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                                  text-xs font-bold text-synapso-gold hover:bg-amber-50
                                                  border border-amber-200 transition-colors duration-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                                     text-xs font-bold text-slate-300 border border-slate-100 cursor-not-allowed">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            Bloqueado
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        {{-- Paginación --}}
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-400">
                @if($tickets->total() > 0)
                    Mostrando {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} de {{ $tickets->total() }} tickets
                @else
                    Sin tickets para mostrar
                @endif
            </span>
            <div class="flex items-center gap-1.5">
                @if($tickets->onFirstPage())
                    <span class="w-6 h-6 border border-slate-100 bg-slate-50 text-slate-300 flex items-center justify-center rounded cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <a href="{{ $tickets->previousPageUrl() }}" class="w-6 h-6 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center rounded transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif
                @if($tickets->hasMorePages())
                    <a href="{{ $tickets->nextPageUrl() }}" class="w-6 h-6 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center rounded transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span class="w-6 h-6 border border-slate-100 bg-slate-50 text-slate-300 flex items-center justify-center rounded cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Banners inferiores cliente --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- ¿Necesitas ayuda? --}}
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6 flex items-center gap-5">
            <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-synapso-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">¿Necesitas ayuda inmediata?</h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Nuestros agentes están disponibles Lun–Vie, 9am–6pm EST.
                </p>
                <a href="{{ route('tickets.create') }}"
                   class="inline-flex items-center gap-1.5 mt-3 text-xs font-bold text-synapso-gold
                          hover:text-synapso-amber transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Abrir nuevo ticket
                </a>
            </div>
        </div>

        {{-- Estado del sistema --}}
        <div class="relative overflow-hidden bg-indigo-700 rounded-xl shadow p-6 text-white">
            <div class="absolute right-4 top-4 text-white/10 pointer-events-none">
                <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-indigo-300 uppercase tracking-wider">Estado del Sistema</p>
                <h3 class="text-lg font-extrabold mt-1 tracking-tight">Operativo</h3>
                <p class="text-xs text-indigo-200 mt-2 leading-relaxed max-w-xs">
                    Toda la infraestructura y servicios cloud funcionan con normalidad.
                    Sin incidentes reportados en las últimas 24 horas.
                </p>
                <p class="text-[10px] text-indigo-300 mt-3 flex items-center gap-1.5 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Última revisión: hace 2 minutos
                </p>
            </div>
        </div>
    </div>


{{-- ═══════════════════════════════════════════════════════════════════════
     LAYOUT: AGENTE
     ═══════════════════════════════════════════════════════════════════════ --}}
@elseif($user->role === 'agent')

    {{-- Encabezado --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Gestión de Tickets</h1>
            <p class="mt-1.5 text-sm text-slate-500 font-medium">Gestiona y responde las solicitudes de servicio operativas.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- Filtro estado --}}
            <form id="filter-form" method="GET" action="{{ route('tickets.index') }}" class="flex flex-wrap gap-2">
                <select name="status" id="status-select" onchange="doSearch()"
                        class="border border-slate-200 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-700
                               bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 shadow-sm transition">
                    <option value="">Todos los estados</option>
                    <option value="open"        {{ $status === 'open'        ? 'selected' : '' }}>Abierto</option>
                    <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                    <option value="resolved"    {{ $status === 'resolved'    ? 'selected' : '' }}>Resuelto</option>
                    <option value="closed"      {{ $status === 'closed'      ? 'selected' : '' }}>Cerrado</option>
                </select>
                {{-- Filtro categoría --}}
                <select name="category_id" id="category-select" onchange="doSearch()"
                        class="border border-slate-200 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-700
                               bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300 shadow-sm transition">
                    <option value="">Todas las categorías</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                {{-- Rango de fecha --}}
                <div class="inline-flex bg-slate-100 p-0.5 rounded-lg border border-slate-200/50 shadow-inner">
                    @foreach(['all' => 'Todos', 'today' => 'Hoy', 'week' => 'Esta Semana'] as $range => $label)
                        <a href="{{ route('tickets.index', array_merge(request()->query(), ['date_range' => $range])) }}"
                           class="px-3 py-1 rounded-md text-xs font-bold transition-all duration-200
                                  {{ $dateRange === $range ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </form>
        </div>
    </div>

    {{-- Tarjetas métricas agente --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex items-center justify-between h-24">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tiempo Prom. Respuesta</p>
                <p class="text-2xl font-extrabold text-slate-800 mt-1 tracking-tight">
                    {{ $avgResponseTime ?? '—' }} <span class="text-base font-bold text-slate-500">hrs</span>
                </p>
            </div>
            <div class="w-9 h-9 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex items-center justify-between h-24">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tickets Activos</p>
                <p class="text-2xl font-extrabold text-slate-800 mt-1 tracking-tight">{{ $activeCount }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex items-center justify-between h-24">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Resueltos Hoy</p>
                <p class="text-2xl font-extrabold text-slate-800 mt-1 tracking-tight">{{ $resolvedToday }}</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex items-center justify-between h-24">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Escalaciones Críticas</p>
                <p class="text-2xl font-extrabold {{ $criticalCount > 0 ? 'text-red-600' : 'text-slate-800' }} mt-1 tracking-tight">
                    {{ $criticalCount }}
                </p>
            </div>
            <div class="w-9 h-9 rounded-full {{ $criticalCount > 0 ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-400' }} flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

    </div>

    {{-- Barra de búsqueda agente --}}
    <div class="mb-4 flex flex-wrap gap-2 items-center">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input id="search-input" type="text" name="search" value="{{ $search }}"
                   placeholder="Buscar por título…" autocomplete="off"
                   class="pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 bg-white
                          focus:outline-none focus:ring-2 focus:ring-indigo-300 transition w-56 placeholder-slate-400">
        </div>
        @if($search || $status || $categoryId)
            <a href="{{ route('tickets.index') }}"
               class="px-3 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 rounded-lg text-sm font-medium transition">
                Limpiar
            </a>
        @endif
    </div>

    {{-- Tabla agente --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden mb-8">
        <div class="overflow-x-auto">
            @if($tickets->isEmpty())
                <div class="py-12 text-center text-slate-400 text-sm">Sin tickets asignados.</div>
            @else
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Título</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden md:table-cell">Cliente</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Fecha</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Prioridad</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Tiempo Resp.</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tickets-tbody" class="bg-white divide-y divide-slate-100">
                        @foreach($tickets as $ticket)
                            @php
                                $pb = $priorityBadge[$ticket->priority] ?? ['label' => $ticket->priority, 'classes' => 'bg-slate-100 text-slate-600 border border-slate-200'];
                                $sb = $statusBadge[$ticket->status]     ?? ['label' => $ticket->status,   'classes' => 'bg-slate-100 text-slate-700'];
                                // Tiempo de respuesta en horas (due_date - resolved_at)
                                if ($ticket->resolved_at && $ticket->due_date) {
                                    $diff = abs($ticket->resolved_at->diffInMinutes($ticket->due_date));
                                    $respTime = $diff >= 60 ? round($diff / 60, 1).'h' : $diff.'m';
                                } else {
                                    $respTime = '—';
                                }
                                // Si está overdue (activo y pasó due_date)
                                $isOverdue = in_array($ticket->status, ['open','in_progress'])
                                    && $ticket->due_date
                                    && $ticket->due_date->isPast();
                            @endphp
                            <tr class="hover:bg-slate-50 transition duration-100">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-slate-800">{{ $ticket->title }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">ID: #SYN-{{ sprintf('%04d', $ticket->id) }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-medium hidden md:table-cell">
                                    {{ $ticket->user->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 hidden lg:table-cell">
                                    {{ $ticket->created_at->format('d M Y') }}
                                </td>
                                {{-- Estado — dropdown inline para el agente --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @can('update', $ticket)
                                        <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <select name="status" onchange="this.form.submit()"
                                                    class="border-0 bg-transparent text-xs font-semibold rounded-full
                                                           py-0.5 pl-2 pr-6 cursor-pointer focus:outline-none focus:ring-2
                                                           focus:ring-indigo-300 transition {{ $sb['classes'] }}">
                                                @foreach(['open' => 'Abierto', 'in_progress' => 'En Progreso', 'resolved' => 'Resuelto', 'closed' => 'Cerrado'] as $val => $lbl)
                                                    <option value="{{ $val }}" {{ $ticket->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sb['classes'] }}">
                                            {{ $sb['label'] }}
                                        </span>
                                    @endcan
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold tracking-wider {{ $pb['classes'] }}">
                                        {{ $pb['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                    <span class="text-sm font-semibold {{ $isOverdue ? 'text-red-500' : 'text-slate-600' }}">
                                        {{ $isOverdue ? 'Vencido' : $respTime }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('tickets.show', $ticket) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold
                                              text-synapso-gold hover:bg-amber-50 border border-amber-200 transition-colors duration-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        {{-- Paginación --}}
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-400">
                @if($tickets->total() > 0)
                    Mostrando {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} de {{ $tickets->total() }} tickets
                @else
                    Sin tickets para mostrar
                @endif
            </span>
            <div class="flex items-center gap-1.5">
                @if($tickets->onFirstPage())
                    <span class="w-6 h-6 border border-slate-100 bg-slate-50 text-slate-300 flex items-center justify-center rounded cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <a href="{{ $tickets->previousPageUrl() }}" class="w-6 h-6 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center rounded transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif
                @if($tickets->hasMorePages())
                    <a href="{{ $tickets->nextPageUrl() }}" class="w-6 h-6 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center rounded transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span class="w-6 h-6 border border-slate-100 bg-slate-50 text-slate-300 flex items-center justify-center rounded cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Banner inferior agente --}}
    <div class="relative overflow-hidden bg-synapso-navy rounded-xl p-6 text-white">
        <div class="absolute right-0 inset-y-0 w-1/3 pointer-events-none opacity-10"
             style="background: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 600 400%22><circle cx=%22500%22 cy=%22200%22 r=%22300%22 fill=%22white%22 opacity=%220.5%22/></svg>') no-repeat right center / cover;">
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-extrabold tracking-tight">¿Necesitas asistencia con un ticket?</h3>
                <p class="text-sm text-slate-300 mt-1 max-w-md leading-relaxed">
                    Nuestros ingenieros senior están disponibles para escalar cualquier ticket marcado como <span class="font-bold text-red-400">Crítico</span>.
                </p>
            </div>
            <a href="#"
               class="inline-flex items-center gap-2 bg-synapso-gold hover:bg-synapso-amber text-white px-5 py-2.5
                      rounded-lg font-bold text-sm shadow transition-all duration-150 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Contactar Supervisor
            </a>
        </div>
    </div>


{{-- ═══════════════════════════════════════════════════════════════════════
     LAYOUT: ADMIN
     ═══════════════════════════════════════════════════════════════════════ --}}
@else

    {{-- Encabezado --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Gestión de Tickets</h1>
            <p class="mt-1.5 text-sm text-slate-500 font-medium">
                Control total: gestiona, edita y audita todas las solicitudes del sistema.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('tickets.create') }}"
               class="inline-flex items-center gap-2 bg-synapso-gold hover:bg-synapso-amber text-white
                      px-4 py-2 rounded-lg text-sm font-semibold shadow-md transition-all duration-200
                      hover:shadow-lg hover:-translate-y-px active:translate-y-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Ticket
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

    {{-- Filtros admin --}}
    <div class="mb-5 flex flex-wrap gap-2 items-center">
        <form id="filter-form" method="GET" action="{{ route('tickets.index') }}" class="flex flex-wrap gap-2 items-center">
            {{-- Búsqueda --}}
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input id="search-input" type="text" name="search" value="{{ $search }}"
                       placeholder="Buscar por título…" autocomplete="off"
                       class="pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 bg-white
                              focus:outline-none focus:ring-2 focus:ring-indigo-300 transition w-52 placeholder-slate-400">
            </div>
            {{-- Estado --}}
            <select name="status" id="status-select" onchange="doSearch()"
                    class="border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 bg-white
                           focus:outline-none focus:ring-2 focus:ring-indigo-300 shadow-sm transition">
                <option value="">Todos los estados</option>
                <option value="open"        {{ $status === 'open'        ? 'selected' : '' }}>Abierto</option>
                <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                <option value="resolved"    {{ $status === 'resolved'    ? 'selected' : '' }}>Resuelto</option>
                <option value="closed"      {{ $status === 'closed'      ? 'selected' : '' }}>Cerrado</option>
            </select>
            {{-- Categoría --}}
            <select name="category_id" id="category-select" onchange="doSearch()"
                    class="border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 bg-white
                           focus:outline-none focus:ring-2 focus:ring-indigo-300 shadow-sm transition">
                <option value="">Todas las categorías</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @if($search || $status || $categoryId)
                <a href="{{ route('tickets.index') }}"
                   class="px-3 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 rounded-lg text-sm font-medium transition">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    {{-- Tabla admin --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden mb-8">
        <div class="overflow-x-auto">
            @if($tickets->isEmpty())
                <div class="py-12 text-center text-slate-400 text-sm">Sin tickets registrados.</div>
            @else
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">ID Ticket</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Título</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Categoría</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Prioridad</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden md:table-cell">Cliente</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Agente Asignado</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Fecha</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tickets-tbody" class="bg-white divide-y divide-slate-100">
                        @foreach($tickets as $ticket)
                            @php
                                $prefix = in_array($ticket->priority, ['urgent','high']) ? 'INC' : 'REQ';
                                $pb = $priorityBadge[$ticket->priority] ?? ['label' => $ticket->priority, 'classes' => 'bg-slate-100 text-slate-600 border border-slate-200'];
                                $sb = $statusBadge[$ticket->status]     ?? ['label' => $ticket->status,   'classes' => 'bg-slate-100 text-slate-700'];
                            @endphp
                            <tr class="hover:bg-slate-50 transition duration-100 group">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="text-xs font-bold text-slate-500">#{{ $prefix }}-{{ sprintf('%04d', $ticket->id) }}</span>
                                </td>
                                <td class="px-4 py-4 max-w-[200px]">
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $ticket->title }}</p>
                                </td>
                                {{-- Categoría editable --}}
                                <td class="px-4 py-4 whitespace-nowrap hidden lg:table-cell">
                                    <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select name="category_id" onchange="this.form.submit()"
                                                class="border border-slate-200 bg-white text-slate-700 text-xs font-semibold
                                                       rounded-lg py-1 pl-2 pr-6 hover:bg-slate-50 focus:outline-none
                                                       focus:ring-2 focus:ring-indigo-300 transition cursor-pointer shadow-sm">
                                            <option value="">Sin categoría</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ $ticket->category_id === $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                {{-- Prioridad editable --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select name="priority" onchange="this.form.submit()"
                                                class="border-0 bg-transparent text-[10px] font-bold tracking-wider
                                                       rounded py-0.5 pl-2 pr-5 cursor-pointer focus:outline-none
                                                       focus:ring-2 focus:ring-indigo-300 transition {{ $pb['classes'] }}">
                                            <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>CRÍTICO</option>
                                            <option value="high"   {{ $ticket->priority === 'high'   ? 'selected' : '' }}>ALTA</option>
                                            <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>MEDIA</option>
                                            <option value="low"    {{ $ticket->priority === 'low'    ? 'selected' : '' }}>BAJA</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-600 font-medium hidden md:table-cell">
                                    {{ $ticket->user->name ?? '—' }}
                                </td>
                                {{-- Agente editable --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select name="agent_id" onchange="this.form.submit()"
                                                class="border border-slate-200 bg-slate-50 text-slate-700 text-xs font-semibold
                                                       rounded-lg py-1 pl-2 pr-6 hover:bg-slate-100 focus:outline-none
                                                       focus:ring-2 focus:ring-indigo-300 transition cursor-pointer shadow-sm min-w-[120px]">
                                            <option value="">Sin asignar</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->id }}" {{ $ticket->agent_id === $agent->id ? 'selected' : '' }}>
                                                    {{ $agent->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                {{-- Estado badge --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $sb['classes'] }}">
                                        {{ $sb['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-xs text-slate-500 hidden lg:table-cell">
                                    {{ $ticket->created_at->format('Y-m-d') }}
                                </td>
                                {{-- Acciones --}}
                                <td class="px-4 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                           title="Ver detalles"
                                           class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors duration-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @can('delete', $ticket)
                                            <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" id="delete-form-{{ $ticket->id }}">
                                                @csrf @method('DELETE')
                                                <button type="button"
                                                        title="Eliminar ticket"
                                                        onclick="window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                                                            detail: {
                                                                title: 'Eliminar Ticket',
                                                                message: '¿Eliminar este ticket? Esta acción no se puede deshacer.',
                                                                confirmText: 'Eliminar',
                                                                cancelText: 'Cancelar',
                                                                confirmButtonClass: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
                                                                onConfirm: () => document.getElementById('delete-form-{{ $ticket->id }}').submit()
                                                            }
                                                        }))"
                                                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors duration-100">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        {{-- Paginación --}}
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-400">
                @if($tickets->total() > 0)
                    Mostrando {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} de {{ $tickets->total() }} tickets
                @else
                    Sin tickets para mostrar
                @endif
            </span>
            <div class="flex items-center gap-1.5">
                @if($tickets->onFirstPage())
                    <span class="w-6 h-6 border border-slate-100 bg-slate-50 text-slate-300 flex items-center justify-center rounded cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @else
                    <a href="{{ $tickets->previousPageUrl() }}" class="w-6 h-6 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center rounded transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endif
                @if($tickets->hasMorePages())
                    <a href="{{ $tickets->nextPageUrl() }}" class="w-6 h-6 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center rounded transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <span class="w-6 h-6 border border-slate-100 bg-slate-50 text-slate-300 flex items-center justify-center rounded cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Tarjetas métricas admin --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Abiertos</p>
            <p class="text-3xl font-extrabold text-blue-600 mt-2 tracking-tight">{{ number_format($totalOpen) }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Escalados</p>
            <p class="text-3xl font-extrabold {{ $escalatedCount > 0 ? 'text-red-600' : 'text-slate-800' }} mt-2 tracking-tight">
                {{ number_format($escalatedCount) }}
            </p>
        </div>

        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Eficiencia del Agente</p>
                <span class="text-xs font-bold text-emerald-600">+{{ $agentEfficiencyPct }}%</span>
            </div>
            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                <div class="bg-synapso-gold h-full rounded-full transition-all duration-700"
                     style="width: {{ $agentEfficiencyPct }}%"></div>
            </div>
        </div>

    </div>

@endif


</div>
</div>

{{-- ── Búsqueda dinámica (fetch + debounce) ─────────────────────────────── --}}
<script>
function doSearch() {
    const input    = document.getElementById('search-input');
    const status   = document.getElementById('status-select');
    const category = document.getElementById('category-select');
    const tbody    = document.getElementById('tickets-tbody');

    if (!tbody) return;

    const params = new URLSearchParams();
    if (input   && input.value)    params.set('search',      input.value);
    if (status  && status.value)   params.set('status',      status.value);
    if (category && category.value) params.set('category_id', category.value);

    const url = '{{ route('tickets.index') }}?' + params.toString();

    tbody.style.opacity    = '0.4';
    tbody.style.transition = 'opacity 0.15s';

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
            const doc      = (new DOMParser()).parseFromString(html, 'text/html');
            const newTbody = doc.getElementById('tickets-tbody');
            if (newTbody) tbody.innerHTML = newTbody.innerHTML;
            history.pushState(null, '', url);
            tbody.style.opacity = '1';
        })
        .catch(() => { tbody.style.opacity = '1'; });
}

// Debounce para el campo de búsqueda
(function () {
    const input = document.getElementById('search-input');
    if (!input) return;
    let timer;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(doSearch, 350);
    });
})();
</script>

@endsection