@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-synapso-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ── HEADER ── --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Gestión de Usuarios</h1>
                <p class="mt-2 text-sm text-slate-500 font-medium">Administra los accesos y roles del sistema.</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center gap-2 bg-synapso-gold hover:bg-synapso-amber text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-px active:translate-y-0 self-start md:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Usuario
            </a>
        </div>

        {{-- ── TARJETAS DE RESUMEN ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">

            {{-- Total activos --}}
            <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between h-32">
                <div class="absolute -right-5 -top-5 w-20 h-20 rounded-full bg-emerald-50/60 pointer-events-none"></div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Usuarios Activos</p>
                <div class="flex items-end justify-between mt-2">
                    <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ $totalActive }}</span>
                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total suspendidos --}}
            <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between h-32">
                <div class="absolute -right-5 -top-5 w-20 h-20 rounded-full bg-red-50/60 pointer-events-none"></div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Usuarios Suspendidos</p>
                <div class="flex items-end justify-between mt-2">
                    <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ $totalSuspended }}</span>
                    <div class="w-10 h-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center shadow-sm flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total general --}}
            <div class="relative overflow-hidden bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between h-32">
                <div class="absolute -right-5 -top-5 w-20 h-20 rounded-full bg-blue-50/60 pointer-events-none"></div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total en el Sistema</p>
                <div class="flex items-end justify-between mt-2">
                    <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ $totalActive + $totalSuspended }}</span>
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── TABLA DE USUARIOS ── --}}
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">

            {{-- Cabecera de la tabla con filtros integrados --}}
            <div class="px-6 py-5 border-b border-slate-100 bg-white">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-base font-bold text-slate-800 tracking-tight">Directorio de Usuarios</h2>

                    <form id="users-filter-form" method="GET" action="{{ route('admin.users') }}"
                        class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">

                        {{-- Búsqueda --}}
                        <div class="relative flex-1 sm:w-64">
                            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                                </svg>
                            </span>
                            <input id="users-search-input" type="text" name="search" value="{{ request('search') }}"
                                placeholder="Buscar usuario..." autocomplete="off"
                                class="w-full border border-slate-200 bg-slate-50 rounded-lg pl-8 pr-3 py-1.5 text-sm focus:ring-1 focus:ring-synapso-navy focus:border-synapso-navy focus:bg-white transition-colors">
                        </div>

                        {{-- Filtro rol --}}
                        <select name="role"
                            class="w-36 border border-slate-200 bg-slate-50 rounded-lg px-3 py-1.5 text-xs font-bold text-slate-600 focus:ring-1 focus:ring-synapso-navy focus:border-synapso-navy transition-colors cursor-pointer"
                            onchange="this.form.submit()">
                            <option value="">Todos los roles</option>
                            <option value="admin"  {{ request('role') === 'admin'  ? 'selected' : '' }}>Admin</option>
                            <option value="agent"  {{ request('role') === 'agent'  ? 'selected' : '' }}>Agente</option>
                            <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>Cliente</option>
                        </select>

                        {{-- Filtro estado --}}
                        <select name="status"
                            class="w-36 border border-slate-200 bg-slate-50 rounded-lg px-3 py-1.5 text-xs font-bold text-slate-600 focus:ring-1 focus:ring-synapso-navy focus:border-synapso-navy transition-colors cursor-pointer"
                            onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Activos</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspendidos</option>
                        </select>

                        @if(request()->hasAny(['search', 'role', 'status']))
                            <a href="{{ route('admin.users') }}"
                                class="inline-flex items-center gap-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-colors duration-150">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Limpiar
                            </a>
                        @endif

                    </form>
                </div>

                {{-- Pills de filtros activos --}}
                @if(request()->hasAny(['search', 'role', 'status']))
                    <div class="flex flex-wrap gap-2 mt-3">
                        @if(request('search'))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                                </svg>
                                "{{ request('search') }}"
                            </span>
                        @endif
                        @if(request('role'))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-100 text-xs font-semibold">
                                Rol: {{ ucfirst(request('role') === 'agent' ? 'Agente' : (request('role') === 'client' ? 'Cliente' : 'Admin')) }}
                            </span>
                        @endif
                        @if(request('status'))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ request('status') === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-600 border border-red-100' }} text-xs font-semibold">
                                {{ request('status') === 'active' ? 'Activos' : 'Suspendidos' }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Tabla --}}
            <div class="overflow-x-auto">
                @php
                    $roleClasses = [
                        'admin'  => 'bg-amber-50 text-amber-700 border border-amber-100',
                        'agent'  => 'bg-blue-50 text-blue-700 border border-blue-100',
                        'client' => 'bg-slate-100 text-slate-600 border border-slate-200',
                    ];
                    $roleLabels = [
                        'admin'  => 'Admin',
                        'agent'  => 'Agente',
                        'client' => 'Cliente',
                    ];
                    $avatarColors = [
                        'admin'  => 'bg-amber-100 text-amber-700',
                        'agent'  => 'bg-blue-100 text-blue-700',
                        'client' => 'bg-slate-200 text-slate-600',
                    ];
                @endphp

                @if($users->isEmpty())
                    {{-- ESTADO VACÍO --}}
                    <div class="p-16 text-center">
                        <div class="flex flex-col items-center gap-4 text-slate-400">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-600 text-sm">No se encontraron usuarios</p>
                                <p class="text-xs mt-0.5">Ajusta los filtros para ver resultados</p>
                            </div>
                            @if(request()->hasAny(['search', 'role', 'status']))
                                <a href="{{ route('admin.users') }}"
                                    class="text-xs text-blue-600 hover:underline font-bold">
                                    Limpiar filtros
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Usuario</th>
                                <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rol</th>
                                <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tickets</th>
                                <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Miembro desde</th>
                                <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @foreach ($users as $user)
                                <tr class="{{ $user->trashed() ? 'bg-slate-50/70' : 'hover:bg-slate-50' }} transition duration-100">

                                    {{-- USUARIO --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm {{ $avatarColors[$user->role] ?? 'bg-slate-200 text-slate-600' }} {{ $user->trashed() ? 'opacity-50' : '' }}">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-slate-800 {{ $user->trashed() ? 'line-through text-slate-400' : '' }} truncate">
                                                    {{ $user->name }}
                                                    @if($user->id === auth()->id())
                                                        <span class="ml-1 text-xs font-normal text-slate-400">(tú)</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- ROL --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->id === auth()->id())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold tracking-wider {{ $roleClasses[$user->role] ?? 'bg-slate-100 text-slate-600' }}">
                                                {{ strtoupper($roleLabels[$user->role] ?? $user->role) }}
                                            </span>
                                        @else
                                            <form action="{{ route('admin.users.updateRole', $user) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role"
                                                    onchange="this.form.submit()"
                                                    {{ $user->trashed() ? 'disabled' : '' }}
                                                    class="border border-slate-200 bg-slate-50 text-slate-700 text-xs font-bold rounded-lg py-1.5 pl-3 pr-7 hover:bg-slate-100 focus:border-synapso-navy focus:ring-1 focus:ring-synapso-navy transition-all shadow-sm cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                                                    <option value="admin"  @selected($user->role === 'admin')>Admin</option>
                                                    <option value="agent"  @selected($user->role === 'agent')>Agente</option>
                                                    <option value="client" @selected($user->role === 'client')>Cliente</option>
                                                </select>
                                            </form>
                                        @endif
                                    </td>

                                    {{-- TICKETS --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-sm font-medium text-slate-600">
                                                <span class="font-extrabold text-slate-800">{{ $user->tickets_count }}</span>
                                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider ml-1">creados</span>
                                            </span>
                                            @if($user->role === 'agent')
                                                <span class="text-sm font-medium text-slate-600">
                                                    <span class="font-extrabold text-slate-800">{{ $user->assigned_tickets_count }}</span>
                                                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider ml-1">asignados</span>
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- MIEMBRO DESDE --}}
                                    <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                        <span class="text-sm font-medium text-slate-500">{{ $user->created_at->format('d/m/Y') }}</span>
                                    </td>

                                    {{-- ESTADO --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->trashed())
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold tracking-wider bg-red-50 text-red-600 border border-red-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                SUSPENDIDO
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                ACTIVO
                                            </span>
                                        @endif
                                    </td>

                                    {{-- ACCIONES --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-1">

                                            @if($user->trashed())
                                                {{-- RESTAURAR --}}
                                                <form action="{{ route('admin.users.restore', $user) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" title="Restaurar acceso"
                                                        onclick="return confirm('¿Restaurar el acceso de {{ addslashes($user->name) }}?')"
                                                        class="text-slate-400 hover:text-emerald-600 transition inline-flex items-center justify-center p-2 rounded-lg hover:bg-emerald-50">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                @if($user->id === auth()->id())
                                                    <button type="button" disabled title="No puedes suspenderte a ti mismo"
                                                        class="text-slate-200 cursor-not-allowed inline-flex items-center justify-center p-2 rounded-lg">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                    </button>
                                                @else
                                                    {{-- SUSPENDER --}}
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Suspender usuario"
                                                            onclick="return confirm('¿Suspender a {{ addslashes($user->name) }}? Perderá acceso al sistema.')"
                                                            class="text-slate-400 hover:text-red-500 transition inline-flex items-center justify-center p-2 rounded-lg hover:bg-red-50">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif

                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Footer de la tabla: conteo + paginación --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-white flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-xs font-semibold text-slate-400">
                    Mostrando {{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }} usuarios
                </span>
                <div>
                    {{ $users->links() }}
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Debounce + autofocus al recargar --}}
<script>
    (function () {
        const input = document.getElementById('users-search-input');
        const form  = document.getElementById('users-filter-form');
        let timer;

        if (!input || !form) return;

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () { form.submit(); }, 350);
        });

        if (input.value.length > 0) {
            input.focus();
            const len = input.value.length;
            input.setSelectionRange(len, len);
        }
    })();
</script>

@endsection