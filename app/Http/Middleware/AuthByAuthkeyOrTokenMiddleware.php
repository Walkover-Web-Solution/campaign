<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\Token;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthByAuthkeyOrTokenMiddleware
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
        if (!($request->hasHeader('token') || $request->hasHeader('authorization'))) {
            throw new \Exception("User not Authorized", 1);
        }


        if ($request->header('authorization')) {

            //decode the authorization key
            try {
                $res = JWTDecode(request()->header('authorization'));
            } catch (\Exception $e) {
                throw new \Exception("Unauthorized", 1);
            }

            // get company with re_id and checks if key has company that exists in db table
            $company = Company::where('ref_id', $res->company->id)->first();
            if (empty($company)) {
                throw new \Exception("User not Authorized");
            }

            //get campaign with slug
            $campaign = $company->campaigns()->where('slug', $request->slug)->first();
            if (empty($campaign)) {
                throw new \Exception("Invalid Campaign", 1);
            }

            //checks if slug's campaign's id exists in db table
            if (!$company->campaigns()->where('id', $campaign->id)->exists()) {
                throw new \Exception("User not Authorized");
            }
        } else {


            // token validation
            $token = Token::where('token', $request->header('token'))->first();

            if (empty($token)) {
                throw new \Exception("User not Authorized");
            }

            $ip = $this->ip;
            $whiteListTypeIP = 1;
            if (!$token->ips()->where('ip', $ip)->where('ip_type_id', $whiteListTypeIP)->exists()) {
                $this->validateUserIP($token);
                $this->throttleTokenValidate($token);
            }

            $campaign = $token->company->campaigns()->where('slug', $request->slug)->first();
            if (empty($campaign)) {
                throw new \Exception("Invalid Campaign", 1);
            }
            if ($campaign->company_token_id != $token->id) {
                throw new \Exception("User not Authorized");
            }
        }

        $request->merge([
            'campaign' => $campaign,
            'company' => $campaign->company
        ]);


        return $next($request);
    }


    public function validateUserIP($token)
    {
        $ip = $this->ip;

        $whiteListTypeIP = 1;
        $blackListIPType = 2;
        $temporaryBlockedIPType = 3;
        $tokenIP = $token->ips()->where('ip', $ip)->first();
        if (!empty($tokenIP)) {
            if ($tokenIP->ip_type_id == $blackListIPType) {
                throw new \Exception("Your IP is blocked");
            }

            if ($tokenIP->ip_type_id == $temporaryBlockedIPType) {
                if (time() < strtotime(ISTToGMT($tokenIP->expires_at))) {
                    throw new \Exception($ip . " is blocked temporary");
                } else {
                    $tokenIP->delete();
                }
            }
        }

        $temp = explode(':', $token->temporary_throttle_limit);
        $tempThrottleCount = $temp[0];
        $tempThrottleRange = $temp[1];

        $key = 'temporary_blocked_id:' . $token->id . ':' . $ip;
        if (Cache::has($key)) {
            $temporaryCount = Cache::increment($key);
        } else {
            $temporaryCount = (int)Cache::put($key, 1, $tempThrottleRange);
        }

        if ($temporaryCount > $tempThrottleCount) {
            $ipObj = $token->ips()->where('ip', $ip)->first();
            //  block  ip temporary
            if ($ipObj) { //  case of none tye 
                $ipObj->update([
                    'ip_type_id' => $temporaryBlockedIPType,
                    'expires_at' => date('Y-m-d H:i:s', time() + $token->temporary_throttle_time)
                ]);
            } else {
                $token->ips()->create([
                    'ip' => $ip,
                    'ip_type_id' => $temporaryBlockedIPType,
                    'expires_at' => date('Y-m-d H:i:s', time() + $token->temporary_throttle_time)
                ]);
            }

            Cache::forget($key); // reset the  counter
            throw new \Exception($ip . "  blocked  temporarily", 1);
        }
    }


    public function throttleTokenValidate($token)
    {
        $throttleLimit = $token->throttle_limit;
        $key = 'campaign_run_' . $this->ip;
        $throttleArr = explode(':', $throttleLimit);
        $limit = $throttleArr[0];
        $rate = $throttleArr[1];

        if ($this->limiter->tooManyAttempts($key, $limit)) {
            throw new ThrottleRequestsException("Too many attempts");
        }

        $this->limiter->hit($key, $rate);
    }
}
