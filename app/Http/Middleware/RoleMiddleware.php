<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda harus login untuk mengakses resource ini.',
            ], 401);
        }

        if (!$request->user()->hasRole($role)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengakses resource ini.',
                'required_role' => $role,
                'your_role' => $request->user()->getRoleNames(), // Mengambil semua role user
            ], 403);
        }

        return $next($request);
    }
}
