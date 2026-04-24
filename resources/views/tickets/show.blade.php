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
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-white border border-slate-300 text-slate-600">
                    Prioridad: {{ strtoupper($ticket->priority) }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-600 text-white">
                    Estado: {{ ucfirst($ticket->status) }}
                </span>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <h3 class="text-sm font-bold text-slate-400 uppercase mb-2">Descripción</h3>
                <p class="text-slate-700 whitespace-pre-wrap">{{ $ticket->description }}</p>
            </div>
            <div class="space-y-4 border-l border-slate-100 pl-6">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase">Creado por</h3>
                    <p class="text-sm font-semibold text-slate-800">{{ $ticket->user->name }}</p>

                    <p class="text-xs text-slate-500 italic">{{ $ticket->created_at->format('d/m/Y h:i A') }}</p>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase">Asignado a</h3>
                    <p class="text-sm font-semibold text-slate-800">{{ $ticket->agent->name ?? 'Pendiente' }}</p>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase">Categoría</h3>
                    <p class="text-sm font-semibold text-slate-800">{{ $ticket->category->name ?? 'General' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-bold text-slate-800 flex items-center">
            <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
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
                        <p class="text-sm text-slate-700 leading-relaxed">
                            {{ $comment->message }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="bg-slate-50 border border-dashed border-slate-300 rounded-xl p-8 text-center text-slate-500 italic">
                    No hay comentarios en este ticket aún.
                </div>
            @endforelse
        </div>

        <div class="mt-8 bg-white border border-slate-200 rounded-xl p-4 shadow-md">
            <form action="#" method="POST"> {{-- Aquí pondrás la ruta cuando hagas el CommentController --}}
                @csrf
                <textarea name="message" rows="3" class="w-full border-none focus:ring-0 text-sm text-slate-700 placeholder-slate-400" placeholder="Escribe una respuesta o actualización..."></textarea>
                <div class="flex justify-end mt-2 pt-2 border-t border-slate-100">
                    <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-700 transition">
                        Enviar Comentario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection