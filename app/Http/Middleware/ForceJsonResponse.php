<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    /**
     * Manipula a requisição.
     */
    public function handle(Request $request, Closure $next)
    {
        // Se for rota de API ou cliente espera JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
