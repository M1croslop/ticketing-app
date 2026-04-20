<nav class="bg-synapso-navy text-white shadow p-4 flex justify-between items-center">

    <!-- IZQUIERDA -->
    <div class="flex items-center space-x-6">
        <span class="text-2xl font-bold">Synapso</span>

        <div class="hidden md:flex space-x-4 text-sm">
            <a href="#" class="hover:text-synapso-gold transition">Dashboard</a>
            <a href="#" class="hover:text-synapso-gold transition">Tickets</a>
            <a href="#" class="hover:text-synapso-gold transition">Reportes</a>
        </div>
    </div>

    <div class="flex items-center space-x-4">

    <button class="btn-action">
        + Crear Ticket
    </button>

    <!-- PROFILE DROPDOWN -->
    <div x-data="{ open: false }" class="relative">

        <!-- AVATAR -->
        <div 
            @click="open = !open"
            class="w-10 h-10 bg-slate-600 rounded-full flex items-center justify-center border border-white text-sm cursor-pointer"
        >
            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
        </div>

        <!-- DROPDOWN -->
        <div 
            x-show="open"
            @click.outside="open = false"
            x-transition
            class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden"
        >

            <!-- USER INFO -->
            <div class="px-4 py-2 text-sm text-slate-500 border-b">
                {{ Auth::user()->email }}
            </div>

            <!-- LOGOUT -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button 
                    type="submit"
                    class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-100"
                    hover:bg-synapso-bg hover:text-synapso-navy"
                >
                    Cerrar sesión
                </button>
            </form>

        </div>

    </div>

</div>

</nav>