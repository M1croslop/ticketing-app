<x-guest-layout>
    <div class="w-full max-w-5xl bg-white rounded-xl overflow-hidden flex flex-col md:flex-row shadow-sm border border-slate-200">
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
            {{-- Encabezado --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 mb-1">
                    Iniciar sesión
                </h1>
                <p class="text-sm text-slate-500">
                    Ingresa tus credenciales para acceder al portal de gestión IT.
                </p>
            </div>

            {{-- Mensaje de sesión --}}
            <x-auth-session-status class="mb-4 text-sm text-synapso-success" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- EMAIL --}}
                <div>
                    <x-input-label for="email" :value="__('Correo electrónico')"
                        class="block text-xs font-semibold tracking-widest text-slate-500 uppercase mb-1" />

                    <x-text-input
                        id="email"
                        class="block w-full rounded border-slate-300 bg-white text-slate-900 text-sm
                               px-4 py-2.5 focus:border-synapso-navy focus:ring-synapso-navy
                               placeholder-slate-400"
                        type="email"
                        name="email"
                        :value="old('email')"
                        placeholder="agente@synapso.com"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-synapso-danger text-xs" />
                </div>

                {{-- PASSWORD --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <x-input-label for="password" :value="__('Contraseña')"
                            class="text-xs font-semibold tracking-widest text-slate-500 uppercase" />

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-xs font-semibold text-synapso-navy hover:text-amber-600 transition-colors">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <x-text-input
                        id="password"
                        class="block w-full rounded border-slate-300 bg-white text-slate-900 text-sm
                               px-4 py-2.5 focus:border-synapso-navy focus:ring-synapso-navy
                               placeholder-slate-400"
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    />

                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-synapso-danger text-xs" />
                </div>

                {{-- REMEMBER ME --}}
                <div class="flex items-center gap-2">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 rounded border-slate-300 text-synapso-navy focus:ring-synapso-navy bg-white"
                    >
                    <label for="remember_me" class="text-sm text-slate-600 select-none">
                        Recordarme
                    </label>
                </div>

                {{-- SUBMIT --}}
                <button
                    type="submit"
                    class="w-full flex justify-center items-center py-3 px-4 rounded
                           bg-synapso-gold hover:bg-amber-600 active:bg-amber-700
                           text-white text-xs font-bold tracking-widest uppercase
                           transition-colors duration-150 shadow-sm"
                >
                    Iniciar sesión
                </button>

            </form>
        </div>

        <div class="w-full md:w-1/2 bg-synapso-navy p-8 md:p-12 flex flex-col justify-between text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-indigo-500 opacity-20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-48 h-48 bg-blue-700 opacity-20 rounded-full blur-2xl pointer-events-none"></div>

            {{-- Contenido superior --}}
            <div class="relative z-10">

                {{-- Logo + nombre --}}
                <div class="flex items-center gap-2.5 mb-12">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                         class="w-7 h-7 text-synapso-gold">
                        <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 2.5a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                    </svg>
                    <span class="text-2xl font-bold tracking-tight">Synapso</span>
                </div>

                {{-- Título y descripción --}}
                <h2 class="text-3xl font-bold leading-tight tracking-tight mb-4">
                    Bienvenido a<br>Synapso IT Management
                </h2>
                <p class="text-sm text-slate-300 leading-relaxed max-w-sm">
                    Control centralizado para infraestructura empresarial.
                    Monitorea sistemas, gestiona tickets y mantén la excelencia operativa.
                </p>
            </div>

            {{-- Contenido inferior: widget + footer --}}
            <div class="relative z-10 space-y-5 mt-12 md:mt-0">

                <div class="bg-white/5 border border-white/10 rounded-lg p-4 backdrop-blur-sm">
                    <div class="flex items-center justify-between">

                        <div class="flex items-center gap-3">
                            @if ($dbOnline)
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-synapso-success opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-synapso-success"></span>
                                </span>
                                <span class="text-sm font-medium">Sistemas operativos</span>
                            @else
                                <span class="relative flex h-3 w-3">
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-400"></span>
                                </span>
                                <span class="text-sm font-medium text-amber-400">Sistemas caídos</span>
                            @endif
                        </div>

                        @if ($dbOnline)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257
                                         3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33
                                         4.502 4.502 0 0 0-3.09 4.575"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-amber-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71
                                         c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0
                                         L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                            </svg>
                        @endif
                    </div>

                    <div class="mt-3 text-xs font-semibold tracking-widest uppercase
                                {{ $dbOnline ? 'text-slate-400' : 'text-amber-400' }}">
                        {{ $dbOnline
                            ? 'Todos los servicios funcionan correctamente'
                            : 'No se pudo conectar a la base de datos' }}
                    </div>
                </div>

                <div class="flex justify-between items-center text-xs text-slate-500 font-semibold tracking-widest uppercase">
                    <span>© {{ date('Y') }} M1croslop.</span>
                    <span>Todos los derechos reservados.</span>
                </div>

            </div>
        </div>

    </div>

</x-guest-layout>