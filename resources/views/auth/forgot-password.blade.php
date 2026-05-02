<x-guest-layout>
<div class="w-full max-w-5xl bg-white rounded-xl overflow-hidden flex flex-col md:flex-row shadow-sm border border-slate-200">

        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
            {{-- Botón de regreso --}}
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-synapso-navy hover:text-amber-600 transition-colors mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver al login
            </a>

            {{-- Encabezado --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 mb-2">
                    Recuperar contraseña
                </h1>
                <p class="text-sm text-slate-500 leading-relaxed">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </p>
            </div>

            {{-- Mensaje de sesión --}}
            <x-auth-session-status class="mb-4 text-sm text-synapso-success font-medium" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                {{-- EMAIL --}}
                <div>
                    <x-input-label for="email" :value="__('Email')"
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
                    />

                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-synapso-danger text-xs" />
                </div>

                {{-- SUBMIT --}}
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full flex justify-center items-center py-3 px-4 rounded
                               bg-synapso-gold hover:bg-amber-600 active:bg-amber-700
                               text-white text-xs font-bold tracking-widest uppercase
                               transition-colors duration-150 shadow-sm"
                    >
                        {{ __('Email Password Reset Link') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="w-full md:w-1/2 bg-synapso-navy p-8 md:p-12 flex flex-col justify-between text-white relative overflow-hidden">

            {{-- Orbes decorativos de fondo --}}
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
                    Recuperación de<br>Acceso Seguro
                </h2>
                <p class="text-sm text-slate-300 leading-relaxed max-w-sm">
                    Protegemos la infraestructura empresarial. Sigue los pasos para restablecer tu contraseña y recuperar el control de tu cuenta de forma segura.
                </p>
            </div>

            <div class="relative z-10 space-y-5 mt-12 md:mt-0">
                <div class="flex justify-between items-center text-xs text-slate-500 font-semibold tracking-widest uppercase">
                    <span>© {{ date('Y') }} M1croslop.</span>
                    <span>Todos los derechos reservados.</span>
                </div>

            </div>
        </div>

    </div>

</x-guest-layout>
