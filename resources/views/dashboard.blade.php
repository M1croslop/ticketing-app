@extends('layouts.app')

@section('content')

<div class="min-h-screen flex flex-col">

    <!-- MAIN -->
    <main class="p-6 flex-grow max-w-7xl mx-auto w-full">

        <!-- MÉTRICAS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

            <div class="card">
                <p class="text-slate-500 text-sm font-semibold uppercase">Tickets Abiertos</p>
                <p class="text-3xl font-bold text-slate-800">24</p>
            </div>

            <div class="card">
                <p class="text-slate-500 text-sm font-semibold uppercase">Avg. resolution time</p>
                <p class="text-3xl font-bold text-slate-800">8h</p>
            </div>


            <div class="card">
                <p class="text-slate-500 text-sm font-semibold uppercase">Resueltos Hoy</p>
                <p class="text-3xl font-bold text-synapso-success">12</p>
            </div>

        </div>

        <!-- KANBAN -->
        <div class="flex space-x-4 overflow-x-auto pb-4">

            <!-- NUEVOS -->
            <div class="flex-shrink-0 w-80 bg-slate-100 p-3 rounded-lg">
                <h3 class="font-bold text-slate-700 mb-4 flex justify-between">
                    NUEVOS <span class="bg-slate-300 px-2 rounded text-sm">3</span>
                </h3>

                <div class="space-y-3">

                    <div class="card">

                        <span class="badge-critical">
                            Prioridad Crítica
                        </span>

                        <p class="font-semibold text-slate-800 mt-1">
                            Falla crítica en VPN
                        </p>

                        <p class="text-sm text-slate-500">
                            Usuario: Roberto M.
                        </p>

                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-xs text-slate-400">
                                Hace 15 min
                            </span>

                            <button class="text-synapso-gold text-sm font-bold hover:underline">
                                Tomar Caso
                            </button>
                        </div>

                    </div>

                </div>
            </div>

            <!-- EN PROGRESO -->
            <div class="flex-shrink-0 w-80 bg-slate-100 p-3 rounded-lg">
                <h3 class="font-bold text-slate-700 mb-4 flex justify-between">
                    EN PROGRESO <span class="bg-slate-300 px-2 rounded text-sm">2</span>
                </h3>

                <div class="space-y-3">

                    <div class="card">

                        <span class="badge-high">
                            Prioridad Media
                        </span>

                        <p class="font-semibold text-slate-800 mt-1">
                            Configuración Outlook
                        </p>

                        <p class="text-sm text-slate-500">
                            Agente: Alexis C.
                        </p>

                        <div class="mt-4 flex justify-end">
                            <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center text-xs">
                                AC
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- RESUELTOS -->
            <div class="flex-shrink-0 w-80 bg-slate-100 p-3 rounded-lg">
                <h3 class="font-bold text-slate-700 mb-4 flex justify-between">
                    RESUELTOS <span class="bg-slate-300 px-2 rounded text-sm">15</span>
                </h3>

                <div class="space-y-3 opacity-70">

                    <div class="card">

                        <p class="font-semibold text-slate-800 line-through">
                            Recuperación de contraseña
                        </p>

                        <p class="text-sm text-slate-500 italic">
                            Cerrado hace 1h
                        </p>

                    </div>

                </div>
            </div>

        </div>

    </main>
</div>

@endsection