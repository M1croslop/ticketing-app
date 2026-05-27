@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-synapso-bg py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="mb-8">
            <a href="{{ route('admin.users') }}"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Nuevo Usuario</h1>
            <p class="mt-2 text-sm text-slate-500 font-medium">Registra un nuevo acceso al sistema.</p>
        </div>

        {{-- ALERT DE ERRORES --}}
        @if($errors->any())
            <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3.5">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-semibold text-red-700">Por favor corrige los errores antes de continuar.</p>
            </div>
        @endif

        {{-- CARD FORMULARIO --}}
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">

            {{-- Card header --}}
            <div class="px-6 py-5 border-b border-slate-100 bg-white">
                <h2 class="text-base font-bold text-slate-800 tracking-tight">Datos del Usuario</h2>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="px-6 py-6">
                @csrf

                <div class="space-y-6">

                    {{-- NOMBRE --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-synapso-navy mb-1.5">
                            Nombre completo
                            <span class="text-synapso-danger">*</span>
                        </label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ej. María García"
                            autocomplete="name"
                            class="w-full border {{ $errors->has('name') ? 'border-synapso-danger' : 'border-slate-200' }} bg-slate-50 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-synapso-navy focus:border-synapso-navy focus:bg-white transition-colors">
                        @error('name')
                            <p class="text-synapso-danger text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- CORREO --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-synapso-navy mb-1.5">
                            Correo electrónico
                            <span class="text-synapso-danger">*</span>
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="usuario@empresa.com"
                            autocomplete="email"
                            class="w-full border {{ $errors->has('email') ? 'border-synapso-danger' : 'border-slate-200' }} bg-slate-50 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-synapso-navy focus:border-synapso-navy focus:bg-white transition-colors">
                        @error('email')
                            <p class="text-synapso-danger text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ROL con Alpine.js --}}
                    <div x-data="{ role: '{{ old('role', 'client') }}' }">
                        <label for="role" class="block text-sm font-semibold text-synapso-navy mb-1.5">
                            Rol en el sistema
                            <span class="text-synapso-danger">*</span>
                        </label>
                        <select
                            id="role"
                            name="role"
                            x-model="role"
                            class="w-full border {{ $errors->has('role') ? 'border-synapso-danger' : 'border-slate-200' }} bg-slate-50 rounded-lg px-3 py-2.5 text-sm font-medium focus:ring-1 focus:ring-synapso-navy focus:border-synapso-navy focus:bg-white transition-colors cursor-pointer">
                            <option value="client">Cliente</option>
                            <option value="agent">Agente</option>
                            <option value="admin">Administrador</option>
                        </select>

                        {{-- Descripción dinámica por rol --}}
                        <div class="mt-2">
                            <div x-show="role === 'client'" x-transition
                                class="flex items-start gap-2 bg-slate-50 border border-slate-100 rounded-lg px-3 py-2.5">
                                <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    <span class="font-bold text-slate-700">Cliente</span> — Puede crear y seguir sus propios tickets de soporte.
                                </p>
                            </div>

                            <div x-show="role === 'agent'" x-transition
                                class="flex items-start gap-2 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2.5">
                                <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-xs text-blue-700 leading-relaxed">
                                    <span class="font-bold">Agente</span> — Atiende y resuelve los tickets asignados por un administrador.
                                </p>
                            </div>

                            <div x-show="role === 'admin'" x-transition
                                class="flex items-start gap-2 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2.5">
                                <div class="w-5 h-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <p class="text-xs text-amber-700 leading-relaxed">
                                    <span class="font-bold">Administrador</span> — Acceso completo al sistema: gestión de usuarios, tickets y configuración.
                                </p>
                            </div>
                        </div>

                        @error('role')
                            <p class="text-synapso-danger text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Línea divisoria --}}
                    <div class="border-t border-slate-100"></div>

                    {{-- CONTRASEÑA con medidor de fortaleza --}}
                    <div x-data="{
                        password: '',
                        get strength() {
                            const len = this.password.length;
                            if (len === 0) return null;
                            if (len < 8) return 'weak';
                            if (len <= 12) return 'medium';
                            return 'strong';
                        },
                        get strengthLabel() {
                            return { weak: 'Débil', medium: 'Media', strong: 'Fuerte' }[this.strength] ?? '';
                        },
                        get strengthColor() {
                            return { weak: 'bg-synapso-danger', medium: 'bg-amber-400', strong: 'bg-synapso-success' }[this.strength] ?? 'bg-slate-200';
                        },
                        get strengthTextColor() {
                            return { weak: 'text-synapso-danger', medium: 'text-amber-500', strong: 'text-synapso-success' }[this.strength] ?? '';
                        },
                        get strengthWidth() {
                            return { weak: 'w-1/3', medium: 'w-2/3', strong: 'w-full' }[this.strength] ?? 'w-0';
                        }
                    }">
                        <label for="password" class="block text-sm font-semibold text-synapso-navy mb-1.5">
                            Contraseña
                            <span class="text-synapso-danger">*</span>
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            x-model="password"
                            autocomplete="new-password"
                            class="w-full border {{ $errors->has('password') ? 'border-synapso-danger' : 'border-slate-200' }} bg-slate-50 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-synapso-navy focus:border-synapso-navy focus:bg-white transition-colors">

                        {{-- Medidor de fortaleza --}}
                        <div x-show="password.length > 0" x-transition class="mt-2">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Fortaleza</span>
                                <span class="text-[10px] font-bold tracking-wider uppercase" :class="strengthTextColor" x-text="strengthLabel"></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300" :class="[strengthColor, strengthWidth]"></div>
                            </div>
                        </div>

                        @error('password')
                            <p class="text-synapso-danger text-xs mt-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- CONFIRMAR CONTRASEÑA --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-synapso-navy mb-1.5">
                            Confirmar contraseña
                            <span class="text-synapso-danger">*</span>
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            autocomplete="new-password"
                            class="w-full border border-slate-200 bg-slate-50 rounded-lg px-3 py-2.5 text-sm focus:ring-1 focus:ring-synapso-navy focus:border-synapso-navy focus:bg-white transition-colors">
                    </div>

                </div>

                {{-- ACCIONES --}}
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.users') }}"
                        class="inline-flex items-center gap-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg text-sm font-bold shadow-sm transition-all duration-150">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-synapso-gold hover:bg-synapso-amber text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-px active:translate-y-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Crear Usuario
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
