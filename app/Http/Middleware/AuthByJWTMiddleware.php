<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;

class AuthByJWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty($request->header('authorization'))) {
            throw new \Exception('Unauthorized', 1);
        }

        try {
            $res = JWTDecode($request->header('authrization'));
        } catch (\Exception $e) {
            throw new \Exception('Unauthorized', 2);
        }

        return $next($request);
    }
}
