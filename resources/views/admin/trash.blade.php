@extends('layouts.app')

@section('content')
@php
    $priorityBadge = [
        'urgent' => ['label' => 'CRÍTICO', 'classes' => 'bg-red-50 text-red-600 border border-red-200'],
        'high'   => ['label' => 'ALTA',    'classes' => 'bg-red-50 text-orange-600 border border-orange-200'],
        'medium' => ['label' => 'MEDIA',   'classes' => 'bg-amber-50 text-amber-600 border border-amber-200'],
        'low'    => ['label' => 'BAJA',    'classes' => 'bg-blue-50 text-blue-600 border border-blue-200'],
    ];
    $statusBadge = [
        'open'        => ['label' => 'Abierto',     'classes' => 'bg-blue-100 text-blue-700'],
        'in_progress' => ['label' => 'En Progreso', 'classes' => 'bg-amber-100 text-amber-800'],
        'resolved'    => ['label' => 'Resuelto',    'classes' => 'bg-emerald-100 text-emerald-800'],
        'closed'      => ['label' => 'Cerrado',     'classes' => 'bg-slate-100 text-slate-700'],
    ];
@endphp

<div class="min-h-screen bg-synapso-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Administración</p>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Papelera de Tickets</h1>
                <p class="mt-1.5 text-sm text-slate-500 font-medium">
                    Tickets eliminados<br>
                    Restáuralos o elimínalos definitivamente.
                </p>
            </div>
            <a href="{{ route('tickets.index') }}"
               class="inline-flex items-center gap-2 border border-slate-200 bg-white hover:bg-slate-50
                      text-slate-700 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition duration-150 self-start md:self-auto">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Tickets
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                <h2 class="text-sm font-bold text-slate-800 tracking-tight">Tickets en papelera</h2>
                <span class="text-xs font-semibold text-slate-500">{{ $tickets->count() }} eliminado(s)</span>
            </div>

            <div class="overflow-x-auto">
                @if($tickets->isEmpty())
                    <div class="py-12 text-center text-slate-400 text-sm">No hay tickets en la papelera.</div>
                @else
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Título</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Categoría</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Prioridad</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden md:table-cell">Cliente</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden lg:table-cell">Eliminado el</th>
                                <th class="px-4 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($tickets as $ticket)
                                @php
                                    $prefix = in_array($ticket->priority, ['urgent', 'high']) ? 'INC' : 'REQ';
                                    $pb = $priorityBadge[$ticket->priority] ?? ['label' => $ticket->priority, 'classes' => 'bg-slate-100 text-slate-600 border border-slate-200'];
                                    $sb = $statusBadge[$ticket->status] ?? ['label' => $ticket->status, 'classes' => 'bg-slate-100 text-slate-700'];
                                @endphp
                                <tr class="bg-slate-100/70 hover:bg-slate-200/50 transition duration-100">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="text-xs font-bold text-slate-500">#{{ $prefix }}-{{ sprintf('%04d', $ticket->id) }}</span>
                                    </td>
                                    <td class="px-4 py-4 max-w-[220px]">
                                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $ticket->title }}</p>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap hidden lg:table-cell text-sm text-slate-600">
                                        {{ $ticket->category?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $pb['classes'] }}">
                                            {{ $pb['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap hidden md:table-cell text-sm text-slate-600">
                                        {{ $ticket->user?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sb['classes'] }}">
                                            {{ $sb['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap hidden lg:table-cell text-xs text-slate-500">
                                        {{ $ticket->deleted_at?->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('admin.trash.restore', $ticket->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold
                                                               bg-emerald-50 text-emerald-700 border border-emerald-200
                                                               hover:bg-emerald-100 transition">
                                                    Restaurar
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.trash.force-delete', $ticket->id) }}" method="POST"
                                                  id="force-delete-form-{{ $ticket->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold
                                                               bg-red-50 text-red-700 border border-red-200
                                                               hover:bg-red-100 transition"
                                                        onclick="window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                                                            detail: {
                                                                title: 'Eliminar definitivamente',
                                                                message: '¿Eliminar el ticket #{{ $ticket->id }} de forma permanente? Esta acción no se puede deshacer.',
                                                                confirmText: 'Eliminar definitivamente',
                                                                cancelText: 'Cancelar',
                                                                confirmButtonClass: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
                                                                onConfirm: function () { document.getElementById('force-delete-form-{{ $ticket->id }}').submit(); }
                                                            }
                                                        }))">
                                                    Eliminar definitivamente
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
