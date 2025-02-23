<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureToken
{
    public function handle(Request $request, Closure $next)
    {
       

        if (empty($request->bearerToken())) {
            return response()->json(['error' => 'No API token provided.'], 401);
        }

        if (! Auth::guard('sanctum')->check()) {
            return response()->json(['error' => 'Invalid API token.'], 401);
        }

        return $next($request);
    }
}
