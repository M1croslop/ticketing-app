@extends('layouts.app')

@section('content')
@php
    use Carbon\Carbon;

    $user      = auth()->user();
    $userRole  = strtolower($user->role);
    $isAdmin   = $userRole === 'admin';
    $isAgent   = $userRole === 'agent';
    $isClient  = $userRole === 'client';
    $isOwner   = $user->id === $ticket->user_id;

    //  Badge maps 
    $priorityMeta = [
        'urgent' => ['label' => 'CRÍTICO',  'dot' => 'bg-pink-500',    'badge' => 'bg-synapso-priority-urgent-bg text-synapso-priority-urgent-text border border-pink-200'],
        'high'   => ['label' => 'ALTA',     'dot' => 'bg-red-500',     'badge' => 'bg-synapso-priority-high-bg text-synapso-priority-high-text border border-red-200'],
        'medium' => ['label' => 'MEDIA',    'dot' => 'bg-amber-400',   'badge' => 'bg-synapso-priority-mid-bg text-synapso-priority-mid-text border border-amber-200'],
        'low'    => ['label' => 'BAJA',     'dot' => 'bg-blue-400',    'badge' => 'bg-synapso-priority-low-bg text-synapso-priority-low-text border border-blue-200'],
    ];
    $statusMeta = [
        'open'        => ['label' => 'Abierto',     'badge' => 'bg-synapso-status-open-bg text-synapso-status-open-text border border-purple-200',     'dot' => 'bg-purple-500'],
        'in_progress' => ['label' => 'En Progreso', 'badge' => 'bg-synapso-status-progress-bg text-synapso-status-progress-text border border-blue-200', 'dot' => 'bg-blue-500'],
        'resolved'    => ['label' => 'Resuelto',    'badge' => 'bg-synapso-status-done-bg text-synapso-status-done-text border border-emerald-200',      'dot' => 'bg-emerald-500'],
        'closed'      => ['label' => 'Cerrado',     'badge' => 'bg-slate-100 text-slate-600 border border-slate-200',                                     'dot' => 'bg-slate-400'],
    ];

    $pb = $priorityMeta[$ticket->priority] ?? ['label' => $ticket->priority, 'dot' => 'bg-slate-400', 'badge' => 'bg-slate-100 text-slate-600 border border-slate-200'];
    $sb = $statusMeta[$ticket->status]     ?? ['label' => $ticket->status,   'dot' => 'bg-slate-400', 'badge' => 'bg-slate-100 text-slate-600 border border-slate-200'];

    // Ticket ID display
    $prefix  = in_array($ticket->priority, ['urgent','high']) ? 'INC' : 'TK';
    $ticketId = '#' . $prefix . '-' . sprintf('%04d', $ticket->id);

    // SLA calculations
    $isActive  = in_array($ticket->status, ['open','in_progress']);
    $isOverdue = $isActive && $ticket->due_date && $ticket->due_date->isPast();
    $slaOk     = $ticket->due_date && !$ticket->due_date->isPast();

    // Time open
    $openedAt     = $ticket->created_at;
    $openMinutes  = $openedAt->diffInMinutes(now());
    $openHours    = floor($openMinutes / 60);
    $openMins     = $openMinutes % 60;
    $timeOpenStr  = $openHours > 0 ? $openHours . 'h ' . $openMins . 'm' : $openMins . 'm';

    // Avatar initials helper
    $initials = fn(string $name) => strtoupper(substr($name, 0, 1));

    // Avatar color palette (deterministic by user id)
    $avatarColors = ['bg-indigo-500','bg-violet-500','bg-sky-500','bg-teal-500','bg-amber-500','bg-rose-500','bg-emerald-500','bg-fuchsia-500'];
    $avatarColor  = fn(int $id) => $avatarColors[$id % count($avatarColors)];

    // Can agent self-assign?
    $canTake = $isAgent && is_null($ticket->agent_id);

    // Activity log (status history) sorted newest first
    $activityLogs = $ticket->statusLogs->sortByDesc('changed_at');

    // Status log action descriptions
    $statusLabels = [
        'open'        => 'Abierto',
        'in_progress' => 'En Progreso',
        'resolved'    => 'Resuelto',
        'closed'      => 'Cerrado',
    ];

    // Comment count
    $commentCount = $ticket->comments->count();
