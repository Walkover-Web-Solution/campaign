<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidRequestException;
use Closure;
use Illuminate\Http\Request;

class AuthByAuthkeyMiddleware
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
        if (empty($request->header('authkey'))) {
            throw new InvalidRequestException("Unauthorized");
        }

        //used by MSG91 service 

        return $next($request);
    }
}
