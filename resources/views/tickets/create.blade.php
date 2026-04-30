@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-synapso-bg">

    {{-- HEADER --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-synapso-navy">Crear Ticket</h2>
        <p class="text-slate-500 text-sm">Registrar un nuevo incidente en el sistema.</p>
    </div>

    {{-- CARD --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6">

        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf

            <div class="space-y-6">

                {{-- TITULO --}}
                <div>
                    <label class="block text-sm font-semibold text-synapso-navy mb-1">
                        Título
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm
                               focus:ring-1 focus:ring-synapso-navy">

                    @error('title')
                        <p class="text-synapso-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- GRID --}}
                <div class="grid md:grid-cols-2 gap-4">

                    {{-- CATEGORIA --}}
                    <div>
                        <label class="text-sm font-semibold text-synapso-navy mb-1 block">
                            Categoría
                        </label>
                        <select name="category_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">

                            <option value="">Seleccionar</option>

                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-synapso-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- AGENTE --}}
                    <div>
                        <label class="text-sm font-semibold text-synapso-navy mb-1 block">
                            Agente
                        </label>
                        <select name="agent_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">

                            <option value="">Sin asignar</option>

                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- GRID 2 --}}
                <div class="grid md:grid-cols-2 gap-4">

                    {{-- PRIORIDAD --}}
                    <div>
                        <label class="text-sm font-semibold text-synapso-navy mb-1 block">
                            Prioridad
                        </label>
                        <select name="priority"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">

                            @foreach(\App\Models\Ticket::PRIORITIES as $priority)
                                <option value="{{ $priority }}" @selected(old('priority', 'medium') === $priority)>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach

                        </select>
                        @error('priority')
                            <p class="text-synapso-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- FECHA --}}
                    <div>
                        <label class="text-sm font-semibold text-synapso-navy mb-1 block">
                            Fecha límite
                        </label>
                        <input type="date" name="due_date"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                    </div>

                </div>

                {{-- DESCRIPCION --}}
                <div>
                    <label class="text-sm font-semibold text-synapso-navy mb-1 block">
                        Descripción
                    </label>
                    <textarea name="description" rows="4"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-synapso-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ACTIONS --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">

                    <a href="{{ route('tickets.index') }}"
                        class="border border-slate-300 px-4 py-2 rounded-lg text-sm">
                        Cancelar
                    </a>

                    <button
                        class="bg-synapso-navy text-white px-5 py-2 rounded-lg text-sm font-semibold">
                        Crear Ticket
                    </button>

                </div>

            </div>

        </form>

    </div>
</div>
@endsection