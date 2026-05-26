@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-synapso-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- LAYOUT: ADMIN--}}
        @if(auth()->user()->role === 'admin')
            
            <!-- Encabezado -->
            <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Supervisión Global</h1>
                    <p class="mt-2 text-sm text-slate-500 font-medium">Métricas operacionales de toda la compañía y gestión de tickets.</p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Segmented Controls / Pill Buttons for Date Range -->
                    <div class="inline-flex bg-slate-100 p-0.5 rounded-lg border border-slate-200/50 shadow-inner">
                        @foreach([
                            'all'   => 'Todos',
                            'today' => 'Hoy',
                            'week'  => 'Esta Semana',
                            'month' => 'Este Mes'
                        ] as $range => $label)
                            <a href="{{ route('dashboard', ['date_range' => $range, 'status' => $status]) }}" 
                               class="px-3 py-1 rounded-md text-xs font-bold transition-all duration-200 {{ $dateRange === $range ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Exportar Button -->
                    <button type="button" class="inline-flex items-center gap-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition duration-150">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Exportar
                    </button>
                </div>
            </div>

            <!-- Tres tarjetas de estado -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <!-- Tarjeta 1: TOTAL OPEN TICKETS -->
                <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between h-36">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-blue-50/40 pointer-events-none"></div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tickets Abiertos</p>
                        <div class="flex items-baseline mt-2">
                            <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($openCount) }}</span>
                            @if($openTrendPercent > 0)
                                <span class="text-red-500 font-extrabold text-xs ml-2 inline-flex items-center gap-0.5">▲ {{ $openTrendPercent }}%</span>
                            @elseif($openTrendPercent < 0)
                                <span class="text-emerald-500 font-extrabold text-xs ml-2 inline-flex items-center gap-0.5">▼ {{ abs($openTrendPercent) }}%</span>
                            @else
                                <span class="text-slate-400 font-medium text-xs ml-2">—</span>
                            @endif
                        </div>
                    </div>
                    <div class="w-full bg-slate-50 h-1.5 rounded-full mt-auto overflow-hidden">
                        <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ $openPercentage }}%"></div>
                    </div>
                </div>

                <!-- Tarjeta 2: AVG. RESOLUTION TIME -->
                <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between h-36">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-amber-50/40 pointer-events-none"></div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tiempo Promedio de Resolución</p>
                        <div class="flex items-baseline mt-2">
                            <span class="text-3xl font-extrabold text-slate-800 tracking-tight">
                                {{ $avgResolutionTime ? number_format($avgResolutionTime, 1) : '—' }}
                                @if($avgResolutionTime)
                                    <span class="text-xl font-bold text-slate-600">hrs</span>
                                @endif
                            </span>
                            @if($avgResolutionTrend < 0)
                                <span class="text-emerald-500 font-extrabold text-xs ml-2 inline-flex items-center gap-0.5">▼ {{ abs($avgResolutionTrend) }}h</span>
                            @elseif($avgResolutionTrend > 0)
                                <span class="text-red-500 font-extrabold text-xs ml-2 inline-flex items-center gap-0.5">▲ +{{ $avgResolutionTrend }}h</span>
                            @else
                                <span class="text-slate-400 font-medium text-xs ml-2">—</span>
                            @endif
                        </div>
                    </div>
                    <div class="w-full bg-slate-50 h-1.5 rounded-full mt-auto overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: {{ $resolutionProgress }}%"></div>
                    </div>
                </div>

                <!-- Tarjeta 3: RESOLVED TICKETS -->
                <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between h-36">
                    <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full bg-emerald-50/40 pointer-events-none"></div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tickets Resueltos</p>
                        <div class="flex items-baseline mt-2">
                            <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($resolvedThisMonthCount) }}</span>
                            <span class="text-slate-400 font-medium text-xs ml-2">
                                @if($dateRange === 'today')
                                    hoy
                                @elseif($dateRange === 'week')
                                    esta semana
                                @elseif($dateRange === 'month')
                                    este mes
                                @else
                                    histórico
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="w-full bg-slate-50 h-1.5 rounded-full mt-auto overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $resolvedPercentage }}%"></div>
                    </div>
                </div>

            </div>

            <!-- Tabla de gestión de tickets -->
            <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-white">
                    <h2 class="text-base font-bold text-slate-800 tracking-tight">Gestión Total</h2>
                    
                    @php
                        $statusLabels = [
                            'all'         => 'Todos los Estados',
                            'open'        => 'Abiertos',
                            'in_progress' => 'En Progreso',
                            'resolved'    => 'Resueltos',
                            'closed'      => 'Cerrados'
                        ];
                        $currentStatusLabel = $statusLabels[$status] ?? ($statusLabels[request('status')] ?? 'Todos los Estados');
                    @endphp

                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button type="button" @click="open = !open" 
                                class="inline-flex items-center gap-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            {{ $currentStatusLabel }}
                            <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-1.5 w-44 rounded-lg bg-white shadow-lg border border-slate-100 ring-1 ring-black ring-opacity-5 focus:outline-none z-30" 
                             style="display: none;">
                            <div class="py-1">
                                @foreach($statusLabels as $statusKey => $label)
                                    <a href="{{ route('dashboard', ['date_range' => $dateRange, 'status' => $statusKey]) }}" 
                                       class="block px-4 py-2 text-xs font-semibold {{ ($status === $statusKey || ($statusKey === 'all' && !$status)) ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }} transition">
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    @if($recentTickets->isEmpty())
                        <div class="p-8 text-center text-slate-400 text-sm">
                            No se encontraron tickets.
                        </div>
                    @else
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">ID del Ticket</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Categoría</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Prioridad</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Agente</th>
                                    <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @foreach($recentTickets as $ticket)
                                    @php
                                        // Prefix dynamic assignment INC/REQ
                                        $prefix = ($ticket->priority === 'urgent' || $ticket->priority === 'high') ? 'INC' : 'REQ';
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition duration-100">
                                        <!-- ID -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">
                                            #{{ $prefix }}-{{ sprintf('%04d', $ticket->id) }}
                                        </td>
                                        <!-- Category -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-505 font-medium text-slate-600">
                                            {{ $ticket->category->name ?? 'Sin categorizar' }}
                                        </td>
                                        <!-- Priority Badges -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($ticket->priority === 'urgent')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded border border-red-200 bg-red-50 text-[10px] font-bold text-red-600 tracking-wider">
                                                    <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.209.325-.379.69-.528 1.042-.113.266-.251.57-.42.896-.12.23-.29.497-.474.768a49.386 49.386 0 00-2.077 3.307c-.583.98-.9 2.007-.9 3.01 0 3.3 2.7 6 6 6s6-2.7 6-6c0-1.635-.705-2.988-1.8-3.89a1 1 0 00-1.58 1.16c.375.512.58 1.116.58 1.73 0 1.65-1.35 3-3 3s-3-1.35-3-3c0-.386.075-.753.22-1.096.225-.538.588-1.077 1.01-1.664.38-.529.74-1.03 1.016-1.562.18-.346.378-.775.518-1.216.143-.448.263-.967.26-1.548 0-.49-.136-.957-.403-1.405a1 1 0 00-.195-.263z" clip-rule="evenodd"/></svg>
                                                    CRÍTICO
                                                </span>
                                            @elseif($ticket->priority === 'high')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded border border-red-200 bg-red-50 text-[10px] font-bold text-orange-600 tracking-wider">
                                                    <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                                    ALTA
                                                </span>
                                            @elseif($ticket->priority === 'medium')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded border border-orange-200 bg-orange-50 text-[10px] font-bold text-amber-600 tracking-wider">
                                                    <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                                    MEDIA
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded border border-blue-200 bg-blue-50 text-[10px] font-bold text-blue-600 tracking-wider">
                                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                                    BAJA
                                                </span>
                                            @endif
                                        </td>
                                        <!-- Agent Dropdown View -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($ticket->agent)
                                                <div class="flex items-center gap-2 cursor-pointer group/agent">
                                                    <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold uppercase overflow-hidden ring-1 ring-slate-100">
                                                        {{ substr($ticket->agent->name, 0, 2) }}
                                                    </div>
                                                    <span class="text-sm font-medium text-slate-700 group-hover/agent:text-slate-900 transition">{{ $ticket->agent->name }}</span>
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-2 cursor-pointer group/agent">
                                                    <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-400 border border-dashed border-slate-300 flex items-center justify-center">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    </div>
                                                    <span class="text-sm font-medium text-slate-400 group-hover/agent:text-slate-600 transition">Sin asignar</span>
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </div>
                                            @endif
                                        </td>
                                        <!-- Status Badge -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($ticket->status === 'open')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700">Abierto</span>
                                            @elseif($ticket->status === 'in_progress')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800">En Progreso</span>
                                            @elseif($ticket->status === 'resolved')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800">Resuelto</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">Cerrado</span>
                                            @endif
                                        </td>
                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="text-slate-400 hover:text-slate-600 transition inline-flex items-center justify-center p-1 rounded hover:bg-slate-100" title="Ver Detalles">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                <!-- Footer pagination -->
                <div class="px-6 py-4 border-t border-slate-100 bg-white flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400">
                        @if($recentTickets->total() > 0)
                            Mostrando {{ $recentTickets->firstItem() }}-{{ $recentTickets->lastItem() }} de {{ $recentTickets->total() }}
                        @else
                            No hay tickets para mostrar
                        @endif
                    </span>
                    <div class="flex items-center gap-1.5">
                        @if($recentTickets->onFirstPage())
                            <span class="w-6 h-6 border border-slate-100 bg-slate-50 text-slate-300 flex items-center justify-center rounded cursor-not-allowed shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        @else
                            <a href="{{ $recentTickets->previousPageUrl() }}" 
                               class="w-6 h-6 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center rounded transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                        @endif

                        @if($recentTickets->hasMorePages())
                            <a href="{{ $recentTickets->nextPageUrl() }}" 
                               class="w-6 h-6 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center rounded transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <span class="w-6 h-6 border border-slate-100 bg-slate-50 text-slate-300 flex items-center justify-center rounded cursor-not-allowed shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        {{-- LAYOUT: AGENTE--}}
        @elseif(auth()->user()->role === 'agent')
            
            <!-- Encabezado -->
            <div class="mb-8">
                @php
                    $hour = now()->hour;
                    if ($hour < 12) {
                        $greeting = "Buenos días";
                    } elseif ($hour < 18) {
                        $greeting = "Buenas tardes";
                    } else {
                        $greeting = "Buenas noches";
                    }
                @endphp
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $greeting }}, {{ auth()->user()->name }}.</h1>
                <p class="mt-2 text-sm text-slate-500 font-medium">Aquí tienes tu resumen diario.</p>
            </div>

            <!-- Tres tarjetas de estado para el Agente -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <!-- Tarjeta 1: RESOLVED TODAY -->
                <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-center justify-between h-28">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Resueltos Hoy</p>
                        <p class="text-3xl font-extrabold text-slate-800 mt-1 tracking-tight">{{ $resolvedToday }}</p>
                        @if($resolvedTrend > 0)
                            <p class="text-[10px] font-bold text-emerald-500 mt-1 inline-flex items-center gap-0.5">
                                ▲ +{{ $resolvedTrend }} desde ayer
                            </p>
                        @elseif($resolvedTrend < 0)
                            <p class="text-[10px] font-bold text-red-500 mt-1 inline-flex items-center gap-0.5">
                                ▼ {{ $resolvedTrend }} desde ayer
                            </p>
                        @else
                            <p class="text-[10px] font-medium text-slate-400 mt-1 inline-flex items-center gap-0.5">
                                — Sin cambios desde ayer
                            </p>
                        @endif
                    </div>
                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <!-- Tarjeta 2: AVG RESPONSE TIME -->
                <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-center justify-between h-28">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tiempo Prom. de Respuesta</p>
                        <p class="text-3xl font-extrabold text-slate-800 mt-1 tracking-tight">
                            {{ $avgResponseTime ? number_format($avgResponseTime, 1) : '—' }}
                            @if($avgResponseTime)
                                <span class="text-lg font-bold text-slate-600">hrs</span>
                            @endif
                        </p>
                        @if($avgResponseTrend < 0)
                            <p class="text-[10px] font-bold text-emerald-500 mt-1 inline-flex items-center gap-0.5">
                                ▼ {{ $avgResponseTrend }}h esta semana
                            </p>
                        @elseif($avgResponseTrend > 0)
                            <p class="text-[10px] font-bold text-red-500 mt-1 inline-flex items-center gap-0.5">
                                ▲ +{{ $avgResponseTrend }}h esta semana
                            </p>
                        @else
                            <p class="text-[10px] font-medium text-slate-400 mt-1 inline-flex items-center gap-0.5">
                                — Sin cambios esta semana
                            </p>
                        @endif
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <!-- Tarjeta 3: SLA BREACHES -->
                <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-center justify-between h-28">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Incumplimientos de SLA</p>
                        <p class="text-3xl font-extrabold text-slate-800 mt-1 tracking-tight">{{ $slaBreachesCount }}</p>
                        @if($slaBreachesTrend > 0)
                            <p class="text-[10px] font-bold text-red-500 mt-1 inline-flex items-center gap-0.5">
                                ▲ +{{ $slaBreachesTrend }} desde ayer
                            </p>
                        @elseif($slaBreachesTrend < 0)
                            <p class="text-[10px] font-bold text-emerald-500 mt-1 inline-flex items-center gap-0.5">
                                ▼ {{ $slaBreachesTrend }} desde ayer
                            </p>
                        @else
                            <p class="text-[10px] font-medium text-slate-400 mt-1 inline-flex items-center gap-0.5">
                                — Sin cambios desde ayer
                            </p>
                        @endif
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>

            </div>

            <!-- Cabecera de Kanban -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">Flujo de Trabajo Activo</h2>
                
                @php
                    $priorityLabels = [
                        'all'    => 'Prioridad: Todas',
                        'urgent' => 'Prioridad: Crítica',
                        'high'   => 'Prioridad: Alta',
                        'medium' => 'Prioridad: Media',
                        'low'    => 'Prioridad: Baja',
                    ];
                    $currentPriorityLabel = $priorityLabels[$priority] ?? ($priorityLabels[request('priority')] ?? 'Prioridad: Todas');

                    $orderLabels = [
                        'created_at_desc' => 'Más Nuevos',
                        'created_at_asc'  => 'Más Antiguos',
                        'due_date_asc'    => 'Vence Próximamente',
                    ];
                    $currentOrderLabel = $orderLabels[$orderBy] ?? ($orderLabels[request('order_by')] ?? 'Ordenar por');
                @endphp

                <div class="flex items-center gap-2">
                    <!-- Dropdown: Filtrar por Prioridad -->
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button type="button" @click="open = !open" 
                                class="inline-flex items-center gap-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            {{ $currentPriorityLabel }}
                            <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" 
                             @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-1.5 w-44 rounded-lg bg-white shadow-lg border border-slate-100 ring-1 ring-black ring-opacity-5 focus:outline-none z-30" 
                             style="display: none;">
                            <div class="py-1">
                                @foreach($priorityLabels as $key => $label)
                                    <a href="{{ route('dashboard', ['priority' => $key, 'order_by' => $orderBy]) }}" 
                                       class="block px-4 py-2 text-xs font-semibold {{ ($priority === $key || ($key === 'all' && !$priority)) ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }} transition">
                                        {{ str_replace('Prioridad: ', '', $label) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown: Ordenar -->
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button type="button" @click="open = !open" 
                                class="inline-flex items-center gap-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l-4-4m4 4v12"/></svg>
                            {{ $currentOrderLabel }}
                            <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" 
                             @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-1.5 w-48 rounded-lg bg-white shadow-lg border border-slate-100 ring-1 ring-black ring-opacity-5 focus:outline-none z-30" 
                             style="display: none;">
                            <div class="py-1">
                                @foreach($orderLabels as $key => $label)
                                    <a href="{{ route('dashboard', ['priority' => $priority, 'order_by' => $key]) }}" 
                                       class="block px-4 py-2 text-xs font-semibold {{ $orderBy === $key ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' }} transition">
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grilla de Columnas Kanban -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                <!-- Columna 1: New -->
                <div class="bg-slate-50/60 border border-slate-100 rounded-xl p-4 flex flex-col gap-4 min-h-[500px]">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 mr-2"></span>
                            <span class="text-sm font-bold text-slate-800">Nuevos</span>
                        </div>
                        <span class="bg-slate-200/60 text-slate-600 px-2 py-0.5 rounded-full text-xs font-extrabold">{{ $agentNewTickets->count() }}</span>
                    </div>

                    <div class="flex flex-col gap-4 overflow-y-auto max-h-[600px] pr-1">
                        @if($agentNewTickets->isEmpty())
                            <div class="border border-dashed border-slate-200 rounded-xl p-6 text-center text-slate-400 text-xs font-medium bg-white">
                                Sin nuevos tickets asignados.
                            </div>
                        @else
                            @foreach($agentNewTickets as $ticket)
                                <a href="{{ route('tickets.show', $ticket) }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 hover:shadow transition duration-150 relative block group">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[11px] font-bold text-slate-400 tracking-wider">#TK-{{ sprintf('%04d', $ticket->id) }}</span>
                                        @if($ticket->priority === 'urgent')
                                            <span class="bg-red-50 text-red-600 border border-red-100 px-1.5 py-0.5 rounded text-[9px] font-extrabold tracking-wider">CRÍTICO</span>
                                        @elseif($ticket->priority === 'high')
                                            <span class="bg-red-50 text-orange-600 border border-orange-100 px-1.5 py-0.5 rounded text-[9px] font-extrabold tracking-wider">ALTA</span>
                                        @elseif($ticket->priority === 'medium')
                                            <span class="bg-amber-50 text-amber-600 border border-amber-100 px-1.5 py-0.5 rounded text-[9px] font-extrabold tracking-wider">MEDIA</span>
                                        @else
                                            <span class="bg-blue-50 text-blue-600 border border-blue-100 px-1.5 py-0.5 rounded text-[9px] font-extrabold tracking-wider">BAJA</span>
                                        @endif
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 mt-2.5 group-hover:text-synapso-navy transition line-clamp-2 leading-snug">{{ $ticket->title }}</h3>
                                    <p class="text-xs text-slate-400 font-medium mt-1">Cliente: {{ $ticket->user->name ?? 'N/A' }}</p>
                                    <div class="text-[10px] text-slate-400 font-semibold mt-4 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $ticket->created_at->diffForHumans() }}
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Columna 2: In Progress -->
                <div class="bg-slate-50/60 border border-slate-100 rounded-xl p-4 flex flex-col gap-4 min-h-[500px]">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-600 mr-2"></span>
                            <span class="text-sm font-bold text-slate-800">En Progreso</span>
                        </div>
                        <span class="bg-slate-200/60 text-slate-600 px-2 py-0.5 rounded-full text-xs font-extrabold">{{ $agentInProgressTickets->count() }}</span>
                    </div>

                    <div class="flex flex-col gap-4 overflow-y-auto max-h-[600px] pr-1">
                        @if($agentInProgressTickets->isEmpty())
                            <div class="border border-dashed border-slate-200 rounded-xl p-6 text-center text-slate-400 text-xs font-medium bg-white">
                                Sin tickets en progreso.
                            </div>
                        @else
                            @foreach($agentInProgressTickets as $ticket)
                                <a href="{{ route('tickets.show', $ticket) }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 hover:shadow transition duration-150 relative block group">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[11px] font-bold text-slate-400 tracking-wider">#TK-{{ sprintf('%04d', $ticket->id) }}</span>
                                        @if($ticket->priority === 'urgent')
                                            <span class="bg-red-50 text-red-600 border border-red-100 px-1.5 py-0.5 rounded text-[9px] font-extrabold tracking-wider">CRÍTICO</span>
                                        @elseif($ticket->priority === 'high')
                                            <span class="bg-red-50 text-orange-600 border border-orange-100 px-1.5 py-0.5 rounded text-[9px] font-extrabold tracking-wider">ALTA</span>
                                        @elseif($ticket->priority === 'medium')
                                            <span class="bg-amber-50 text-amber-600 border border-amber-100 px-1.5 py-0.5 rounded text-[9px] font-extrabold tracking-wider">MEDIA</span>
                                        @else
                                            <span class="bg-blue-50 text-blue-600 border border-blue-100 px-1.5 py-0.5 rounded text-[9px] font-extrabold tracking-wider">BAJA</span>
                                        @endif
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 mt-2.5 group-hover:text-synapso-navy transition line-clamp-2 leading-snug">{{ $ticket->title }}</h3>
                                    <p class="text-xs text-slate-400 font-medium mt-1">Cliente: {{ $ticket->user->name ?? 'N/A' }}</p>
                                    <div class="flex items-center justify-between mt-4">
                                        <div class="text-[10px] text-slate-400 font-semibold flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $ticket->created_at->diffForHumans() }}
                                        </div>
                                        <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[9px] font-bold uppercase overflow-hidden ring-1 ring-slate-100">
                                            {{ substr(auth()->user()->name, 0, 2) }}
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Columna 3: Resolved -->
                <div class="bg-slate-50/60 border border-slate-100 rounded-xl p-4 flex flex-col gap-4 min-h-[500px]">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <div class="flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 mr-2"></span>
                            <span class="text-sm font-bold text-slate-800">Resueltos</span>
                        </div>
                        <span class="bg-slate-200/60 text-slate-600 px-2 py-0.5 rounded-full text-xs font-extrabold">{{ $agentResolvedTickets->count() }}</span>
                    </div>

                    <div class="flex flex-col gap-4 overflow-y-auto max-h-[600px] pr-1">
                        @if($agentResolvedTickets->isEmpty())
                            <div class="border border-dashed border-slate-200 rounded-xl p-6 text-center text-slate-400 text-xs font-medium bg-white">
                                Sin tickets resueltos.
                            </div>
                        @else
                            @foreach($agentResolvedTickets as $ticket)
                                <a href="{{ route('tickets.show', $ticket) }}" class="bg-white rounded-xl shadow-sm border-y border-r border-l-4 border-l-emerald-500 border-slate-100 p-5 hover:shadow transition duration-150 relative block group">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[11px] font-bold text-slate-400 tracking-wider">#TK-{{ sprintf('%04d', $ticket->id) }}</span>
                                        <span class="text-emerald-600 font-extrabold text-[9px] tracking-wider inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            Hoy
                                        </span>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 mt-2.5 group-hover:text-synapso-navy transition line-clamp-2 leading-snug">{{ $ticket->title }}</h3>
                                    <p class="text-xs text-slate-400 font-medium mt-1">Cliente: {{ $ticket->user->name ?? 'N/A' }}</p>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>

        {{-- LAYOUT: CLIENTE    --}}
        @elseif(auth()->user()->role === 'client')
            
            <!-- Encabezado -->
            <div class="mb-8">
                @php
                    $hour = now()->hour;
                    if ($hour < 12) {
                        $greeting = 'Buenos días';
                    } elseif ($hour < 18) {
                        $greeting = 'Buenas tardes';
                    } else {
                        $greeting = 'Buenas noches';
                    }
                @endphp
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $greeting }}, {{ auth()->user()->name }}!</h1>
                <p class="mt-2 text-sm text-slate-500 font-medium">Aquí tienes un resumen rápido de tus solicitudes actuales. ¿Necesitas ayuda con algo nuevo? Usa el botón 'Crear Nuevo Ticket' para comenzar.</p>
            </div>

            <!-- Grilla Principal (Left: Activity feed, Right: CTA + Summary Sidebar) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Columna Izquierda: Actividad Reciente (span 2) -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-extrabold text-slate-800">Actividad Reciente</h2>
                            <a href="{{ route('tickets.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700 transition">
                                Ver Todo
                            </a>
                        </div>
                        
                        <div class="space-y-4">
                            @forelse($recentTickets as $ticket)
                                @php
                                    // 1. Determinar círculo e icono de acuerdo a categoría/estado
                                    $circleColor = 'bg-blue-50 text-blue-600 border border-blue-100';
                                    // Icono de Laptop por defecto (Hardware)
                                    $iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>';
                                    
                                    if ($ticket->category && Str::contains(Str::lower($ticket->category->name), ['software', 'acces', 'sistema', 'cuenta'])) {
                                        $circleColor = 'bg-amber-50 text-amber-600 border border-amber-100';
                                        // Icono de Llave (Accesos)
                                        $iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>';
                                    } elseif ($ticket->category && Str::contains(Str::lower($ticket->category->name), ['wifi', 'red', 'internet', 'conexi', 'network'])) {
                                        $circleColor = 'bg-indigo-50 text-indigo-600 border border-indigo-100';
                                        // Icono de Wifi
                                        $iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071a9.9 9.9 0 0114.162 0M3.515 9.515a14 14 0 0116.97 0"/></svg>';
                                    } elseif ($ticket->category && Str::contains(Str::lower($ticket->category->name), ['correo', 'email', 'sync', 'sincroniz'])) {
                                        $circleColor = 'bg-pink-50 text-pink-600 border border-pink-100';
                                        // Icono de Correo
                                        $iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>';
                                    }

                                    // Sobrescribir icono según el estado del ticket
                                    if ($ticket->status === 'open') {
                                        $circleColor = 'bg-blue-50 text-blue-600 border border-blue-100';
                                        // Icono de Puerta Abierta (Abierto)
                                        $iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H5v14h4M9 5l7-2v18l-7-2V5z"/></svg>';
                                    } elseif ($ticket->status === 'in_progress') {
                                        $circleColor = 'bg-amber-50 text-amber-600 border border-amber-100';
                                        // Icono de Engranaje (En Progreso) - ¡Agregando clase animate-spin de velocidad lenta para un toque increíble!
                                        $iconSvg = '<svg class="w-5 h-5 animate-spin" style="animation-duration: 8s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
                                    } elseif ($ticket->status === 'resolved' || $ticket->status === 'closed') {
                                        $circleColor = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                                        // Icono de Check (Resuelto)
                                        $iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                                    }
                                    
                                    $clientStatusColors = [
                                        'open' => 'bg-blue-50 text-blue-600 border border-blue-100',
                                        'in_progress' => 'bg-amber-50 text-amber-600 border border-amber-100',
                                        'resolved' => 'bg-emerald-50 text-emerald-600 border border-emerald-100',
                                        'closed' => 'bg-slate-50 text-slate-600 border border-slate-100',
                                    ];
                                    $clientStatusLabels = [
                                        'open' => 'Abierto',
                                        'in_progress' => 'En Progreso',
                                        'resolved' => 'Resuelto',
                                        'closed' => 'Cerrado',
                                    ];
                                @endphp
                                
                                <a href="{{ route('tickets.show', $ticket) }}" class="flex items-center justify-between p-4 bg-slate-50/50 hover:bg-slate-50 border border-slate-100 hover:border-slate-200 rounded-xl transition duration-150 group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $circleColor }} flex-shrink-0">
                                            {!! $iconSvg !!}
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-semibold text-slate-800 group-hover:text-synapso-navy transition truncate">{{ $ticket->title }}</h3>
                                            <p class="text-xs text-slate-505 mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span>{{ sprintf('REQ-%04d', $ticket->id) }}</span>
                                                <span class="text-slate-300">&bull;</span>
                                                @if($ticket->status === 'resolved' || $ticket->status === 'closed')
                                                    <span>{{ $ticket->status === 'resolved' ? 'Resuelto' : 'Cerrado' }} {{ $ticket->updated_at->diffForHumans() }}</span>
                                                @else
                                                    <span>Abierto {{ $ticket->created_at->diffForHumans() }}</span>
                                                @endif
                                                <span class="text-slate-300">&bull;</span>
                                                <span class="inline-flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full @if($ticket->priority === 'urgent') bg-red-500 @elseif($ticket->priority === 'high') bg-orange-500 @elseif($ticket->priority === 'medium') bg-blue-500 @else bg-slate-400 @endif"></span>
                                                    <span class="font-bold uppercase text-[9px] tracking-wider @if($ticket->priority === 'urgent') text-red-600 @elseif($ticket->priority === 'high') text-orange-600 @elseif($ticket->priority === 'medium') text-blue-600 @else text-slate-505 @endif">
                                                        @if($ticket->priority === 'urgent')
                                                            URGENTE
                                                        @elseif($ticket->priority === 'high')
                                                            ALTA
                                                        @elseif($ticket->priority === 'medium')
                                                            MEDIA
                                                        @else
                                                            BAJA
                                                        @endif
                                                    </span>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $clientStatusColors[$ticket->status] ?? 'bg-slate-100 text-slate-800' }}">
                                        {{ $clientStatusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                                    </span>
                                </a>
                            @empty
                                <div class="text-center py-8 text-slate-400">
                                    Sin actividad reciente.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <!-- Columna Derecha: Sidebar (CTA + Resumen) -->
                <div class="space-y-6">
                    
                    <!-- Tarjeta 1: Indigo Support CTA -->
                    <div class="relative overflow-hidden bg-indigo-700 text-white rounded-xl shadow p-6 flex flex-col justify-between min-h-[220px]">
                        {{-- Gráfico de audífonos de soporte de fondo --}}
                        <div class="absolute right-4 bottom-4 text-white/5 pointer-events-none">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.486 2 2 6.486 2 12v5a3 3 0 003 3h3v-8H5v-2c0-3.859 3.14-7 7-7s7 3.141 7 7v2h-3v8h3a3 3 0 003-3v-5c0-5.514-4.486-10-10-10zm-5 16H5v-4h2v4zm12 0h-2v-4h2v4z"/>
                            </svg>
                        </div>
                        
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold tracking-tight">¿Necesitas Ayuda?</h3>
                            <p class="mt-2 text-sm text-slate-200 leading-relaxed">Nuestro equipo de soporte técnico de TI está listo para ayudarte a resolver cualquier problema técnico rápidamente.</p>
                        </div>
                        
                        <div class="relative z-10 mt-6">
                            <a href="{{ route('tickets.create') }}" class="w-full bg-[#EA580C] hover:bg-[#C2410C] text-white py-2.5 px-4 rounded-lg flex justify-center items-center font-bold text-sm shadow-md transition-all duration-150 hover:-translate-y-px active:translate-y-0 gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                Crear Nuevo Ticket
                            </a>
                        </div>
                    </div>

                    <!-- Tarjeta 2: Summary panel con bloques redondeados -->
                    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-6">
                        <h3 class="text-sm font-bold text-slate-800 mb-4 tracking-tight uppercase">Tu Resumen</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-4">
                            
                            <!-- Bloque Activo -->
                            <div class="bg-slate-50/50 hover:bg-slate-50 border border-slate-100 hover:border-slate-200 rounded-xl p-4 text-center transition duration-150 group">
                                <div class="flex items-center justify-center gap-1.5 mb-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#EA580C] animate-pulse"></span>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Activos</p>
                                </div>
                                <p class="text-3xl font-extrabold text-[#EA580C] tracking-tight transition duration-150 group-hover:scale-105 inline-block">{{ $activeCount }}</p>
                            </div>
                            
                            <!-- Bloque Resuelto -->
                            <div class="bg-slate-50/50 hover:bg-slate-50 border border-slate-100 hover:border-slate-200 rounded-xl p-4 text-center transition duration-150 group">
                                <div class="flex items-center justify-center gap-1.5 mb-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Resueltos</p>
                                </div>
                                <p class="text-3xl font-extrabold text-emerald-600 tracking-tight transition duration-150 group-hover:scale-105 inline-block">{{ $resolvedTotalCount }}</p>
                            </div>
                            
                        </div>
                    </div>

                </div>

            </div>

        @endif

    </div>
</div>
@endsection