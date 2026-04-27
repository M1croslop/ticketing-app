@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6 bg-synapso-bg">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-synapso-navy">Panel de Tickets</h2>
            <p class="text-slate-500 text-sm">Gestiona y supervisa las incidencias del sistema.</p>
        </div>

        <a href="{{ route('tickets.create') }}"
            class="bg-synapso-navy hover:opacity-90 text-white px-5 py-2.5 rounded-lg font-semibold transition flex items-center gap-2">
            
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>

            Nuevo Ticket
        </a>
    </div>

    {{-- ALERTA --}}
    @if (session('status'))
        <div class="mb-4 p-4 border-l-4 border-synapso-success bg-green-50 text-synapso-success">
            {{ session('status') }}
        </div>
    @endif

    {{-- FILTROS --}}
    <div class="mb-4 flex gap-3">
        <form method="GET" class="flex gap-2">

            <input type="text" name="search"
                value="{{ request('search') }}"
                placeholder="Buscar ticket..."
                class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-synapso-navy">

            <select name="status" onchange="this.form.submit()"
                class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-synapso-navy">

                <option value="">Todos</option>
                <option value="open" {{ request('status')=='open'?'selected':'' }}>Open</option>
                <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
                <option value="resolved" {{ request('status')=='resolved'?'selected':'' }}>Resolved</option>

            </select>

            <button class="bg-synapso-navy text-white px-4 py-2 rounded-lg text-sm">
                Buscar
            </button>

        </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">

            <thead class="bg-synapso-bg">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Ticket</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Prioridad</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Agente</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200">

                @forelse ($tickets as $ticket)
                <tr class="hover:bg-slate-50">

                    {{-- TITULO --}}
                    <td class="px-6 py-4">
                        <div class="font-semibold text-slate-800">{{ $ticket->title }}</div>
                        <div class="text-xs text-slate-500">Por {{ $ticket->user->name }}</div>
                    </td>

                    {{-- CATEGORIA --}}
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $ticket->category->name ?? 'Sin categoría' }}
                    </td>

                    {{-- PRIORIDAD --}}
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded
                            {{ $ticket->priority == 'high' ? 'bg-red-100 text-synapso-danger' :
                               ($ticket->priority == 'medium' ? 'bg-amber-100 text-amber-700' :
                               'bg-green-100 text-synapso-success') }}">
                            {{ strtoupper($ticket->priority) }}
                        </span>
                    </td>

                    {{-- AGENTE --}}
                    <td class="px-6 py-4 text-sm">
                        {{ $ticket->agent->name ?? 'No asignado' }}
                    </td>

                    {{-- ESTADO --}}
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded
                            {{ $ticket->status == 'open' ? 'bg-blue-100 text-blue-700' :
                               ($ticket->status == 'in_progress' ? 'bg-amber-100 text-amber-700' :
                               'bg-green-100 text-synapso-success') }}">
                            {{ ucfirst(str_replace('_',' ', $ticket->status)) }}
                        </span>
                    </td>

                    {{-- ACCIONES --}}
                    <td class="px-6 py-4 text-right flex justify-end gap-2">

                        <a href="{{ route('tickets.show', $ticket) }}"
                            class="bg-synapso-navy text-white px-3 py-1 rounded text-xs">
                            Ver
                        </a>

                        <a href="{{ route('tickets.edit', $ticket) }}"
                            class="border border-slate-300 px-3 py-1 rounded text-xs">
                            Editar
                        </a>

                        <form action="{{ route('tickets.destroy', $ticket) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-synapso-danger text-xs">
                                Eliminar
                            </button>
                        </form>

                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-slate-400">
                        No hay tickets registrados
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- PAGINACION --}}
    <div class="mt-6">
        {{ $tickets->links() }}
    </div>

</div>
@endsection