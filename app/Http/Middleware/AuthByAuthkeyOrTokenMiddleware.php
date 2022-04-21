<?php

namespace App\Http\Middleware;

use App\Http\Resources\CustomResource;
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
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        printLog("start of handle");
        if (!($request->hasHeader('token') || $request->hasHeader('authorization'))) {
            //     if (!($request->hasHeader('token'))){
            throw new \Exception("Invalid Request");
        }

        printLog("check for authorization");
        if ($request->header('authorization')) {
            try {
                $res = JWTDecode(request()->header('authorization'));
                printLog("res=", (array)$res);
            } catch (\Exception $e) {
                printLog("expeption", (array)$e->getTrace());
                throw new \Exception("Unauthorized");
            }

            $company = Company::where('ref_id', $res->company->id)->first();

            printLog("company :", (array)$company);

            if (empty($company)) {
                $response = new CustomResource(["message" => "invalid request"], true);
                return response()->json($response, 404);
            }
            printLog("check for company");
            $campaign = $company->campaigns()->where('slug', $request->slug)->first();
            if (empty($campaign)) {
                $response = new CustomResource(["message" => "Invalid Campaign"], true);
                return response()->json($response, 404);
            }
            printLog("company found");
            if (!$company->campaigns()->where('id', $campaign->id)->exists()) {
                $response = new CustomResource(["message" => "User not Authorized"], true);
                return response()->json($response, 404);
            }
        } else {

            printLog("check by token");
            // token validation
            $token = Token::where('token', $request->header('token'))->first();
            printLog("token : ", (array)$token);
            if (empty($token)) {
                $response = new CustomResource(["message" => "User not Authorized"], true);
                return response()->json($response, 404);
            }
            printLog("check for ip");
            $ip = $this->ip;
            $whiteListTypeIP = 1;
            if (!$token->ips()->where('ip', $ip)->where('ip_type_id', $whiteListTypeIP)->exists()) {
                printlog("validate user ip");
                $blockedResponse = $this->validateUserIP($token);
                if (empty($blockedResponse)) {
                    printLog("validate throttle token");
                    $this->throttleTokenValidate($token);
                } else {
                    return $blockedResponse;
                }
            }
            printLog("check for token");
            $campaign = $token->company->campaigns()->where('slug', $request->slug)->first();
            if (empty($campaign)) {
                $response = new CustomResource(["message" => "Invalid Campaign"], true);
                return response()->json($response, 404);
            }
            printLog("found campaign");
            if ($campaign->token_id != $token->id) {
                $response = new CustomResource(["message" => "User not Authorized"], true);
                return response()->json($response, 404);
            }
        }
        printLog("merge campaign");
        $request->merge([
            'campaign' => $campaign,
            'company' => $campaign->company
        ]);

        return $next($request);
    }


    public function validateUserIP($token)
    {

        //$ip=request()->header('cf-connecting-ip');
        $ip = $this->ip;

        $whiteListTypeIP = 1;
        $blackListIPType = 2;
        $temporaryBlockedIPType = 3;
        $none = 4;

        printLog("ip=", (array)$ip);
        $tokenIP = $token->ips()->where('ip', $ip)->first();


        printLog("check for token ip", (array)$tokenIP);
        if (!empty($tokenIP)) {

            if ($tokenIP->ip_type_id == $blackListIPType) {
                $response = new CustomResource(["message" => "Your IP is blocked"], true);
                return response()->json($response, 404);
            }

            printLog("found token ip");
            if ($tokenIP->ip_type_id == $temporaryBlockedIPType) {
                if (time() < strtotime(ISTToGMT($tokenIP->expires_at))) {
                    $response = new CustomResource(["message" => $ip . " is blocked temporary"], true);
                    return response()->json($response, 404);
                } else {
                    $tokenIP->delete();
                    $token->ips()->create([
                        'ip' => $ip,
                        'ip_type_id' => $none,
                        'expires_at' => ""
                    ]);
                }
            }
        } else {
            $token->ips()->create([
                'ip' => $ip,
                'ip_type_id' => $none,
                'expires_at' => ""
            ]);
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
        printLog("throttle ", (array)$temporaryCount);
        if ($temporaryCount > $tempThrottleCount) {
            $ipObj = $token->ips()->where('ip', $ip)->first();

            printlog("ip obj - ", (array)$ipObj);
            //  block  ip temporary
            if ($ipObj) { //  case of none tye
                $ipObj->update([
                    'ip_type_id' => $temporaryBlockedIPType,
                    'expires_at' => date('Y-m-d H:i:s', time() + $token->temporary_throttle_time)
                ]);
            } else {
                printLog("create data for company token ip");
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
