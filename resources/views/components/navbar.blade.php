@php
    $user = Auth::user();
    $avatar = strtoupper(substr($user->name, 0, 1));
    $roleLabels = ['admin' => 'Admin', 'agent' => 'Agente', 'user' => 'Usuario'];
    $roleBadgeColors = [
        'admin' => 'bg-synapso-gold text-white',
        'agent' => 'bg-synapso-success text-white',
        'user' => 'bg-slate-500 text-white',
    ];
    $roleLabel = $roleLabels[$user->role] ?? ucfirst($user->role ?? 'Usuario');
    $roleBadge = $roleBadgeColors[$user->role] ?? 'bg-slate-500 text-white';
@endphp

<nav x-data="{ mobileOpen: false, userOpen: false }" class="bg-synapso-navy text-white shadow-lg" role="navigation"
    aria-label="Navegación principal">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- ── LADO IZQUIERDO: Logo + Links ── --}}
            <div class="flex items-center gap-8">

                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group" aria-label="Ir al inicio">
                    <div
                        class="w-8 h-8 rounded-lg bg-synapso-gold flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">Synapso</span>
                </a>

                {{-- Nav Links — Desktop --}}
                <div class="hidden md:flex items-center gap-1">

                    <a href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard') ? 'bg-white/10 text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}
                              px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-150 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                        @if(request()->routeIs('dashboard'))
                            <span class="sr-only">(página actual)</span>
                        @endif
                    </a>

                    <a href="{{ route('tickets.index') }}"
                        class="{{ request()->routeIs('tickets.*') ? 'bg-white/10 text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}
                              px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-150 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Tickets
                        @if(request()->routeIs('tickets.*'))
                            <span class="sr-only">(página actual)</span>
                        @endif
                    </a>

                    <a href="#"
                        class="text-slate-300 hover:text-white hover:bg-white/5
                              px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-150 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Reportes
                    </a>

                </div>
            </div>

            {{-- ── LADO DERECHO: Nuevo Ticket + Usuario ── --}}
            <div class="flex items-center gap-3">

                {{-- Botón Nuevo Ticket --}}
                <a href="{{ route('tickets.create') }}" class="hidden sm:flex items-center gap-2 bg-synapso-gold hover:bg-amber-600
                          text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-md
                          transition-all duration-200 hover:shadow-lg hover:-translate-y-px active:translate-y-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Ticket
                </a>

                {{-- Divider --}}
                <div class="hidden sm:block w-px h-6 bg-white/20" aria-hidden="true"></div>

                {{-- User Dropdown --}}
                <div class="relative" x-data>

                    {{-- Trigger --}}
                    <button @click="userOpen = !userOpen" class="flex items-center gap-2.5 rounded-xl px-2 py-1.5
                               hover:bg-white/10 transition-all duration-150 group" aria-haspopup="true"
                        :aria-expanded="userOpen" id="user-menu-btn">
                        {{-- Avatar --}}
                        <div class="w-9 h-9 rounded-full bg-synapso-gold flex items-center justify-center
                                    text-white text-sm font-bold shadow-sm ring-2 ring-white/20
                                    group-hover:ring-white/40 transition-all duration-150" aria-hidden="true">
                            {{ $avatar }}
                        </div>

                        {{-- Nombre + Rol — visible en md+ --}}
                        <div class="hidden md:flex flex-col items-start leading-tight">
                            <span class="text-sm font-semibold text-white">{{ $user->name }}</span>
                            <span class="text-xs text-slate-400">{{ $roleLabel }}</span>
                        </div>

                        {{-- Chevron --}}
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                            :class="userOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown Panel --}}
                    <div x-show="userOpen" @click.outside="userOpen = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 -translate-y-1" class="absolute right-0 mt-2 w-60 bg-white rounded-xl shadow-xl
                               border border-slate-100 overflow-hidden z-50" role="menu"
                        aria-labelledby="user-menu-btn" style="display: none;">
                        {{-- User Info Header --}}
                        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-synapso-gold flex items-center justify-center
                                            text-white text-sm font-bold shadow-sm flex-shrink-0" aria-hidden="true">
                                    {{ $avatar }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-synapso-navy truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                                    <span
                                        class="inline-flex items-center mt-0.5 px-2 py-0.5 rounded-full text-xs font-medium {{ $roleBadge }}">
                                        {{ $roleLabel }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Menu Items --}}
                        <div class="py-1" role="none">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700
                                      hover:bg-slate-50 hover:text-synapso-navy transition-colors duration-100"
                                role="menuitem">
                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Mi Perfil
                            </a>
                        </div>

                        {{-- Separator --}}
                        <div class="border-t border-slate-100" role="separator" aria-orientation="horizontal"></div>

                        {{-- Logout --}}
                        <div class="py-1" role="none">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-synapso-danger
                                           hover:bg-red-50 transition-colors duration-100" role="menuitem">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

                {{-- Hamburger — Mobile --}}
                <button @click="mobileOpen = !mobileOpen"
                    class="md:hidden p-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/10 transition-colors duration-150"
                    :aria-expanded="mobileOpen" aria-controls="mobile-menu" aria-label="Abrir menú">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

            </div>
        </div>
    </div>

    {{-- ── MENÚ MÓVIL ── --}}
    <div x-show="mobileOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2" id="mobile-menu"
        class="md:hidden border-t border-white/10 bg-synapso-navy" style="display: none;">
        <div class="px-4 py-3 space-y-1">

            <a href="#"
                class="{{ request()->routeIs('dashboard') ? 'bg-white/10 text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}
                      flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('tickets.index') }}"
                class="{{ request()->routeIs('tickets.*') ? 'bg-white/10 text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-white/5' }}
                      flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Tickets
            </a>

            <a href="#"
                class="text-slate-300 hover:text-white hover:bg-white/5
                      flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Reportes
            </a>

        </div>

        {{-- Mobile: Nuevo Ticket + separador --}}
        <div class="px-4 pb-4 pt-1 border-t border-white/10 mt-1">
            <a href="{{ route('tickets.create') }}" class="flex items-center justify-center gap-2 w-full bg-synapso-gold hover:bg-amber-600
                      text-white px-4 py-2.5 rounded-lg text-sm font-semibold shadow transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Ticket
            </a>
        </div>

    </div>

</nav>