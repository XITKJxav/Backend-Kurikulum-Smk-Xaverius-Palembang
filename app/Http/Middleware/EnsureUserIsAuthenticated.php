<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Anda harus login terlebih dahulu'
            ], 401);
        }

        return $next($request);
    }
}
