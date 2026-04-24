@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Editar Ticket #{{ $ticket->id }}</h2>
            <p class="text-slate-500 text-sm">Actualiza la información y asignación del caso.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
            Creado el: {{ $ticket->created_at->format('d/m/Y') }}
        </span>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm">
        <form action="{{ route('tickets.update', $ticket) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Título</label>
                    <input type="text" name="title" value="{{ old('title', $ticket->title) }}" required 
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Categoría</label>
                        <select name="category_id" required class="w-full rounded-lg border-slate-300">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('category_id', $ticket->category_id) == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Asignar a Agente</label>
                        <select name="agent_id" class="w-full rounded-lg border-slate-300">
                            <option value="">Sin asignar</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ (old('agent_id', $ticket->agent_id) == $agent->id) ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Estado</label>
                        <select name="status" required class="w-full rounded-lg border-slate-300">
                            <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Abierto</option>
                            <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                            <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resuelto</option>
                            <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Cerrado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Prioridad</label>
                        <select name="priority" required class="w-full rounded-lg border-slate-300">
                            <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>Baja</option>
                            <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>Media</option>
                            <option value="high" {{ $ticket->priority == 'high' ? 'selected' : '' }}>Alta</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Fecha Límite</label>
                        <input type="date" name="due_date" 
                            value="{{ old('due_date', $ticket->due_date ? $ticket->due_date->format('Y-m-d') : '') }}" 
                            class="w-full rounded-lg border-slate-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Descripción del problema</label>
                    <textarea name="description" rows="4" required 
                        class="w-full rounded-lg border-slate-300">{{ old('description', $ticket->description) }}</textarea>
                </div>

                <div class="flex justify-between items-center pt-6 border-t border-slate-100">
                    @if($ticket->resolved_at)
                        <span class="text-xs text-green-600 font-medium">
                            ✓ Resuelto el: {{ $ticket->resolved_at->format('d/m/Y H:i') }}
                        </span>
                    @else
                        <div></div>
                    @endif

                    <div class="flex space-x-3">
                        <a href="{{ route('tickets.index') }}" class="px-4 py-2 text-slate-600 hover:text-slate-800 font-semibold transition">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-lg font-bold shadow-md transition">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection