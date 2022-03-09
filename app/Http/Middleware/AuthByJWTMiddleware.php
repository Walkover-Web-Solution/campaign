<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;

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
            throw new \Exception('Invalid Request');
        }

        try {
            $value = $request->header('authorization');
            $res = JWTDecode($value);
        } catch (\Exception $e) {
            throw new \Exception('Unauthorized');
        }

        return $next($request);
    }
}
