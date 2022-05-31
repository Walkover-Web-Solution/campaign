<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidRequestException;
use App\Models\Token;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthByTokenMiddleware
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
        if (empty($request->header('token'))) {
            throw new InvalidRequestException("Invaild Request");
        }

        // gets token object from db whose token is equal to the header token
        $token = Token::where('token', $request->header('token'))->first();
        if (empty($token)) {
            throw new InvalidRequestException("Unauthorized");
        }

        //gets campaign by slug passed with request
        $campaign = $token->company->campaigns()->where('slug', $request->slug)->first();
        if (empty($campaign)) {
            throw new NotFoundHttpException("Invalid Campaign");
        }

        //checks uf token's id is equal to the cmapiagn's company token id
        if ($campaign->token_id != $token->id) {
            throw new InvalidRequestException('Unauthorized, Invalid Request');
        }

        return $next($request);
    }
}
