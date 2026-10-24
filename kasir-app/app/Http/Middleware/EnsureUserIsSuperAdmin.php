<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperAdmin
{
    /**
     * Izinkan request hanya jika user yang masuk berperan super admin.
     *
     * Fitur pengaturan akun tidak tersedia untuk admin maupun kasir.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Halaman ini khusus untuk super admin.');
        }

        return $next($request);
    }
}
