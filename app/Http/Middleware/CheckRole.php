<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles[] = $role;
            if ($role === 'dokter_hewan') {
                $allowedRoles[] = 'dokter';
            } elseif ($role === 'dokter') {
                $allowedRoles[] = 'dokter_hewan';
            } elseif ($role === 'pemilik_hewan') {
                $allowedRoles[] = 'pemilik';
            } elseif ($role === 'pemilik') {
                $allowedRoles[] = 'pemilik_hewan';
            }
        }

        if (! in_array($user->role, $allowedRoles, true)) {
            abort(403, 'Akses tidak diizinkan.');
        }

        return $next($request);
    }
}
