<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next){
        $header = $request->header('Authorization');
        $expected = 'Bearer ' . env('API_TOKEN');

        if ($header !== $expected){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
