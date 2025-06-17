<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Não autenticado'], 401);
        }

        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Acesso negado. Apenas administradores.'], 403);
        }

        return $next($request);
    }
}