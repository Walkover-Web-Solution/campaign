<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidRequestException;
use App\Models\Company;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class AuthByJWTMiddleware
{
    public $limiter;
    protected $ip;

    public function __construct(RateLimiter $limiter)
    {
        if (!empty(request()->ip)) {
            request()->merge([
                'op_ip' => request()->ip
            ]);
        }
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
            throw new InvalidRequestException('Invalid Request');
        }

        try {
            $value = $request->header('authorization');
            $res = JWTDecode($value);
        } catch (\Exception $e) {
            printLog("expeption", (array)$e->getTrace());
            throw new InvalidRequestException('Unauthorized');
        }

        // checks if the request is to register.
        if (\Request::getRequestUri() == "/api/register") {
            return $next($request);
        }

        // get company whose ref_id matches with the company's id found in key
        $company = Company::where('ref_id', $res->company->id)->first();
        if (empty($company)) {
            throw new InvalidRequestException('Invalid Request');
        }

        // get user whose company matches with the company passed in key
        $user = User::where('company_id', $company->id)->first();
        if (empty($user)) {
            throw new InvalidRequestException('Invalid Request');
        }

        // merge into the request
        $request->merge([
            'company' => $company,
            'user' => $user
        ]);

        return $next($request);
    }
}
