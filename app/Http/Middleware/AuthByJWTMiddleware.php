<?php

namespace App\Http\Middleware;

use App\Http\Resources\CustomResource;
use App\Models\Company;
use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Cache\RateLimiter;

class AuthByJWTMiddleware
{
    public $limiter;
    protected $ip;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
        if (request()->header('userip')) {
            $ips = explode(',', request()->header('userip'));
            $this->ip = $ips[0];
        } elseif (request()->header('cf-connecting-ip')) {
            $this->ip = request()->header('cf-connecting-ip');
        } else {
            $this->ip = request()->ip();
        }
        request()->merge([
            'ip' => $this->ip
        ]);
    }
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

            // checks if the request is to register.
            if (\Request::getRequestUri() == "/api/register") {
                return $next($request);
            }

            // get company whose ref_id matches with the company's id found in key
            $company = Company::where('ref_id', $res->company->id)->first();
            if (empty($company)) {
                $response = new CustomResource(["message" => "invalid request"], true);
                return response()->json($response, 404);
            }

            // get user whose company matches with the company passed in key
            $user = User::where('company_id', $company->id)->first();
            if (empty($user)) {
                $response = new CustomResource(["message" => "invalid request"], true);
                return response()->json($response, 404);
            }

            // merge into the request
            $request->merge([
                'company' => $company,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Unauthorized');
        }

        return $next($request);
    }
}
