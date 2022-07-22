<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidRequestException;
use App\Models\Company;
use App\Models\Token;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            throw new InvalidRequestException('Invalid Request');
        }

        printLog("check for authorization");
        if ($request->header('authorization')) {
            try {
                $res = JWTDecode(request()->header('authorization'));
                printLog("res=", (array)$res);
            } catch (\Exception $e) {
                printLog("expeption", (array)$e->getTrace());
                throw new InvalidRequestException('Unauthorized');
            }

            // in case of JWT we set need_validation to true by default
            $need_validation = true;

            $company = Company::where('ref_id', $res->company->id)->first();

            printLog("company :", (array)$company);

            if (empty($company)) {
                throw new InvalidRequestException('Invalid Request');
            }
            printLog("check for company");
            $campaign = $company->campaigns()->where('slug', $request->slug)->first();
            if (empty($campaign)) {
                throw new NotFoundHttpException('Invalid Campaign');
            }
            printLog("company found");
            if (!$company->campaigns()->where('id', $campaign->id)->exists()) {
                throw new NotFoundHttpException('User not Authorized');
            }
        } else {
            // in case of Token we set need_validation to false by default
            $need_validation = false;
            printLog("check by token");
            // token validation
            $token = Token::where('token', $request->header('token'))->first();
            printLog("token : ", (array)$token);
            if (empty($token)) {
                throw new NotFoundHttpException('User not Authorized');
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
                throw new NotFoundHttpException('Invalid Campaign');
            }
            printLog("found campaign");
            if ($campaign->token_id != $token->id) {
                throw new NotFoundHttpException('User not Authorized');
            }
        }
        printLog("merge campaign");
        $request->merge([
            'campaign' => $campaign,
            'company' => $campaign->company,
            'need_validation' => $need_validation
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
                throw new NotFoundHttpException('Your IP is blocked');
            }

            printLog("found token ip");
            if ($tokenIP->ip_type_id == $temporaryBlockedIPType) {
                if (time() < strtotime(ISTToGMT($tokenIP->expires_at))) {
                    throw new NotFoundHttpException($ip . " is blocked temporary");
                } else {
                    $tokenIP->delete();
                    $token->ips()->create([
                        'ip' => $ip,
                        'ip_type_id' => $none
                    ]);
                }
            }
        } else {
            $token->ips()->create([
                'ip' => $ip,
                'ip_type_id' => $none
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
