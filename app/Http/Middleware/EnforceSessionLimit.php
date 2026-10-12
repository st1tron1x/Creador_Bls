<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Duración MÁXIMA ABSOLUTA de la sesión (independiente de la actividad).
 * La inactividad la controla SESSION_LIFETIME (minutos); esto controla SESSION_MAX_HOURS.
 * Con SESSION_MAX_HOURS=0 se desactiva.
 */
class EnforceSessionLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $maxHours = (int) config('session.max_hours', 0);

        if (! $user || $maxHours <= 0) {
            return $next($request);
        }

        $session = $request->session();

        // Marca el inicio de esta sesión (y la reinicia si cambió el usuario autenticado).
        if ($session->get('session_started_for') !== $user->id || ! $session->has('session_started_at')) {
            $session->put('session_started_for', $user->id);
            $session->put('session_started_at', now()->timestamp);

            return $next($request);
        }

        if (now()->timestamp - (int) $session->get('session_started_at') > $maxHours * 3600) {
            Auth::guard('web')->logout();
            $session->invalidate();
            $session->regenerateToken();

            // Livewire (XHR): 419 -> el navegador recarga y cae en el login.
            if ($request->hasHeader('X-Livewire')) {
                abort(419);
            }

            return redirect()->route('login')
                ->with('status', 'Tu sesión superó el tiempo máximo permitido. Ingresa nuevamente.');
        }

        return $next($request);
    }
}
