<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users()
    {
        $users = User::latest()->get();

        return view('admin.users', compact('users'));
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

        return back()->with('success', 'Usuario eliminado');
    }

    public function restoreUser(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $user->restore();

        return back()->with('success', 'Usuario restaurado');
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