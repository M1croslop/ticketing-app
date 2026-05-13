@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-synapso-navy">Panel de Tickets</h2>
                <p class="text-slate-500 text-sm">Gestiona y supervisa las incidencias del sistema.</p>
            </div>

            <a href="{{ route('tickets.create') }}"
                class="bg-synapso-gold hover:bg-synapso-amber text-white px-5 py-2.5 rounded-lg font-semibold transition flex items-center gap-2 transition-all duration-200 hover:shadow-lg hover:-translate-y-px active:translate-y-0">

                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>

                Nuevo Ticket
            </a>
        </div>


        {{-- FILTROS --}}
        <div class="mb-4 flex gap-3">
            <form id="filter-form" method="GET" action="{{ route('tickets.index') }}" class="flex gap-2">
 
                <input id="search-input" type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar ticket..." autocomplete="off"
                    class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-synapso-navy">
 
                <select name="status" id="status-select"
                    class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-synapso-navy"
                    onchange="doSearch()">
 
                    <option value="">Todos</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress
                    </option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
 
                </select>
 
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
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Acciones</th>
                    </tr>
                </thead>

                <tbody id="tickets-tbody" class="divide-y divide-slate-200">
                    @php
                    $priorityClasses = [
                        'urgent' => 'bg-synapso-priority-urgent-bg text-synapso-priority-urgent-text',
                        'high'   => 'bg-synapso-priority-high-bg text-synapso-priority-high-text',
                        'medium' => 'bg-synapso-priority-mid-bg text-synapso-priority-mid-text',
                        'low'    => 'bg-synapso-priority-low-bg text-synapso-priority-low-text',
                    ];
                    $statusClasses = [
                        'open'        => 'bg-synapso-status-open-bg text-synapso-status-open-text',
                        'in_progress' => 'bg-synapso-status-progress-bg text-synapso-status-progress-text',
                        'resolved'    => 'bg-synapso-status-done-bg text-synapso-status-done-text',
                        'closed'      => 'bg-synapso-status-done-bg text-synapso-status-done-text',
                    ];
                    @endphp

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
                                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $priorityClasses[$ticket->priority] ?? '' }}">
                                            {{ strtoupper($ticket->priority) }}
                                        </span>
                                    </td>

                                    {{-- AGENTE --}}
                                    <td class="px-6 py-4 text-sm">
                                        @if(auth()->user()->role === 'admin')
                                            <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <select name="agent_id" onchange="this.form.submit()" 
                                                    class="block w-full min-w-[130px] border-slate-200 bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg py-1.5 pl-3 pr-8 hover:bg-slate-100 hover:border-slate-300 focus:border-synapso-navy focus:ring focus:ring-synapso-navy focus:ring-opacity-50 transition-all shadow-sm cursor-pointer">
                                                    <option value="">No asignado</option>
                                                    @foreach($agents as $agent)
                                                        <option value="{{ $agent->id }}" @selected($ticket->agent_id === $agent->id)>
                                                            {{ $agent->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        @else
                                            {{ $ticket->agent->name ?? 'No asignado' }}
                                        @endif
                                    </td>

                                    {{-- ESTADO --}}
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-sm font-semibold rounded whitespace-nowrap {{ $statusClasses[$ticket->status] ?? '' }}">
                                            {{ ucfirst(str_replace('_', '-', $ticket->status)) }}
                                        </span>
                                    </td>

                                    {{-- ACCIONES --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-1.5">

                                            {{-- VER DETALLES --}}
                                            <a href="{{ route('tickets.show', $ticket) }}" title="Ver detalles"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-transparent
                                                                                                                                                                                                                                                                                                                       text-slate-400 hover:text-synapso-navy hover:bg-slate-50 hover:border-synapso-navy
                                                                                                                                                                                                                                                                                                                       transition-all duration-150">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                                                                                                                                                                                                                                                                                                           a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                            @can('delete', $ticket)
                                            {{-- EDITAR --}}
                                            <a href="{{ route('tickets.edit', $ticket) }}" title="Editar ticket"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-transparent
                                                                                                                                                                                                                                                                                                                       text-slate-400 hover:text-synapso-gold hover:bg-amber-50 hover:border-synapso-gold
                                                                                                                                                                                                                                                                                                                       transition-all duration-150">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                                                                                                                                                                                                                                                                                           m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            {{-- ELIMINAR --}}
                                            <form action="{{ route('tickets.destroy', $ticket) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Borrar ticket"
                                                    onclick="return confirm('¿Deseas borrar este ticket?')"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-transparent
                                                                                                                                                                                                                                                                                                                           text-slate-400 hover:text-synapso-danger hover:bg-red-50 hover:border-synapso-danger
                                                                                                                                                                                                                                                                                                                           transition-all duration-150">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858
                                                                                                                                                                                                                                                                                                                               L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
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

    {{-- BUSQUEDA DINAMICA (fetch) --}}
    <script>
        function doSearch() {
            const input = document.getElementById('search-input');
            const select = document.getElementById('status-select');
            const tbody = document.getElementById('tickets-tbody');

            const params = new URLSearchParams();
            if (input.value) params.set('search', input.value);
            if (select.value) params.set('status', select.value);

            const url = '{{ route('tickets.index') }}?' + params.toString();

            // Indicador visual sutil de carga
            tbody.style.opacity = '0.4';
            tbody.style.transition = 'opacity 0.15s';

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTbody = doc.getElementById('tickets-tbody');

                    if (newTbody) {
                        tbody.innerHTML = newTbody.innerHTML;
                    }

                    history.pushState(null, '', url);

                    tbody.style.opacity = '1';
                })
                .catch(error => { 
                    tbody.style.opacity = '1'; 
                    console.error('Error fetching tickets:', error);
                    alert('Error al realizar la búsqueda.');
                });
        }

        // Debounce
        (function () {
            const input = document.getElementById('search-input');
            let timer;

            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(doSearch, 350);
            });
        })();
    </script>

@endsection