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
        $user = Auth::user();
        $tickets = Ticket::with(['user', 'agent', 'category'])
            ->when($user->role === 'client', fn($q) => $q->where('user_id', $user->id))
            ->when(request('status'), fn($q, $status) => $q->where('status', $status))
            ->when(request('search'), function ($q, $search) {
                $safe = str_replace(['%', '_'], ['\%', '\_'], $search);
                $q->where('title', 'like', "%{$safe}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $agents = $user->role === 'admin' ? User::agents()->get() : collect();

        return view('tickets.index', compact('tickets', 'agents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $agents = User::agents()->get();
        return view('tickets.create', compact('categories', 'agents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        $validated = $request->validated();

        $ticket = new Ticket($validated);
        $ticket->user_id = Auth::id();
        $ticket->status = 'open';
        $ticket->save();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'agent', 'category', 'comments.user', 'statusLogs.user']);
        $agents = User::agents()->get();
        $canEdit = $ticket->canBeEditedBy(Auth::user());
        return view('tickets.show', compact('ticket', 'agents', 'canEdit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        $categories = Category::all();
        $agents = User::agents()->get();
        return view('tickets.edit', compact('ticket', 'categories', 'agents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $validated = $request->validated();

        $ticket->update($validated);

        return redirect()->route('tickets.index')
        ->with('success', 'Ticket actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', $ticket);

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('warning', 'Ticket enviado a la papelera.');
    }
}
