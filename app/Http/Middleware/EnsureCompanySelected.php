<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanySelected
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $activeId = session('active_company_id');
        $belongsToActive = $activeId && $user->companies()->where('companies.id', $activeId)->exists();

        if ($belongsToActive) {
            return $next($request);
        }

        // Si solo pertenece a una empresa, se activa sola sin pedir nada.
        if ($user->companies()->count() === 1) {
            session(['active_company_id' => $user->companies()->first()->id]);

            return $next($request);
        }

        // Si tiene más de una empresa y ninguna activa en sesión, que elija --
        // guardamos a dónde iba (para volver ahí después de elegir), SIN cambiar
        // el destino de esta redirección (ese fue el bug: usar setTargetUrl aquí
        // apuntaba la redirección de vuelta a la misma URL que la disparó = bucle infinito).
        if (! $request->routeIs('company.select')) {
            session(['url.intended' => $request->fullUrl()]);
        }

        return redirect()->route('company.select');
    }
}