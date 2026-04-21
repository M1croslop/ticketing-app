<x-guest-layout>

    <div class="flex justify-center pt-20">
        <div class="w-full max-w-md card">
            
            <!-- TÍTULO -->
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-synapso-navy">
                    Synapso
                </h1>
                <p class="text-sm text-slate-500">
                    Acceso al sistema de tickets IT
                </p>
            </div>

            <!-- STATUS -->
            <x-auth-session-status class="mb-4 text-sm text-synapso-success" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- EMAIL -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-slate-600"/>

                    <x-text-input
                        id="email"
                        class="block mt-1 w-full border-slate-300 focus:border-synapso-navy focus:ring-synapso-navy"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                    />

                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-synapso-danger text-sm" />
                </div>

                <!-- PASSWORD -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-slate-600"/>

                    <x-text-input
                        id="password"
                        class="block mt-1 w-full border-slate-300 focus:border-synapso-navy focus:ring-synapso-navy"
                        type="password"
                        name="password"
                        required
                    />

                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-synapso-danger text-sm" />
                </div>

                <!-- REMEMBER -->
                <div class="flex items-center justify-between">

                    <label class="flex items-center text-sm text-slate-600">
                        <input type="checkbox" name="remember" class="mr-2 rounded border-slate-300">
                        Recordarme
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm text-synapso-gold hover:underline">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif

                </div>

                <!-- BOTÓN -->
                <div>
                    <button type="submit" class="btn-primary w-full">
                        Iniciar sesión
                    </button>
                </div>

            </form>

        </div>

    </div>

</x-guest-layout>