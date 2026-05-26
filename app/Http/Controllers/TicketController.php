<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class TicketController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user       = Auth::user();
        $status     = $request->get('status');
        $search     = $request->get('search');
        $categoryId = $request->get('category_id');
        $dateRange  = $request->get('date_range', 'all');

        $baseQuery = Ticket::with(['user', 'agent', 'category'])
            ->when($user->role === 'client', fn($q) => $q->where('user_id', $user->id))
            ->when($user->role === 'agent',  fn($q) => $q->where('agent_id', $user->id));

        $tickets = (clone $baseQuery)
            ->when($status, fn($q, $s) => $q->where('status', $s))
            ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
            ->when($search, function ($q, $s) {
                $safe = str_replace(['%', '_'], ['\%', '\_'], $s);
                $q->where('title', 'like', "%{$safe}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $agents     = $user->role === 'admin' ? User::agents()->orderBy('name')->get() : collect();
        $categories = Category::orderBy('name')->get();

        $activeCount       = 0;
        $resolvedToday     = 0;
        $criticalCount     = 0;
        $avgResponseTime   = null;

        if ($user->role === 'agent') {
            $activeCount = (clone $baseQuery)
                ->whereIn('status', ['open', 'in_progress'])
                ->count();

            $resolvedToday = (clone $baseQuery)
                ->whereIn('status', ['resolved', 'closed'])
                ->whereDate('updated_at', Carbon::today())
                ->count();

            $criticalCount = (clone $baseQuery)
                ->where('priority', 'urgent')
                ->whereIn('status', ['open', 'in_progress'])
                ->count();

            $avgRaw = (clone $baseQuery)
                ->whereNotNull('resolved_at')
                ->whereNotNull('due_date')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, resolved_at, due_date)) as avg_min')
                ->value('avg_min');

            $avgResponseTime = $avgRaw !== null ? round(abs($avgRaw) / 60, 1) : 4.2;
        }

        $totalOpen           = 0;
        $escalatedCount      = 0;
        $agentEfficiencyPct  = 85;

        if ($user->role === 'admin') {
            $totalOpen = Ticket::where('status', 'open')->count();

            $escalatedCount = Ticket::whereIn('priority', ['urgent', 'high'])
                ->whereIn('status', ['open', 'in_progress'])
                ->count();

            $totalTickets  = Ticket::count();
            $totalResolved = Ticket::whereIn('status', ['resolved', 'closed'])->count();
            $agentEfficiencyPct = $totalTickets > 0
                ? round(($totalResolved / $totalTickets) * 100)
                : 85;
        }

        return view('tickets.index', compact(
            'tickets', 'agents', 'categories',
            'status', 'search', 'categoryId', 'dateRange',
            'activeCount', 'resolvedToday', 'criticalCount', 'avgResponseTime',
            'totalOpen', 'escalatedCount', 'agentEfficiencyPct'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $agents     = User::agents()->orderBy('name')->get();

        return view('tickets.create', compact('categories', 'agents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        $validated = $request->validated();

        $ticket          = new Ticket($validated);
        $ticket->user_id = Auth::id();
        $ticket->status  = 'open';
        $ticket->save();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['user', 'agent', 'category', 'comments.user', 'statusLogs.user']);

        $agents     = User::agents()->orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $canEdit    = $ticket->canBeEditedBy(Auth::user());

        return view('tickets.show', compact('ticket', 'agents', 'categories', 'canEdit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $categories = Category::orderBy('name')->get();
        $agents     = User::agents()->orderBy('name')->get();

        return view('tickets.edit', compact('ticket', 'categories', 'agents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $ticket->update($request->validated());

        if ($request->boolean('_redirect_back')) {
            return redirect()->route('tickets.show', $ticket)
                ->with('success', 'Ticket actualizado correctamente.');
        }

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket actualizado correctamente.');
    }

    public function take(Ticket $ticket)
    {
        $this->authorize('take', $ticket);

        $updated = \Illuminate\Support\Facades\DB::transaction(function () use ($ticket) {
            $t = Ticket::where('id', $ticket->id)->lockForUpdate()->first();
            if ($t && is_null($t->agent_id)) {
                $t->agent_id = Auth::id();
                $t->save();
                return true;
            }
            return false;
        });

        if (! $updated) {
            return redirect()->route('tickets.show', $ticket)
                ->with('warning', 'Este ticket ya fue tomado por otro agente.');
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket tomado correctamente. El SLA ha sido calculado.');
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