<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Izinkan request hanya jika user yang masuk berperan admin atau super admin.
     *
     * Super admin selalu lolos pembatasan admin/kasir karena merupakan role tertinggi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasAdminAccess()) {
            return redirect()->route('dashboard')->with('error', 'Halaman ini khusus untuk admin.');
        }

        return $next($request);
    }
}
