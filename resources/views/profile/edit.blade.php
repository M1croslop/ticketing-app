@extends('layouts.app')

@section('content')

    @php
        $user = auth()->user();
        $avatar = strtoupper(substr($user->name, 0, 1));

        // Role display
        $roleLabels = ['admin' => 'Admin', 'agent' => 'Agente', 'user' => 'Usuario'];
        $roleLabel = $roleLabels[$user->role] ?? ucfirst($user->role ?? 'Usuario');

        // Role badge
        $roleBadgeClass = match ($user->role) {
            'admin' => 'bg-synapso-gold text-white',
            'agent' => 'bg-synapso-status-progress-bg text-synapso-status-progress-text',
            default => 'bg-slate-200 text-slate-700',
        };

        // Membership date
        $memberSince = $user->created_at->locale('es')->translatedFormat('F Y');

        // Stats
        $statCount = match ($user->role) {
            'admin' => \App\Models\Ticket::count(),
            'agent' => \App\Models\Ticket::where('agent_id', $user->id)->count(),
            default => \App\Models\Ticket::where('user_id', $user->id)->count(),
        };

        $statLabel = match ($user->role) {
            'admin' => 'Tickets totales',
            'agent' => 'Tickets asignados',
            default => 'Mis tickets',
        };

        $statIcon = match ($user->role) {
            'admin' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'agent' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            default => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z',
        };
    @endphp

    <div class="max-w-4xl mx-auto p-6">

        {{-- Header de perfil --}}
        <div class="bg-synapso-navy rounded-2xl p-6 mb-6 flex flex-col sm:flex-row items-center sm:items-start gap-5">

            {{-- Avatar grande --}}
            <div class="w-20 h-20 rounded-full bg-synapso-gold flex-shrink-0 flex items-center justify-center
                        text-white text-3xl font-bold shadow-lg ring-4 ring-white/20">
                {{ $avatar }}
            </div>

            {{-- Datos del usuario --}}
            <div class="flex-1 text-center sm:text-left">
                <h1 class="text-2xl font-bold text-white leading-tight">{{ $user->name }}</h1>
                <p class="text-slate-400 text-sm mt-0.5">{{ $user->email }}</p>

                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-3">
                    {{-- Badge de rol --}}
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $roleBadgeClass }}">
                        {{ $roleLabel }}
                    </span>
                    {{-- Fecha de registro --}}
                    <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Miembro desde {{ $memberSince }}
                    </span>
                </div>
            </div>

            {{-- Tarjeta de estadística --}}
            <div class="flex-shrink-0 bg-white/10 rounded-xl px-5 py-4 flex items-center gap-3 min-w-[140px]">
                <div class="w-10 h-10 rounded-lg bg-synapso-gold/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-synapso-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statIcon }}" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white leading-none">{{ $statCount }}</p>
                    <p class="text-xs text-slate-400 mt-0.5 whitespace-nowrap">{{ $statLabel }}</p>
                </div>
            </div>

        </div>

        {{-- Secciones de formulario --}}
        <div class="space-y-6">

            {{-- 1. Información personal --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

                {{-- Encabezado de sección --}}
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-lg bg-synapso-status-progress-bg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-synapso-status-progress-text" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-synapso-navy">Información personal</h2>
                        <p class="text-xs text-slate-500">Actualiza tu nombre y correo electrónico.</p>
                    </div>
                </div>

                <div class="px-6 py-5">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- 2. Seguridad --}}
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-lg bg-synapso-priority-low-bg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-synapso-priority-low-text" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-synapso-navy">Contraseña</h2>
                        <p class="text-xs text-slate-500">Usa una contraseña segura de al menos 8 caracteres.</p>
                    </div>
                </div>

                <div class="px-6 py-5">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>

@endsection