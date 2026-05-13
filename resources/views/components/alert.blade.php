@php
    $alerts = [
        'success' => [
            'message' => session('success'),
            'bg' => 'bg-white',
            'border' => 'border-l-4 border-synapso-success',
            'icon_bg' => 'bg-synapso-status-done-bg',
            'icon_color' => 'text-synapso-success',
            'bar_color' => 'bg-synapso-success',
            'title' => 'Éxito',
            'title_color' => 'text-synapso-status-done-text',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
        ],
        'error' => [
            'message' => session('error'),
            'bg' => 'bg-white',
            'border' => 'border-l-4 border-synapso-danger',
            'icon_bg' => 'bg-synapso-priority-high-bg',
            'icon_color' => 'text-synapso-danger',
            'bar_color' => 'bg-synapso-danger',
            'title' => 'Error',
            'title_color' => 'text-synapso-priority-high-text',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
        ],
        'warning' => [
            'message' => session('warning'),
            'bg' => 'bg-white',
            'border' => 'border-l-4 border-synapso-gold',
            'icon_bg' => 'bg-synapso-priority-mid-bg',
            'icon_color' => 'text-synapso-gold',
            'bar_color' => 'bg-synapso-gold',
            'title' => 'Advertencia',
            'title_color' => 'text-synapso-priority-mid-text',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />',
        ],
        'info' => [
            'message' => session('info'),
            'bg' => 'bg-white',
            'border' => 'border-l-4 border-synapso-status-progress-text',
            'icon_bg' => 'bg-synapso-status-progress-bg',
            'icon_color' => 'text-synapso-status-progress-text',
            'bar_color' => 'bg-synapso-status-progress-text',
            'title' => 'Información',
            'title_color' => 'text-synapso-status-progress-text',
            'icon' =>
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        ],
    ];
@endphp

@if (collect($alerts)->contains(fn($a) => filled($a['message'])))

    <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 w-80 pointer-events-none" role="region"
        aria-label="Notificaciones" aria-live="polite">
        @foreach ($alerts as $type => $alert)
            @if (filled($alert['message']))
                <div x-data="{
                    show: false,
                    progress: 100,
                    duration: 10000,
                    timer: null,
                    interval: null,
                    init() {
                        // Entrada con pequeño delay para que la transición sea visible
                        this.$nextTick(() => { this.show = true });
                        this.startTimer();
                    },
                    startTimer() {
                        const step = 50;
                        const decrement = (step / this.duration) * 100;
                        this.interval = setInterval(() => {
                            this.progress -= decrement;
                            if (this.progress <= 0) this.dismiss();
                        }, step);
                    },
                    dismiss() {
                        clearInterval(this.interval);
                        this.show = false;
                    },
                    pause() { clearInterval(this.interval); },
                    resume() { this.startTimer(); },
                }" x-init="init()" x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8 scale-95"
                    x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-x-8 scale-95" @mouseenter="pause()"
                    @mouseleave="resume()"
                    class="pointer-events-auto relative overflow-hidden rounded-xl shadow-xl
                    {{ $alert['bg'] }} {{ $alert['border'] }}"
                    id="toast-{{ $type }}">
                    {{-- Body (informacion) --}}
                    <div class="flex items-start gap-3 px-4 py-3.5">

                        {{-- Icon --}}
                        <div class="flex-shrink-0 w-9 h-9 rounded-full {{ $alert['icon_bg'] }} {{ $alert['icon_color'] }}
                                                                                    flex items-center justify-center mt-0.5"
                            aria-hidden="true">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {{-- NOTA: Se renderiza HTML sin escapar ({!! !!}) porque el array de íconos 
                                     debe mantenerse estrictamente dentro de este componente. No inyectar valores externos. --}}
                                {!! $alert['icon'] !!}
                            </svg>
                        </div>

                        {{-- Descripcion --}}
                        <div class="flex-1 min-w-0 pt-0.5">
                            <p class="text-xs font-bold uppercase tracking-wide {{ $alert['title_color'] }} mb-0.5">
                                {{ $alert['title'] }}
                            </p>
                            <p class="text-sm text-slate-700 leading-snug">
                                {{ $alert['message'] }}
                            </p>
                        </div>

                        {{-- Botón cerrar --}}
                        <button @click="dismiss()" type="button"
                            class="flex-shrink-0 p-1 rounded-md text-slate-400 hover:text-slate-600
                                                                                   hover:bg-slate-100 transition-colors duration-150 mt-0.5"
                            aria-label="Cerrar notificación">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Barra de progreso --}}
                    <div class="absolute bottom-0 left-0 h-1 {{ $alert['bar_color'] }} opacity-70 transition-all duration-75 ease-linear"
                        :style="`width: ${progress}%`" aria-hidden="true">
                    </div>

                </div>
            @endif
        @endforeach
    </div>

@endif
