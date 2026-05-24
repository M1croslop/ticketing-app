<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use Carbon\Carbon;


class AdminController extends Controller
{
    public function users(Request $request)
    {
        // Totales globales (independientes de los filtros activos)
        $totalActive    = User::count();
        $totalSuspended = User::onlyTrashed()->count();

        $query = User::withTrashed()
            ->withCount(['tickets', 'assignedTickets']);

        // Filtro: búsqueda por nombre o email (case-insensitive)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro: rol exacto
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Filtro: estado activo / suspendido
        if ($request->input('status') === 'active') {
            $query->whereNull('deleted_at');
        } elseif ($request->input('status') === 'suspended') {
            $query->whereNotNull('deleted_at');
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users', compact('users', 'totalActive', 'totalSuspended'));
    }

    public function createUser(): View
    {
        return view('admin.create-user');
    }

    public function storeUser(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users')
            ->with('success', "Usuario {$user->name} creado correctamente.");
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,agent,client',
        ]);

        $user->update([
            'role' => $request->role,
        ]);

        return back()->with('success', 'Rol actualizado correctamente');
    }

    public function destroyUser(User $user)
    {
        $user->delete();

        return back()->with('success', 'Usuario suspendido correctamente');
    }

    public function restoreUser(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $user->restore();

        return back()->with('success', 'Usuario restaurado correctamente');
    }

    public function stats()
    {
        // ── 1. Total de tickets por categoría ───────────────────────────────
        $ticketsByCategory = Category::withCount([
            'tickets',
            'tickets as open_count'        => fn($q) => $q->where('status', 'open'),
            'tickets as in_progress_count' => fn($q) => $q->where('status', 'in_progress'),
            'tickets as resolved_count'    => fn($q) => $q->whereIn('status', ['resolved', 'closed']),
        ])
        ->orderByDesc('tickets_count')
        ->get();
 
        $maxCategoryCount = $ticketsByCategory->max('tickets_count') ?: 1;
 
        // ── 2. Tiempo promedio de resolución (horas) ────────────────────────
        $avgResolutionTime = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');
 
        $avgResolutionTime = $avgResolutionTime ? round($avgResolutionTime, 1) : null;
 
        // ── 3. Tickets vencidos: now() > due_date y status != resolved ───────
        $overdueTickets = Ticket::with(['user', 'agent', 'category'])
            ->whereNotIn('status', ['resolved', 'closed'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->get();
 
        // ── Totales de tickets ──────────────────────────────────────────────
        $totalTickets    = Ticket::count();
        $openCount       = Ticket::where('status', 'open')->count();
        $inProgressCount = Ticket::where('status', 'in_progress')->count();
        $resolvedCount   = Ticket::whereIn('status', ['resolved', 'closed'])->count();
 
        // ── Cumplimiento SLA ────────────────────────────────────────────────
        $slaComplianceRate = Ticket::slaComplianceRate();
 
        // ── Tabla CRUD de usuarios (sin contraseñas ni datos sensibles) ──────
        $allUsers = User::withTrashed()
            ->withCount(['tickets', 'assignedTickets'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();
 
        // ── Conteos de usuarios (compatibilidad con la vista original) ───────
        $users   = User::count();
        $admins  = User::where('role', 'admin')->count();
        $agents  = User::where('role', 'agent')->count();
        $clients = User::where('role', 'client')->count();
 
        return view('admin.stats', compact(
            // los tres requeridos
            'ticketsByCategory',
            'avgResolutionTime',
            'overdueTickets',
            // KPIs de tickets
            'maxCategoryCount',
            'totalTickets',
            'openCount',
            'inProgressCount',
            'resolvedCount',
            'slaComplianceRate',
            // tabla de usuarios
            'allUsers',
            // conteos de usuarios (compatibilidad)
            'users',
            'admins',
            'agents',
            'clients'
        ));
    }

    public function export(Request $request)
    {
        $request->validate([
            'sections'   => 'required|array',
            'sections.*' => 'in:kpis,categories,overdue,agents,users',
        ]);

        $sections = $request->input('sections', []);

        // Siempre incluimos la cabecera; el resto es opcional
        $data = [
            'sections'    => $sections,
            'generatedAt' => now()->format('d/m/Y H:i'),
            'generatedBy' => auth()->user()->name,
        ];

        if (in_array('kpis', $sections)) {
            $data['totalTickets']    = Ticket::count();
            $data['openCount']       = Ticket::where('status', 'open')->count();
            $data['inProgressCount'] = Ticket::where('status', 'in_progress')->count();
            $data['resolvedCount']   = Ticket::whereIn('status', ['resolved', 'closed'])->count();

            $data['slaComplianceRate'] = Ticket::slaComplianceRate();

            $data['avgResolutionTime'] = Ticket::whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
                ->value('avg_hours');

            $data['avgResolutionTime'] = $data['avgResolutionTime']
                ? round($data['avgResolutionTime'], 1)
                : null;
        }

        if (in_array('categories', $sections)) {
            $data['ticketsByCategory'] = Category::withCount([
                'tickets',
                'tickets as open_count'        => fn($q) => $q->where('status', 'open'),
                'tickets as in_progress_count' => fn($q) => $q->where('status', 'in_progress'),
                'tickets as resolved_count'    => fn($q) => $q->whereIn('status', ['resolved', 'closed']),
            ])
            ->orderByDesc('tickets_count')
            ->get();
        }

        if (in_array('overdue', $sections)) {
            $data['overdueTickets'] = Ticket::with(['user', 'agent', 'category'])
                ->whereNotIn('status', ['resolved', 'closed'])
                ->whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->orderBy('due_date')
                ->get();
        }

        if (in_array('agents', $sections)) {
            $data['topAgents'] = User::where('role', 'agent')
                ->withCount([
                    'assignedTickets as resolved_count' => fn($q) =>
                        $q->whereIn('status', ['resolved', 'closed']),
                    'assignedTickets as active_count' => fn($q) =>
                        $q->whereIn('status', ['open', 'in_progress']),
                ])
                ->orderByDesc('resolved_count')
                ->get();
        }

        if (in_array('users', $sections)) {
            $data['allUsers'] = User::withTrashed()
                ->withCount(['tickets', 'assignedTickets'])
                ->orderBy('role')
                ->orderBy('name')
                ->get();
        }

        return response()
            ->view('admin.stats-export', $data)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }


}