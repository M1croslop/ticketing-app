@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Panel de Tickets</h2>
            <p class="text-slate-500 text-sm">Gestiona y supervisa las incidencias del sistema.</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold shadow-sm transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Ticket
        </a>
    </div>

    @if (session('status'))
    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 shadow-sm">
        {{ session('status') }}
    </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Ticket / Usuario</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Categoría</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Prioridad</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Agente Asignado</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse ($tickets as $ticket)
                <tr class="hover:bg-slate-50 transition duration-150">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-slate-900">{{ $ticket->title }}</div>
                        <div class="text-xs text-slate-500">Por: {{ $ticket->user->name ?? 'Desconocido' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-slate-600">{{ $ticket->category->name ?? 'Sin categoría' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold 
                                {{ $ticket->priority == 'high' ? 'bg-red-100 text-red-700' : ($ticket->priority == 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                            {{ strtoupper($ticket->priority) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-7 w-7 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600 mr-2">
                                {{ $ticket->agent ? strtoupper(substr($ticket->agent->name, 0, 2)) : '?' }}
                            </div>
                            <span class="text-sm {{ $ticket->agent ? 'text-slate-700' : 'text-slate-400 italic' }}">
                                {{ $ticket->agent->name ?? 'No asignado' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center text-sm text-slate-600">
                            <div class="w-2 h-2 rounded-full mr-2 {{ $ticket->status == 'open' ? 'bg-blue-500' : ($ticket->status == 'resolved' ? 'bg-green-500' : 'bg-slate-400') }}"></div>
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('tickets.show', $ticket) }}"
                                class="inline-flex items-center px-3 py-1.5 bg-slate-800 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-slate-700 active:bg-slate-900 focus:outline-none focus:border-slate-900 focus:ring ring-slate-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver
                            </a>


                            <a href="{{ route('tickets.edit', $ticket) }}" class="text-blue-600 hover:text-blue-900 transition">Editar</a>
                            <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('¿Estás seguro de que quieres enviar este ticket a la papelera?')"
                                    class="text-red-500 hover:text-red-700 font-semibold transition">
                                    Eliminar
                                </button>
                            </form>


                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        No se encontraron tickets en la base de datos.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $tickets->links() }}
    </div>
</div>
@endsection