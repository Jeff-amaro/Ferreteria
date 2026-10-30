<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->active) {
            auth()->logout();

            return redirect()->route('login')->with('error', 'Tu cuenta se encuentra inactiva.');
        }

        if (! $user->hasRole($roles)) {
            abort(403, 'No tienes permisos para acceder a este módulo.');
        }

        return $next($request);
    }
}
