@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-synapso-bg">

    <div class="mb-6 flex justify-between">
        <div>
            <h2 class="text-2xl font-bold text-synapso-navy">
                Editar Ticket #{{ $ticket->id }}
            </h2>
            <p class="text-slate-500 text-sm">Actualizar información del ticket.</p>
        </div>

        <span class="text-xs text-slate-500">
            {{ $ticket->created_at->format('d/m/Y') }}
        </span>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-6">

        <form method="POST" action="{{ route('tickets.update', $ticket) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">

                {{-- TITULO --}}
                <div>
                    <label class="text-sm font-semibold text-slate-700">Título</label>
                    <input type="text" name="title"
                        value="{{ old('title', $ticket->title) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2">
                </div>

                {{-- DESCRIPCIÓN --}}
                <div>
                    <label class="text-sm font-semibold text-slate-700">Descripción</label>
                    <textarea name="description" rows="4"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2">{{ old('description', $ticket->description) }}</textarea>
                </div>

                {{-- GRID --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- ESTADO --}}
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Estado</label>
                        <select name="status"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2">

                            @foreach(\App\Models\Ticket::STATUSES as $status)
                                <option value="{{ $status }}" @selected(old('status', $ticket->status) === $status)>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PRIORIDAD --}}
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Prioridad</label>
                        <select name="priority"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2">

                            @foreach(\App\Models\Ticket::PRIORITIES as $priority)
                                <option value="{{ $priority }}" @selected(old('priority', $ticket->priority) === $priority)>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach

                        </select>
                        @error('priority')
                            <p class="text-synapso-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- CATEGORIA --}}
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Categoría</label>
                        <select name="category_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2">

                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $ticket->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                </div>

                {{-- BOTONES --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">

                    <a href="{{ route('tickets.index') }}"
                        class="border border-slate-300 px-4 py-2 rounded-lg text-sm">
                        Cancelar
                    </a>

                    <button
                        class="bg-synapso-navy text-white px-5 py-2 rounded-lg text-sm font-semibold">
                        Guardar
                    </button>

                </div>

            </div>
        </form>

    </div>
</div>
@endsection