@endphp

<div class="min-h-screen bg-synapso-bg py-6">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{--  Breadcrumb  --}}
    <div class="mb-5 flex items-center gap-2 text-xs font-semibold text-slate-400">
        <a href="{{ route('tickets.index') }}"
           class="flex items-center gap-1.5 hover:text-synapso-gold transition-colors duration-150">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
            @if($isClient) Portal del Cliente @elseif($isAgent) Mis Tickets @else Todos los Tickets @endif
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-slate-500 font-bold">{{ $ticketId }}</span>
    </div>

    {{-- 
         HEADER CARD — title + inline controls
     --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-5 overflow-hidden">

        {{-- Top header bar --}}
        <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap gap-4 items-start justify-between">
            {{-- Title block --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-[10px] font-black text-synapso-gold uppercase tracking-widest">{{ $ticketId }}</span>
                    {{-- Priority badge (always visible static for non-admin) --}}
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide {{ $pb['badge'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $pb['dot'] }}"></span>
                        {{ $pb['label'] }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide {{ $sb['badge'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $sb['dot'] }}"></span>
                        {{ $sb['label'] }}
                    </span>
                </div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight leading-tight">{{ $ticket->title }}</h1>
                <p class="text-xs text-slate-400 mt-1">
                    Creado {{ $ticket->created_at->diffForHumans() }}
                    @if($ticket->created_at->diffInDays(now()) > 0)
                        · {{ $ticket->created_at->format('d/m/Y H:i') }}
                    @endif
                </p>
            </div>

            {{-- Inline action controls (Admin only) --}}
            @if($isAdmin)
            <div class="flex flex-wrap items-center gap-2 shrink-0">

                {{-- STATUS inline select --}}
                <form action="{{ route('tickets.update', $ticket) }}" method="POST" id="form-status-{{ $ticket->id }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="_redirect_back" value="1">
                    <div class="relative"
                         x-data="{
                            open: false,
                            current: '{{ $ticket->status }}',
                            labels: { open:'Abierto', in_progress:'En Progreso', resolved:'Resuelto', closed:'Cerrado' },
                            dots: { open:'bg-purple-500', in_progress:'bg-blue-500', resolved:'bg-emerald-500', closed:'bg-slate-400' },
                            select(val) {
                                this.current = val; this.open = false;
                                this.$refs.statusInput.value = val;
                                this.$refs.statusInput.form.submit();
                            }
                         }">
                        <input type="hidden" name="status" x-ref="statusInput" value="{{ $ticket->status }}">
                        <button type="button"
                                @click="open = !open"
                                class="flex items-center gap-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700
                                       px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-all duration-150">
                            <span class="w-2 h-2 rounded-full"
                                  :class="dots[current]"></span>
                            <span x-text="labels[current]"></span>
                            <svg class="w-3 h-3 text-slate-400 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                             @click.outside="open = false"
                             class="absolute right-0 mt-1 w-40 bg-white border border-slate-200 rounded-xl shadow-lg z-30 overflow-hidden">
                            @foreach(['open' => 'Abierto', 'in_progress' => 'En Progreso', 'resolved' => 'Resuelto', 'closed' => 'Cerrado'] as $val => $lbl)
                            <button type="button" @click="select('{{ $val }}')"
                                    class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-700
                                           hover:bg-slate-50 transition-colors duration-100 {{ $ticket->status === $val ? 'bg-slate-50 text-indigo-600' : '' }}">
                                <span class="w-1.5 h-1.5 rounded-full
                                    {{ $val === 'open' ? 'bg-purple-500' : ($val === 'in_progress' ? 'bg-blue-500' : ($val === 'resolved' ? 'bg-emerald-500' : 'bg-slate-400')) }}">
                                </span>
                                {{ $lbl }}
                                @if($ticket->status === $val)
                                    <svg class="w-3 h-3 ml-auto text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @endif
                            </button>
                            @endforeach
                        </div>
                    </div>
                </form>

                {{-- PRIORITY inline select (Admin only) --}}
                @if($isAdmin)
                <form action="{{ route('tickets.update', $ticket) }}" method="POST" id="form-priority-{{ $ticket->id }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="_redirect_back" value="1">
                    <div class="relative"
                         x-data="{
                            open: false,
                            current: '{{ $ticket->priority }}',
                            labels: { urgent:'CRÍTICO', high:'ALTA', medium:'MEDIA', low:'BAJA' },
                            dots: { urgent:'bg-pink-500', high:'bg-red-500', medium:'bg-amber-400', low:'bg-blue-400' },
                            select(val) {
                                this.current = val; this.open = false;
                                this.$refs.priorityInput.value = val;
                                this.$refs.priorityInput.form.submit();
                            }
                         }">
                        <input type="hidden" name="priority" x-ref="priorityInput" value="{{ $ticket->priority }}">
                        <button type="button"
                                @click="open = !open"
                                class="flex items-center gap-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700
                                       px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-all duration-150">
                            <span class="w-2 h-2 rounded-full" :class="dots[current]"></span>
                            <span x-text="labels[current]"></span>
                            <svg class="w-3 h-3 text-slate-400 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                             @click.outside="open = false"
                             class="absolute right-0 mt-1 w-36 bg-white border border-slate-200 rounded-xl shadow-lg z-30 overflow-hidden">
                            @foreach(['urgent' => 'CRÍTICO', 'high' => 'ALTA', 'medium' => 'MEDIA', 'low' => 'BAJA'] as $val => $lbl)
                            <button type="button" @click="select('{{ $val }}')"
                                    class="w-full flex items-center gap-2 px-3 py-2 text-xs font-bold text-slate-700
                                           hover:bg-slate-50 transition-colors duration-100 {{ $ticket->priority === $val ? 'bg-slate-50' : '' }}">
                                <span class="w-1.5 h-1.5 rounded-full
                                    {{ $val === 'urgent' ? 'bg-pink-500' : ($val === 'high' ? 'bg-red-500' : ($val === 'medium' ? 'bg-amber-400' : 'bg-blue-400')) }}">
                                </span>
                                {{ $lbl }}
                                @if($ticket->priority === $val)
                                    <svg class="w-3 h-3 ml-auto text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @endif
                            </button>
                            @endforeach
                        </div>
                    </div>
                </form>
                @endif

                {{-- Admin: Delete button --}}
                @if($isAdmin)
                <form action="{{ route('tickets.destroy', $ticket) }}" method="POST"
                      x-data
                      @submit.prevent="$dispatch('open-confirm-modal', {
                          title: 'Eliminar Ticket',
                          message: '¿Seguro que deseas enviar este ticket a la papelera? Esta acción puede revertirse.',
                          confirmText: 'Sí, eliminar',
                          confirmButtonClass: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
                          onConfirm: () => $el.submit()
                      })">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="flex items-center gap-1.5 border border-red-200 bg-red-50 hover:bg-red-100 text-red-600
                                   px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-all duration-150">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar
                    </button>
                </form>
                @endif

            </div>
            @endif

            {{-- Agent: Take ticket button --}}
            @if($canTake)
            <form action="{{ route('tickets.take', $ticket) }}" method="POST">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
                               px-4 py-2 rounded-lg text-sm font-bold shadow-md transition-all duration-150
                               hover:shadow-lg hover:-translate-y-px active:translate-y-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Tomar Ticket
                </button>
            </form>
            @endif

            {{-- Agent: Close ticket quick action (only their assigned tickets) --}}
            @if($isAgent && $ticket->agent_id === $user->id && !in_array($ticket->status, ['resolved','closed']))
            <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="resolved">
                <input type="hidden" name="_redirect_back" value="1">
                <button type="submit"
                        class="inline-flex items-center gap-2 border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-700
                               px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Resolver Ticket
                </button>
            </form>
            @endif

            {{-- Client: Close ticket quick action --}}
            @if($isOwner && !in_array($ticket->status, ['resolved', 'closed']))
            <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="closed">
                <input type="hidden" name="_redirect_back" value="1">
                <button type="submit"
                        class="inline-flex items-center gap-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-700
                               px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Cerrar Ticket
                </button>
            </form>
            @endif

        </div>

        {{--  Body: Description + Sidebar  --}}
        <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- DESCRIPTION column --}}
            <div class="lg:col-span-2 space-y-4">
                <div>
                    <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Descripción</h2>
                    <div class="bg-slate-50 rounded-xl border border-slate-100 p-4">
                        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $ticket->description }}</p>
                    </div>
                </div>

                {{-- Resolved timestamp notice --}}
                @if($ticket->resolved_at)
                <div class="flex items-center gap-2 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Resuelto el {{ $ticket->resolved_at->format('d/m/Y') }} a las {{ $ticket->resolved_at->format('H:i') }}
                    · {{ $ticket->resolved_at->diffForHumans() }}
                </div>
                @endif

                {{-- SLA warning --}}
                @if($isOverdue)
                <div class="flex items-center gap-2 text-xs font-bold text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2 animate-pulse">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    SLA VENCIDO — Venció {{ $ticket->due_date->diffForHumans() }}
                </div>
                @endif
            </div>

            {{-- META SIDEBAR --}}
            <div class="space-y-5">

                {{-- Created by --}}
                <div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Creado por</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $avatarColor($ticket->user->id) }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ $initials($ticket->user->name) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $ticket->user->name }}</p>
                            <p class="text-[10px] text-slate-400">{{ $ticket->created_at->format('d/m/Y · H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Assigned to --}}
                <div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Asignado a</h3>
                    @if($isAdmin)
                        <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="_redirect_back" value="1">
                            <div class="relative"
                                 x-data="{
                                     open: false,
                                     currentName: '{{ $ticket->agent?->name ?? 'Sin asignar' }}',
                                     select(id, name) {
                                         this.currentName = name; this.open = false;
                                         this.$refs.agentInput.value = id;
                                         this.$refs.agentInput.form.submit();
                                     }
                                 }">
                                <input type="hidden" name="agent_id" x-ref="agentInput" value="{{ $ticket->agent_id ?? '' }}">
                                <button type="button"
                                        @click="open = !open"
                                        class="w-full flex items-center justify-between gap-2 border border-slate-200 bg-white
                                               hover:bg-slate-50 text-slate-700 px-3 py-2 rounded-lg text-sm font-semibold
                                               shadow-sm transition-all duration-150">
                                    <span class="flex items-center gap-2">
                                        @if($ticket->agent)
                                        <span class="w-6 h-6 rounded-full {{ $avatarColor($ticket->agent->id) }} flex items-center justify-center text-white text-[10px] font-bold">
                                            {{ $initials($ticket->agent->name) }}
                                        </span>
                                        @else
                                        <span class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-slate-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </span>
                                        @endif
                                        <span x-text="currentName" class="truncate max-w-[120px]"></span>
                                    </span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-cloak
                                     @click.outside="open = false"
                                     class="absolute top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-30 max-h-52 overflow-y-auto">
                                    <button type="button" @click="select('', 'Sin asignar')"
                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors duration-100 border-b border-slate-100">
                                        <span class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-[10px]">—</span>
                                        Sin asignar
                                    </button>
                                    @foreach($agents as $agent)
                                    <button type="button" @click="select('{{ $agent->id }}', '{{ addslashes($agent->name) }}')"
                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-sm font-semibold text-slate-700
                                                   hover:bg-slate-50 transition-colors duration-100
                                                   {{ $ticket->agent_id === $agent->id ? 'bg-indigo-50 text-indigo-700' : '' }}">
                                        <span class="w-6 h-6 rounded-full {{ $avatarColor($agent->id) }} flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                                            {{ $initials($agent->name) }}
                                        </span>
                                        <span class="truncate">{{ $agent->name }}</span>
                                        @if($ticket->agent_id === $agent->id)
                                            <svg class="w-3.5 h-3.5 ml-auto text-indigo-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    @else
                        @if($ticket->agent)
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full {{ $avatarColor($ticket->agent->id) }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ $initials($ticket->agent->name) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $ticket->agent->name }}</p>
                                <p class="text-[10px] text-slate-400">{{ ucfirst($ticket->agent->role) }}</p>
                            </div>
                        </div>
                        @else
                        <p class="text-sm text-slate-400 italic">Pendiente de asignación</p>
                        @endif
                    @endif
                </div>

                {{-- Category --}}
                <div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Categoría</h3>
                    @if($isAdmin)
                        <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="_redirect_back" value="1">
                            <select name="category_id" onchange="this.form.submit()"
                                    class="w-full border border-slate-200 bg-white text-slate-700 text-xs font-semibold
                                           rounded-lg py-1.5 pl-3 pr-8 hover:bg-slate-50 focus:outline-none
                                           focus:ring-2 focus:ring-indigo-300 transition cursor-pointer shadow-sm">
                                <option value="">General</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $ticket->category_id === $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 rounded-lg border border-slate-200">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span class="text-xs font-semibold text-slate-700">{{ $ticket->category?->name ?? 'General' }}</span>
                        </div>
                    @endif
                </div>

                {{-- SLA / Due date --}}
                <div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Fecha Límite SLA</h3>
                    @if($ticket->due_date)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 {{ $isOverdue ? 'text-red-500' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold {{ $isOverdue ? 'text-red-600' : 'text-slate-800' }}">
                                    {{ $ticket->due_date->format('d/m/Y H:i') }}
                                </p>
                                <p class="text-[10px] font-semibold {{ $isOverdue ? 'text-red-500' : 'text-emerald-600' }}">
                                    {{ $isOverdue ? 'Vencido ' . $ticket->due_date->diffForHumans() : 'Vence ' . $ticket->due_date->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Se asignará al designar un agente</p>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- 
         MAIN GRID: Comments + Activity Sidebar
     --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{--  LEFT: Comments thread  --}}
        <div class="lg:col-span-2">

            {{-- Comment thread header --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <h2 class="text-sm font-bold text-slate-800">Hilo de Comentarios</h2>
                    </div>
                    <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                        {{ $commentCount }} {{ $commentCount === 1 ? 'mensaje' : 'mensajes' }}
                    </span>
                </div>

                {{-- Comments list --}}
                <div class="p-5 space-y-4">
                    @if($ticket->comments->isEmpty())
                    <div class="py-10 text-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-slate-400 font-medium">Sin comentarios todavía</p>
                        <p class="text-xs text-slate-300 mt-1">Sé el primero en responder este ticket</p>
                    </div>
                    @else
                    @foreach($ticket->comments as $comment)
                    @php
                        $isMine = $comment->user_id === $user->id;
                        $commentorRole = strtolower($comment->user->role ?? 'client');
                        $isAgentComment = in_array($commentorRole, ['agent', 'admin']);
                    @endphp
                    <div class="flex gap-3 {{ $isMine ? 'flex-row-reverse' : '' }}">
                        {{-- Avatar --}}
                        <div class="flex-shrink-0">
                            <div class="w-9 h-9 rounded-full {{ $avatarColor($comment->user->id ?? 0) }} flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                {{ $initials($comment->user->name ?? 'Usuario Eliminado') }}
                            </div>
                        </div>

                        {{-- Bubble --}}
                        <div class="max-w-[75%] group">
                            <div class="{{ $isMine
                                ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-sm'
                                : 'bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-tl-sm shadow-sm' }} p-3.5">

                                {{-- Header --}}
                                <div class="flex items-center gap-2 mb-1.5 {{ $isMine ? 'flex-row-reverse' : '' }}">
                                    <span class="text-xs font-bold {{ $isMine ? 'text-indigo-200' : 'text-slate-900' }}">
                                        {{ $comment->user->name ?? 'Usuario Eliminado' }}
                                        @if($isMine) <span class="font-normal opacity-70">(Tú)</span> @endif
                                    </span>
                                    @if($isAgentComment && !$isMine)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide
                                                 {{ $commentorRole === 'admin' ? 'bg-violet-100 text-violet-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $commentorRole === 'admin' ? 'Admin' : 'Agente' }}
                                    </span>
                                    @endif
                                    <span class="text-[10px] {{ $isMine ? 'text-indigo-300' : 'text-slate-400' }} ml-auto">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <p class="text-sm leading-relaxed {{ $isMine ? 'text-white' : 'text-slate-700' }}">
                                    {{ $comment->body }}
                                </p>
                            </div>

                            {{-- Delete action --}}
                            @if($isAdmin || $comment->user_id === $user->id)
                            <div class="mt-1 {{ $isMine ? 'text-right' : 'text-left' }} opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <form action="{{ route('tickets.comments.destroy', [$ticket, $comment]) }}" method="POST"
                                      x-data
                                      @submit.prevent="$dispatch('open-confirm-modal', {
                                          title: 'Eliminar Comentario',
                                          message: '¿Eliminar este comentario? Esta acción no se puede deshacer.',
                                          confirmText: 'Eliminar',
                                          confirmButtonClass: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
                                          onConfirm: () => $el.submit()
                                      })">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-[10px] font-semibold text-slate-400 hover:text-red-500 transition-colors duration-150 px-1">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>

                {{--  Reply form  --}}
                @if($isAdmin || $isAgent || $isOwner)
                <div class="px-5 pb-5"
                     x-data="{
                         body: '',
                         stayOnPage: true,
                         submitForm(el) {
                             if (!this.body.trim()) return;
                             if (!this.stayOnPage) {
                                 el.closest('form').submit();
                                 return;
                             }
                             el.closest('form').submit();
                         }
                     }">
                    <div class="border border-slate-200 rounded-2xl bg-white shadow-sm overflow-hidden">
                        <div class="flex items-center gap-2 px-4 pt-3 pb-1 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-full {{ $avatarColor($user->id) }} flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                                {{ $initials($user->name) }}
                            </div>
                            <span class="text-xs font-bold text-slate-600">Respuesta Pública</span>
                        </div>
                        <form action="{{ route('tickets.comments.store', $ticket) }}" method="POST">
                            @csrf
                            <textarea name="body"
                                      x-model="body"
                                      rows="3"
                                      placeholder="Escribe tu respuesta aquí..."
                                      class="w-full px-4 py-3 text-sm text-slate-700 placeholder-slate-400 border-0 focus:ring-0 resize-none bg-transparent"
                                      required></textarea>
                            <div class="flex items-center justify-between px-4 pb-3 pt-2 border-t border-slate-100">
                                <label class="flex items-center gap-2 text-xs text-slate-500 cursor-pointer select-none">
                                    <input type="checkbox" x-model="stayOnPage" checked
                                           class="w-3.5 h-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-400 focus:ring-offset-0">
                                    Permanecer en la página
                                </label>
                                <button type="submit"
                                        :disabled="!body.trim()"
                                        :class="body.trim()
                                            ? 'bg-slate-900 hover:bg-slate-700 cursor-pointer shadow-md'
                                            : 'bg-slate-300 cursor-not-allowed'"
                                        class="inline-flex items-center gap-2 text-white px-4 py-2 rounded-xl text-xs font-bold
                                               transition-all duration-150">
                                    Enviar Comentario
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Los comentarios son visibles para el equipo de soporte técnico.
                    </p>
                </div>
                @else
                <div class="px-5 pb-5">
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center text-xs font-semibold text-amber-700">
                        No tienes permisos para comentar en este ticket.
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{--  RIGHT SIDEBAR: Activity + SLA panel  --}}
        <div class="space-y-5">

            {{-- Activity Register --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h2 class="text-sm font-bold text-slate-800">Registro de Actividad</h2>
                    </div>
                    @if($activityLogs->count() > 4)
                    <button type="button"
                            x-data="{ expanded: false }"
                            @click="expanded = !expanded"
                            x-text="expanded ? 'Ver menos' : 'Ver historial'"
                            class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                    </button>
                    @endif
                </div>

                <div class="p-5">
                    {{-- Ticket created entry --}}
                    <div class="relative pl-6 pb-4">
                        <div class="absolute left-0 top-1 w-4 h-4 rounded-full bg-indigo-100 border-2 border-indigo-300 flex items-center justify-center flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                        </div>
                        <div class="absolute left-[7px] top-5 bottom-0 w-px bg-slate-100"></div>
                        <p class="text-xs font-semibold text-slate-800">Ticket Creado</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            {{ $ticket->user->name }} · {{ $ticket->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    {{-- Agent assigned entry (if exists) --}}
                    @if($ticket->agent)
                    <div class="relative pl-6 pb-4">
                        <div class="absolute left-0 top-1 w-4 h-4 rounded-full bg-sky-100 border-2 border-sky-300 flex items-center justify-center flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                        </div>
                        <div class="absolute left-[7px] top-5 bottom-0 w-px bg-slate-100"></div>
                        <p class="text-xs font-semibold text-slate-800">Asignado a '{{ $ticket->agent->name }}'</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            @if($ticket->due_date)
                                SLA: {{ $ticket->due_date->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    </div>
                    @endif

                    {{-- Status change logs --}}
                    @forelse($activityLogs->take(4) as $log)
                    @php
                        $logDot = [
                            'open'        => 'bg-purple-500 border-purple-300 bg-purple-100',
                            'in_progress' => 'bg-blue-500 border-blue-300 bg-blue-100',
                            'resolved'    => 'bg-emerald-500 border-emerald-300 bg-emerald-100',
                            'closed'      => 'bg-slate-400 border-slate-300 bg-slate-100',
                        ];
                        $dotColors = $logDot[$log->new_status] ?? 'bg-slate-400 border-slate-300 bg-slate-100';
                        $parts = explode(' ', $dotColors);
                        $dotBg   = $parts[0] ?? 'bg-slate-400';
                        $dotBord = $parts[1] ?? 'border-slate-300';
                        $ringBg  = $parts[2] ?? 'bg-slate-100';
                        $isLast  = $loop->last && $activityLogs->count() <= 4;
                    @endphp
                    <div class="relative pl-6 {{ !$isLast ? 'pb-4' : '' }}">
                        <div class="absolute left-0 top-1 w-4 h-4 rounded-full {{ $ringBg }} border-2 {{ $dotBord }} flex items-center justify-center flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dotBg }}"></span>
                        </div>
                        @if(!$isLast)
                        <div class="absolute left-[7px] top-5 bottom-0 w-px bg-slate-100"></div>
                        @endif
                        <p class="text-xs font-semibold text-slate-800">
                            Estado cambiado a '{{ $statusLabels[$log->new_status] ?? $log->new_status }}'
                        </p>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            {{ $log->user?->name ?? '—' }} · {{ $log->changed_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 italic text-center py-2">Sin cambios de estado registrados</p>
                    @endforelse

                    {{-- Show more collapsed logs --}}
                    @if($activityLogs->count() > 4)
                    <div x-data="{ expanded: false }">
                        <div x-show="expanded" class="space-y-0 mt-0">
                            @foreach($activityLogs->skip(4) as $log)
                            @php
                                $isLastExtra = $loop->last;
                            @endphp
                            <div class="relative pl-6 {{ !$isLastExtra ? 'pb-4' : '' }} pt-4">
                                <div class="absolute left-[7px] bottom-0 top-0 w-px bg-slate-100"></div>
                                <div class="absolute left-0 top-5 w-4 h-4 rounded-full bg-slate-100 border-2 border-slate-200 flex items-center justify-center flex-shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                </div>
                                <p class="text-xs font-semibold text-slate-800">
                                    Estado → '{{ $statusLabels[$log->new_status] ?? $log->new_status }}'
                                </p>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $log->user?->name ?? '—' }} · {{ $log->changed_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                        <button type="button"
                                @click="expanded = !expanded"
                                class="mt-3 w-full flex items-center justify-center gap-1.5 text-[10px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors py-1.5 border border-dashed border-indigo-200 rounded-lg hover:bg-indigo-50">
                            <svg class="w-3 h-3" :class="expanded ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <span x-text="expanded ? 'Ocultar historial' : 'Ver historial completo (' + {{ $activityLogs->count() }} + ' entradas)'"></span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            {{-- SLA / Audit Panel (Admin only) --}}
            @if($isAdmin)
            <div class="bg-slate-900 rounded-2xl shadow-lg overflow-hidden text-white">
                <div class="px-5 py-4 border-b border-white/10 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <h2 class="text-sm font-bold">Panel de Auditoría</h2>
                </div>
                <div class="p-5">
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">
                        Este ticket está marcado para auditoría de cumplimiento SLA. Toda interacción está siendo registrada con sello de tiempo inmutable.
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1">Tiempo Abierto</p>
                            <p class="text-lg font-black text-white">{{ $timeOpenStr }}</p>
                        </div>
                        <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1">SLA Status</p>
                            @if(in_array($ticket->status, ['resolved','closed']))
                                <p class="text-lg font-black text-emerald-400">Cumplido</p>
                            @elseif($isOverdue)
                                <p class="text-lg font-black text-red-400">Vencido</p>
                            @elseif($ticket->due_date)
                                <p class="text-lg font-black text-emerald-400">En Plazo</p>
                            @else
                                <p class="text-lg font-black text-slate-400">Pendiente</p>
                            @endif
                        </div>
                    </div>
                    @if($ticket->due_date && $isActive)
                    <div class="mt-3 bg-white/5 rounded-xl p-3 border border-white/10">
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1">Vence</p>
                        <p class="text-sm font-semibold text-white">{{ $ticket->due_date->format('d/m/Y H:i') }}</p>
                        <p class="text-[10px] {{ $isOverdue ? 'text-red-400' : 'text-emerald-400' }} font-semibold mt-0.5">
                            {{ $ticket->due_date->diffForHumans() }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Agent SLA compact panel --}}
            @if($isAgent)
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="text-sm font-bold text-slate-800">SLA del Ticket</h2>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-slate-500">Tiempo Abierto</span>
                        <span class="text-sm font-bold text-slate-800">{{ $timeOpenStr }}</span>
                    </div>
                    @if($ticket->due_date)
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-slate-500">Fecha Límite</span>
                        <span class="text-xs font-semibold {{ $isOverdue ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ $ticket->due_date->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-slate-500">Estado SLA</span>
                        <span class="text-xs font-black {{ $isOverdue ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ $isOverdue ? '⚠ Vencido' : '✓ En Plazo' }}
                        </span>
                    </div>
                    @else
                    <p class="text-xs text-slate-400 italic">Sin fecha límite asignada</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Client info panel --}}
            @if($isClient)
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800">Estado de tu Solicitud</h2>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-slate-500">Estado actual</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $sb['badge'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sb['dot'] }}"></span>
                            {{ $sb['label'] }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-slate-500">Agente Asignado</span>
                        <span class="text-xs font-semibold text-slate-700">{{ $ticket->agent?->name ?? 'Pendiente' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-slate-500">Abierto hace</span>
                        <span class="text-xs font-semibold text-slate-700">{{ $ticket->created_at->diffForHumans(null, true) }}</span>
                    </div>
                    @if(in_array($ticket->status, ['resolved','closed']))
                    <div class="mt-2 flex items-center gap-2 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tu ticket ha sido resuelto
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
</div>
@endsection