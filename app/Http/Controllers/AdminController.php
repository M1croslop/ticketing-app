<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;

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

    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name'                  => 'required|string|min:3|max:100',
            'email'                 => 'required|email|max:255|unique:users,email',
            'role'                  => 'required|in:admin,agent,client',
            'password'              => 'required|string|min:8|confirmed',
        ], [
            'name.required'         => 'El nombre es obligatorio.',
            'name.min'              => 'El nombre debe tener al menos 3 caracteres.',
            'email.required'        => 'El correo es obligatorio.',
            'email.email'           => 'El correo no tiene un formato válido.',
            'email.unique'          => 'Este correo ya está registrado en el sistema.',
            'role.required'         => 'Debes seleccionar un rol.',
            'role.in'               => 'El rol seleccionado no es válido.',
            'password.required'     => 'La contraseña es obligatoria.',
            'password.min'          => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'    => 'Las contraseñas no coinciden.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->role = $request->role;
        $user->save();

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
        return view('admin.stats', [
            'users' => User::count(),
            'tickets' => Ticket::count(),
            'admins' => User::where('role', 'admin')->count(),
            'agents' => User::where('role', 'agent')->count(),
            'clients' => User::where('role', 'client')->count(),
        ]);
    }
}