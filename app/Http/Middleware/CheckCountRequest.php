<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckCountRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        empty(Cache::get('chatBot')) ? Cache::put('chatBot', 1) : Cache::increment('chatBot');

        if (Cache::get('chatBot') > 3) {
            Cache::forget('chatBot');
            return response()->json(['message' => 'Limite de Requisições atingidas, tente novamente em poucos minutos'], 404);
        }

        return $next($request);
    }
}
