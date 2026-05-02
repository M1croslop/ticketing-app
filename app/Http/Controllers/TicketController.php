<?php

namespace App\Http\Controllers;


use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;


class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
           $tickets = Ticket::with(['user', 'agent', 'category'])
        ->when(request('status'), fn($q, $status) => $q->where('status', $status))
        ->when(request('search'), fn($q, $search) => $q->where('title', 'like', "%$search%"))
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $agents = User::where('role', 'agent')->orWhere('role', 'admin')->get();
        return view('tickets.create', compact('categories', 'agents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        $validated = $request->validated();

        // Asignamos el ID del usuario autenticado automáticamente
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'open';

        try {
            Ticket::create($validated);

            return redirect()->route('tickets.index')
                ->with('success', 'Ticket creado con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'No se pudo crear el ticket. Intenta de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'agent', 'category', 'comments.user', 'statusLogs.user']);
        $agents = User::where('role', 'agent')->orWhere('role', 'admin')->get();
        return view('tickets.show', compact('ticket', 'agents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        $categories = Category::all();
        $agents = User::where('role', 'agent')->orWhere('role', 'admin')->get();
        return view('tickets.edit', compact('ticket', 'categories', 'agents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $validated = $request->validated();

        if ($request->status === 'resolved' && !$ticket->resolved_at) {
            $validated['resolved_at'] = now();
        }

        $ticket->update($validated);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        // Solo el propietario del ticket o un admin pueden eliminarlo
        if (Auth::id() !== $ticket->user_id && Auth::user()->role !== 'admin') {
            return redirect()->route('tickets.index')
                ->with('error', 'No tienes permisos para eliminar este ticket.');
        }

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('warning', 'Ticket enviado a la papelera.');
    }
}
