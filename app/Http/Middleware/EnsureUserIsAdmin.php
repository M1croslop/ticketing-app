<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Bloquea el acceso si el usuario autenticado no tiene el rol 'admin'.
     * Redirige al dashboard con un mensaje de error en lugar de lanzar un 403,
     * para evitar exponer la existencia de rutas de administración.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()?->role !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para acceder a esa sección.');
        }

        return $next($request);
    }
}
