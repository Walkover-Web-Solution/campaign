<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AuthByJWTOrAuthkeyMiddleware
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
        /*
         * Checks if the reuqest header has authorization or authkey if both not present throw exception
        */
        if (empty($request->header('authorization')) && empty($request->header('authkey'))) {
            throw new \Exception('Unauthorized', 1);
        }

        if ($request->header('authorization')) {

            /*
             * decode the authorization key
             */
            try {
                $res = JWTDecode($request->header('authorization'));
            } catch (\Exception $e) {
                throw new \Exception('Unauthorized', 1);
            }

            /*
             * checks if the key's company id matches with ref_id from the db tabale
             */
            $company = Company::where('ref_id', $res->company->id)->first();

            if (empty($company)) {
                throw new \Exception("User is not Authorized");
            }
            if (!empty($res->user)) {
                $user = $res->user;
            }
        } else {
            //logic for authkey 
        }

        if (!empty($user)) {

            /**
             * checks if authorization key has user permissions if exists merge to request
             */
            if (isset($res->user->permissions)) {
                $request->merge(['permissions' => json_decode($user->permissions)]);
            }

            /*
             * get user which matches
             */
            $user = User::where('ref_id', $user->id)->first();
            // if (!empty($user)) {
            $request->merge([
                'user' => $user
            ]);
            // } else {
            //     throw new \Exception('User doesn\'t exist');
            // }
        }

        /*
         * get campaign using slug coming from request, if found empty throw exception
         */
        $campaign = $company->campaigns()->where('slug', $request->slug)->first();
        if (empty($campaign)) {
            throw new \Exception('Invalid Campaign', 1);
        }

        $request->merge([
            'company' => $company,
            'campaign' => $campaign
        ]);

        return $next($request);
    }
}
