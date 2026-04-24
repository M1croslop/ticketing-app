@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Crear Nuevo Ticket</h2>
        <p class="text-slate-500 text-sm">Ingresa los detalles para la apertura del nuevo caso.</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm">
        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Título del Incidente</label>
                    <input type="text" name="title" value="{{ old('title') }}" required 
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" 
                        placeholder="Ej: Error al conectar con la base de datos">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Categoría</label>
                        <select name="category_id" required class="w-full rounded-lg border-slate-300">
                            <option value="" disabled selected>Selecciona una categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Asignar a Agente (Opcional)</label>
                        <select name="agent_id" class="w-full rounded-lg border-slate-300">
                            <option value="">Dejar sin asignar</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ old('agent_id') == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Prioridad</label>
                        <select name="priority" required class="w-full rounded-lg border-slate-300">
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Baja</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Media</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Fecha Límite (Opcional)</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" 
                            class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Descripción Detallada</label>
                    <textarea name="description" rows="5" required 
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" 
                        placeholder="Escribe aquí los detalles del problema...">{{ old('description') }}</textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 text-slate-600 hover:text-slate-800 font-semibold transition">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow-md transition">
                        Crear Ticket
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection