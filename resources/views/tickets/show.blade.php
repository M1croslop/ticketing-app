@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-8">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-xl">
            <div>
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest">Ticket #{{ $ticket->id }}</span>
                <h2 class="text-2xl font-bold text-slate-800">{{ $ticket->title }}</h2>
            </div>
            <div class="flex gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    {{ $ticket->priority === 'urgent' ? 'bg-synapso-priority-urgent-bg text-synapso-priority-urgent-text' :
                      ($ticket->priority === 'high'   ? 'bg-synapso-priority-high-bg text-synapso-priority-high-text' :
                      ($ticket->priority === 'medium' ? 'bg-synapso-priority-mid-bg text-synapso-priority-mid-text' :
                                                        'bg-synapso-priority-low-bg text-synapso-priority-low-text')) }}">
                    {{ strtoupper($ticket->priority) }}
                </span>

                <span class="px-3 py-1 rounded-full text-xs font-bold
                    {{ $ticket->status === 'resolved' || $ticket->status === 'closed'
                        ? 'bg-synapso-status-done-bg text-synapso-status-done-text'
                      : ($ticket->status === 'in_progress'
                        ? 'bg-synapso-status-progress-bg text-synapso-status-progress-text'
                        : 'bg-synapso-status-open-bg text-synapso-status-open-text') }}">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                </span>

            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="md:col-span-2">
                <h3 class="text-sm font-bold text-slate-400 uppercase mb-2">Descripción</h3>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $ticket->description }}</p>
            </div>

            <div class="space-y-5 border-l border-slate-100 pl-6">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase">Creado por</h3>
                    <p class="text-sm font-semibold text-slate-800">{{ $ticket->user->name }}</p>
                    <p class="text-xs text-slate-500 italic">{{ $ticket->created_at->format('d/m/Y h:i A') }}</p>
                </div>

                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase">Categoría</h3>
                    <p class="text-sm font-semibold text-slate-800">{{ $ticket->category->name ?? 'General' }}</p>
                </div>

                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase mb-1">Estado</h3>

                    @if($canEdit)
                        <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                            @csrf @method('PATCH')
                            <select name="status" onchange="if(confirm('¿Confirmas el cambio de estado?')) this.form.submit();"
                                class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm">
                                @foreach(\App\Models\Ticket::STATUSES as $status)
                                    <option value="{{ $status }}" @selected($ticket->status === $status)>
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <p class="text-sm font-semibold text-slate-800">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </p>
                    @endif
                </div>

                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase mb-1">Prioridad</h3>

                    @if(auth()->user()->role === 'admin')
                        <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                            @csrf @method('PATCH')
                            <select name="priority" onchange="this.form.submit()"
                                class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm">
                                @foreach(\App\Models\Ticket::PRIORITIES as $priority)
                                    <option value="{{ $priority }}" @selected($ticket->priority === $priority)>
                                        {{ ucfirst($priority) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <p class="text-sm font-semibold text-slate-800">{{ ucfirst($ticket->priority) }}</p>
                    @endif
                </div>

                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase mb-1">Asignado a</h3>

                    @if(auth()->user()->role === 'admin')
                        <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                            @csrf @method('PATCH')
                            <select name="agent_id" onchange="this.form.submit()"
                                class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm">
                                <option value="">Sin asignar</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" @selected($ticket->agent_id === $agent->id)>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <p class="text-sm font-semibold text-slate-800">{{ $ticket->agent->name ?? 'Pendiente' }}</p>
                    @endif
                </div>

                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase mb-1">Fecha Límite (SLA)</h3>
                    <p class="text-sm font-semibold text-slate-800">
                        @if($ticket->due_date)
                            {{ $ticket->due_date->format('d/m/Y h:i A') }}
                            @if($ticket->due_date->isPast() && !in_array($ticket->status, ['resolved', 'closed']))
                                <span class="text-xs text-synapso-danger font-bold ml-1">(Vencido)</span>
                            @endif
                        @else
                            <span class="text-slate-400 italic">Pendiente de asignación</span>
                        @endif
                    </p>
                </div>

            </div>
        </div>
    </div>

    @if($ticket->statusLogs->isNotEmpty())
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-8 p-6">
        <h3 class="text-sm font-bold text-slate-400 uppercase mb-4">Historial de Estado</h3>
        <div class="space-y-2">
            @foreach($ticket->statusLogs->sortByDesc('changed_at') as $log)
            <div class="flex items-center gap-3 text-sm">
                <span class="w-2 h-2 rounded-full bg-synapso-navy flex-shrink-0"></span>
                <span class="text-slate-500 text-xs">{{ $log->changed_at->format('d/m/Y h:i A') }}</span>
                <span class="text-slate-700">
                    <span class="font-semibold">{{ $log->user->name ?? '—' }}</span>
                    cambió estado a
                    <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $log->status)) }}</span>
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'agent' || auth()->id() === $ticket->user_id)
    <div class="space-y-6">
        <h3 class="text-xl font-bold text-slate-800 flex items-center">
            <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            Hilo de Comentarios
        </h3>

        <div class="space-y-4">
            @forelse($ticket->comments as $comment)
            <div class="flex gap-4 {{ $comment->user_id === Auth::id() ? 'flex-row-reverse' : '' }}">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600">
                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                    </div>
                </div>
                <div class="max-w-[80%] bg-white border border-slate-200 p-4 rounded-2xl shadow-sm">
                    <div class="flex justify-between items-center mb-2 gap-4">
                        <span class="text-sm font-bold text-slate-900">{{ $comment->user->name }}</span>
                        <span class="text-[10px] text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-slate-700 leading-relaxed">{{ $comment->body }}</p>
                </div>
            </div>
            @empty
            <div class="bg-slate-50 border border-dashed border-slate-300 rounded-xl p-8 text-center text-slate-500 italic">
                No hay comentarios en este ticket aún.
            </div>
            @endforelse
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded-xl p-4 shadow-md">
            <form action="{{ route('tickets.comments.store', $ticket) }}" method="POST">
                @csrf
                <textarea name="body" rows="3"
                    class="w-full border-none focus:ring-0 text-sm text-slate-700 placeholder-slate-400"
                    placeholder="Escribe una respuesta o actualización..." required></textarea>
                <div class="flex justify-end mt-2 pt-2 border-t border-slate-100">
                    <button type="submit"
                        class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-700 transition">
                        Enviar Comentario
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center text-red-600">
        No tienes permisos para ver o agregar comentarios en este ticket.
    </div>
    @endif

</div>
@endsection