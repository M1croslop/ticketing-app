@extends('layouts.app')

@section('content')

<div class="min-h-screen flex flex-col">

    <!-- MAIN -->
    <main class="p-6 flex-grow max-w-7xl mx-auto w-full">

        <!-- MÉTRICAS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">

            <div class="card">
                <p class="text-slate-500 text-sm font-semibold uppercase">Tickets Abiertos</p>
                <p class="text-3xl font-bold text-slate-800">{{ $openCount }}</p>
            </div>

            <div class="card">
                <p class="text-slate-500 text-sm font-semibold uppercase">Tiempo Promedio de Resolución</p>
                <p class="text-3xl font-bold text-slate-800">
                {{ $avgResolutionTime ? round($avgResolutionTime) . 'h' : '—' }}
                </p>
            </div>


            <div class="card">
                <p class="text-slate-500 text-sm font-semibold uppercase">Tickets Resueltos</p>
                <p class="text-3xl font-bold text-synapso-success">
                    {{ $resolvedToday }}
                </p>
            </div>

        </div>

        <!-- KANBAN -->
       <div class="flex gap-4 overflow-x-auto pb-4">

        {{-- NUEVOS --}}
        <div class="flex-shrink-0 w-80 bg-slate-100 rounded-lg p-3 flex flex-col max-h-[70vh]">

            <h3 class="font-bold text-slate-700 mb-3 flex justify-between items-center">
                NUEVOS
                <span class="bg-slate-300 px-2 rounded text-xs">
                    {{ $newTickets->count() }}
                </span>
            </h3>

            <div class="space-y-3 overflow-y-auto pr-1">

                @foreach($newTickets as $ticket)
                    <div class="card">

                        <span class="text-xs font-semibold text-synapso-danger">
                            {{ strtoupper($ticket->priority) }}
                        </span>

                        <p class="font-semibold text-slate-800 mt-1">
                            {{ $ticket->title }}
                        </p>

                        <p class="text-sm text-slate-500">
                            {{ $ticket->user->name }}
                        </p>

                        <p class="text-xs text-slate-400 mt-2">
                            {{ $ticket->created_at->diffForHumans() }}
                        </p>

                    </div>
                @endforeach

            </div>
        </div>


        {{-- EN PROGRESO --}}
        <div class="flex-shrink-0 w-80 bg-slate-100 rounded-lg p-3 flex flex-col max-h-[70vh]">

            <h3 class="font-bold text-slate-700 mb-3 flex justify-between items-center">
                EN PROGRESO
                <span class="bg-slate-300 px-2 rounded text-xs">
                    {{ $inProgressTickets->count() }}
                </span>
            </h3>

            <div class="space-y-3 overflow-y-auto pr-1">

                @foreach($inProgressTickets as $ticket)
                    <div class="card">

                        <span class="text-xs font-semibold text-amber-600">
                            {{ strtoupper($ticket->priority) }}
                        </span>

                        <p class="font-semibold text-slate-800 mt-1">
                            {{ $ticket->title }}
                        </p>

                        <p class="text-sm text-slate-500">
                            {{ $ticket->agent->name ?? 'Sin asignar' }}
                        </p>

                    </div>
                @endforeach

            </div>
        </div>


        {{-- RESUELTOS --}}
        <div class="flex-shrink-0 w-80 bg-slate-100 rounded-lg p-3 flex flex-col max-h-[70vh]">

            <h3 class="font-bold text-slate-700 mb-3 flex justify-between items-center">
                RESUELTOS
                <span class="bg-slate-300 px-2 rounded text-xs">
                    {{ $resolvedTickets->count() }}
                </span>
            </h3>

            <div class="space-y-3 overflow-y-auto pr-1 opacity-70">

                @foreach($resolvedTickets as $ticket)
                    <div class="card">

                        <p class="font-semibold text-slate-800 line-through">
                            {{ $ticket->title }}
                        </p>

                        <p class="text-xs text-slate-500">
                            {{ $ticket->updated_at->diffForHumans() }}
                        </p>

                    </div>
                @endforeach

            </div>
        </div>

    </div>

        </div>

    </main>
</div>

@endsection