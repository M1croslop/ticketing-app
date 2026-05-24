@extends('layouts.app')

@section('content')
@php
    // Map category names → UX metadata (icon + label)
    // Falls back to a sensible generic if name doesn't match
    $categoryMeta = [
        'Hardware' => [
            'label' => 'Mi equipo no funciona',
            'sub'   => 'Computadora, monitor, teclado, impresora…',
            'icon'  => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
        ],
        'Software' => [
            'label' => 'Un programa falla o no abre',
            'sub'   => 'Aplicaciones, sistema, errores de software…',
            'icon'  => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z"/></svg>',
        ],
        'Redes' => [
            'label' => 'Sin internet o sin conexión',
            'sub'   => 'WiFi, red, VPN, conexión lenta…',
            'icon'  => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"/></svg>',
        ],
        'Accesos' => [
            'label' => 'No puedo entrar a un sistema',
            'sub'   => 'Contraseñas, permisos, cuentas bloqueadas…',
            'icon'  => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>',
        ],
        'Otro' => [
            'label' => 'Otro tipo de problema',
            'sub'   => 'Si no encaja en ninguna categoría…',
            'icon'  => '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>',
        ],
    ];

    // Generic fallback for categories not in the map
    $genericIcon = '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>';
@endphp

<div class="min-h-screen bg-synapso-bg py-8">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- ── Breadcrumb / back nav ────────────────────────────────────────── --}}
    <div class="mb-6 flex items-center gap-2 text-xs font-semibold text-slate-400">
        <a href="{{ route('tickets.index') }}"
           class="flex items-center gap-1.5 hover:text-synapso-gold transition-colors duration-150">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a mis tickets
        </a>
        <span class="text-slate-300">/</span>
        <span class="text-slate-500 font-bold">Nueva solicitud</span>
    </div>

    {{-- ── Page title ───────────────────────────────────────────────────── --}}
    <div class="mb-7">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Reportar un problema</h1>
        <p class="mt-1 text-sm text-slate-500 font-medium">Cuéntanos qué está pasando y te ayudaremos lo antes posible.</p>
    </div>

    {{-- ── Two-column grid ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- ════ LEFT: FORM (2/3) ══════════════════════════════════════════ --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                <form action="{{ route('tickets.store') }}" method="POST"
                      x-data="{
                          category: '{{ old('category_id', '') }}',
                          priority: '{{ old('priority', 'medium') }}',
                          title: '{{ addslashes(old('title', '')) }}',
                          description: '{{ addslashes(old('description', '')) }}',
                          loading: false,
                          get canSubmit() {
                              return this.title.trim().length >= 5
                                  && this.description.trim().length >= 10
                                  && this.category !== '';
                          },
                          autoResize(el) {
                              el.style.height = 'auto';
                              el.style.height = el.scrollHeight + 'px';
                          },
                          handleSubmit() {
                              if (!this.canSubmit) return;
                              this.loading = true;
                          }
                      }"
                      @submit="handleSubmit">
                    @csrf

                    {{-- Hidden inputs bound to Alpine state --}}
                    <input type="hidden" name="category_id" :value="category">
                    <input type="hidden" name="priority"    :value="priority">

                    <div class="p-6 space-y-7">

                        {{-- ── 1. CATEGORY CARDS ──────────────────────────── --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">
                                ¿Qué tipo de problema tienes?
                                <span class="text-synapso-danger">*</span>
                            </label>
                            <p class="text-xs text-slate-400 mb-3">Elige la categoría que mejor describe tu situación</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                @foreach($categories as $cat)
                                    @if(isset($categoryMeta[$cat->name]))
                                    @php
                                        $meta = $categoryMeta[$cat->name];
                                    @endphp
                                    <button type="button"
                                            @click="category = '{{ $cat->id }}'"
                                            :class="category == '{{ $cat->id }}'
                                                ? 'border-synapso-gold bg-amber-50 ring-1 ring-synapso-gold shadow-sm'
                                                : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50'"
                                            class="flex items-start gap-3 p-3.5 rounded-xl border-2 text-left transition-all duration-150 w-full">

                                        <span :class="category == '{{ $cat->id }}' ? 'text-synapso-gold' : 'text-slate-400'"
                                            class="flex-shrink-0 mt-0.5 transition-colors duration-150">
                                            {!! $meta['icon'] !!}
                                        </span>

                                        <span class="flex-1 min-w-0">
                                            <span class="block text-sm font-semibold text-slate-800 leading-tight">
                                                {{ $meta['label'] }}
                                            </span>
                                            @if($meta['sub'])
                                            <span class="block text-xs text-slate-400 mt-0.5 leading-snug">
                                                {{ $meta['sub'] }}
                                            </span>
                                            @endif
                                        </span>

                                        <span x-show="category == '{{ $cat->id }}'"
                                            class="flex-shrink-0 w-5 h-5 rounded-full bg-synapso-gold flex items-center justify-center mt-0.5">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    </button>
                                    @endif
                                @endforeach
                            </div>

                            @error('category_id')
                                <p class="text-synapso-danger text-xs mt-2 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                            <div x-show="category === '' && document.querySelector('[name=category_id]')?.form?.classList?.contains('submitted')"
                                 class="hidden"></div>
                        </div>

                        {{-- ── 2. TITLE ────────────────────────────────────── --}}
                        <div>
                            <div class="flex items-baseline justify-between mb-1">
                                <label for="title-input" class="text-sm font-bold text-slate-800">
                                    Resume el problema en una línea
                                    <span class="text-synapso-danger">*</span>
                                </label>
                                <span class="text-[10px] font-semibold tabular-nums"
                                      :class="title.length > 130 ? 'text-amber-500' : title.length > 149 ? 'text-red-500' : 'text-slate-400'">
                                    <span x-text="title.length"></span>/150
                                </span>
                            </div>

                            <input id="title-input"
                                   type="text"
                                   name="title"
                                   x-model="title"
                                   maxlength="150"
                                   autocomplete="off"
                                   placeholder="Ej: Mi computadora no enciende, No puedo abrir el correo…"
                                   class="w-full border rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400
                                          transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-synapso-gold focus:border-transparent
                                          {{ $errors->has('title') ? 'border-synapso-danger bg-red-50' : 'border-slate-200 bg-white hover:border-slate-300' }}">

                            @error('title')
                                <p class="text-synapso-danger text-xs mt-1.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- ── 3. DESCRIPTION ──────────────────────────────── --}}
                        <div>
                            <div class="flex items-baseline justify-between mb-1">
                                <label for="desc-input" class="text-sm font-bold text-slate-800">
                                    ¿Qué está pasando exactamente?
                                    <span class="text-synapso-danger">*</span>
                                </label>
                                <span class="text-[10px] font-semibold tabular-nums"
                                      :class="description.length < 10 ? 'text-amber-500' : 'text-slate-400'">
                                    <span x-text="description.length < 10 ? 'mínimo 10 caracteres' : description.length + ' caracteres'"></span>
                                </span>
                            </div>

                            <textarea id="desc-input"
                                      name="description"
                                      x-model="description"
                                      @input="autoResize($el)"
                                      x-init="autoResize($el)"
                                      rows="5"
                                      placeholder="Describe el problema con el mayor detalle posible.&#10;Ej: Desde esta mañana mi computadora no enciende, escucho un sonido pero la pantalla queda en negro. Intenté reiniciarla dos veces sin resultado..."
                                      class="w-full border rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 resize-none
                                             transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-synapso-gold focus:border-transparent
                                             {{ $errors->has('description') ? 'border-synapso-danger bg-red-50' : 'border-slate-200 bg-white hover:border-slate-300' }}">{{ old('description') }}</textarea>

                            @error('description')
                                <p class="text-synapso-danger text-xs mt-1.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- ── 4. URGENCY SELECTOR ─────────────────────────── --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">
                                ¿Cuál es el nivel de urgencia?
                                <span class="text-synapso-danger">*</span>
                            </label>
                            <p class="text-xs text-slate-400 mb-3">Esto nos ayuda a priorizar tu solicitud correctamente</p>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">

                                {{-- Low: Puede esperar --}}
                                <button type="button"
                                        @click="priority = 'low'"
                                        :class="priority === 'low'
                                            ? 'border-emerald-400 bg-emerald-50 ring-1 ring-emerald-400 shadow-sm'
                                            : 'border-slate-200 bg-white hover:border-emerald-300 hover:bg-emerald-50/40'"
                                        class="relative flex flex-col items-center gap-2 p-4 rounded-xl border text-center
                                               transition-all duration-150 cursor-pointer focus:outline-none
                                               focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1">
                                    <span :class="priority === 'low' ? 'text-emerald-600' : 'text-slate-400'"
                                          class="transition-colors duration-150">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>
                                    <span>
                                        <span :class="priority === 'low' ? 'text-emerald-700' : 'text-slate-700'"
                                              class="block text-sm font-bold transition-colors duration-150">Puede esperar</span>
                                        <span class="block text-[11px] text-slate-400 mt-0.5 leading-tight">No es urgente, cuando puedas</span>
                                    </span>
                                    <span x-show="priority === 'low'"
                                          class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                </button>

                                {{-- Medium: Lo necesito hoy --}}
                                <button type="button"
                                        @click="priority = 'medium'"
                                        :class="priority === 'medium'
                                            ? 'border-amber-400 bg-amber-50 ring-1 ring-amber-400 shadow-sm'
                                            : 'border-slate-200 bg-white hover:border-amber-300 hover:bg-amber-50/40'"
                                        class="relative flex flex-col items-center gap-2 p-4 rounded-xl border text-center
                                               transition-all duration-150 cursor-pointer focus:outline-none
                                               focus:ring-2 focus:ring-amber-400 focus:ring-offset-1">
                                    <span :class="priority === 'medium' ? 'text-amber-600' : 'text-slate-400'"
                                          class="transition-colors duration-150">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </span>
                                    <span>
                                        <span :class="priority === 'medium' ? 'text-amber-700' : 'text-slate-700'"
                                              class="block text-sm font-bold transition-colors duration-150">Lo necesito hoy</span>
                                        <span class="block text-[11px] text-slate-400 mt-0.5 leading-tight">Afecta mi trabajo pero puedo continuar</span>
                                    </span>
                                    <span x-show="priority === 'medium'"
                                          class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full bg-amber-400 flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                </button>

                                {{-- Urgent: Trabajo bloqueado --}}
                                <button type="button"
                                        @click="priority = 'urgent'"
                                        :class="priority === 'urgent'
                                            ? 'border-red-400 bg-red-50 ring-1 ring-red-400 shadow-sm'
                                            : 'border-slate-200 bg-white hover:border-red-300 hover:bg-red-50/40'"
                                        class="relative flex flex-col items-center gap-2 p-4 rounded-xl border text-center
                                               transition-all duration-150 cursor-pointer focus:outline-none
                                               focus:ring-2 focus:ring-red-400 focus:ring-offset-1">
                                    <span :class="priority === 'urgent' ? 'text-red-600' : 'text-slate-400'"
                                          class="transition-colors duration-150">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </span>
                                    <span>
                                        <span :class="priority === 'urgent' ? 'text-red-700' : 'text-slate-700'"
                                              class="block text-sm font-bold transition-colors duration-150">Trabajo bloqueado</span>
                                        <span class="block text-[11px] text-slate-400 mt-0.5 leading-tight">No puedo trabajar hasta resolver esto</span>
                                    </span>
                                    <span x-show="priority === 'urgent'"
                                          class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full bg-red-500 flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                </button>

                            </div>

                            @error('priority')
                                <p class="text-synapso-danger text-xs mt-2 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- ── Agent / Admin extra fields ──────────────────── --}}
                        @if(auth()->user()->role !== 'client')
                        <div class="border-t border-slate-100 pt-5">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Opciones de administración</p>
                            <div class="grid sm:grid-cols-2 gap-4">

                                {{-- Agent assignment --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Asignar agente</label>
                                    <select name="agent_id"
                                            class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 bg-white
                                                   focus:outline-none focus:ring-2 focus:ring-synapso-gold focus:border-transparent
                                                   hover:border-slate-300 transition-all duration-150">
                                        <option value="">Sin asignar</option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent->id }}" @selected(old('agent_id') == $agent->id)>
                                                {{ $agent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Due date (admin/agent only) --}}
                                @if(auth()->user()->role === 'admin')
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Fecha límite SLA</label>
                                    <input type="date" name="due_date"
                                           value="{{ old('due_date') }}"
                                           class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 bg-white
                                                  focus:outline-none focus:ring-2 focus:ring-synapso-gold focus:border-transparent
                                                  hover:border-slate-300 transition-all duration-150">
                                </div>
                                @endif

                            </div>
                        </div>
                        @endif

                    </div>{{-- end p-6 space-y-7 --}}

                    {{-- ── Form footer: actions ───────────────────────────── --}}
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">

                        <a href="{{ route('tickets.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white
                                  hover:bg-slate-50 text-sm font-semibold text-slate-600 transition-all duration-150
                                  hover:border-slate-300 hover:-translate-y-px active:translate-y-0">
                            Cancelar
                        </a>

                        <button type="submit"
                                :disabled="!canSubmit || loading"
                                :class="canSubmit && !loading
                                    ? 'bg-synapso-gold hover:bg-synapso-amber text-white shadow-md hover:shadow-lg hover:-translate-y-px active:translate-y-0 cursor-pointer'
                                    : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold
                                       transition-all duration-150">
                            <span x-show="!loading">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </span>
                            <span x-show="loading">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </span>
                            <span x-text="loading ? 'Enviando…' : 'Enviar solicitud de soporte'"></span>
                        </button>

                    </div>

                </form>
            </div>
        </div>

        {{-- ════ RIGHT: INFO SIDEBAR (1/3) ══════════════════════════════════ --}}
        <div class="space-y-4 lg:sticky lg:top-6">

            {{-- Tips card --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-synapso-gold" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                    <h2 class="text-sm font-bold text-slate-800">Consejos para una respuesta más rápida</h2>
                </div>
                <div class="p-5">
                    <ul class="space-y-3">
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                <span class="font-semibold text-slate-800">Sé específico.</span>
                                Describe exactamente qué pasó, no solo que "algo no funciona".
                            </p>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                <span class="font-semibold text-slate-800">Indica desde cuándo ocurre</span>
                                y si pasó algo antes del problema.
                            </p>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                <span class="font-semibold text-slate-800">Menciona si otros tienen el mismo problema</span>
                                o si solo te afecta a ti.
                            </p>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                <span class="font-semibold text-slate-800">Indica qué ya intentaste</span>
                                para solucionarlo.
                            </p>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Response times card --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <h2 class="text-sm font-bold text-slate-800">Tiempos de respuesta</h2>
                </div>
                <div class="p-5 space-y-3">

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            <span class="text-xs font-semibold text-slate-700">Trabajo bloqueado</span>
                        </div>
                        <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full border border-red-100">~4 horas</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <span class="text-xs font-semibold text-slate-700">Lo necesito hoy</span>
                        </div>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100">~24 horas</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-semibold text-slate-700">Puede esperar</span>
                        </div>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">~7 días</span>
                    </div>

                    <p class="text-[10px] text-slate-400 pt-2 border-t border-slate-100 leading-relaxed">
                        Los tiempos son estimados y pueden variar según la carga de trabajo del equipo de soporte.
                    </p>
                </div>
            </div>

            {{-- Status indicator --}}
            <div class="relative overflow-hidden bg-indigo-700 rounded-2xl shadow p-5 text-white">
                <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-white/5"></div>
                <div class="absolute -right-2 -bottom-6 w-28 h-28 rounded-full bg-white/5"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest">Estado del sistema</p>
                    </div>
                    <p class="text-base font-extrabold tracking-tight">Equipo disponible</p>
                    <p class="text-xs text-indigo-200 mt-1 leading-relaxed">
                        Nuestros agentes están activos y listo para ayudarte.
                        Lun–Vie, 9am–6pm.
                    </p>
                </div>
            </div>

        </div>
        {{-- end right sidebar --}}

    </div>
</div>
</div>
@endsection