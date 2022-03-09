<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\CustomResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $input=$request->validated();
		$client=$request->client;
		$company=$client->companies()->where('ref_id',$input['company']['ref_id'])->first();

		/**
         * creatinfg a new company if company is not in the Database
         */
		if(empty($company)){
			$company=$request->client->companies()->create($input['company']);
		}
        /**
         * creating the user for the company
         */

        $user=$company->users()->where('ref_id',$input['user']['ref_id'])->first();
        if(empty($user)){
            $user=$company->users()->create($input['user']);
        }
        /**
         * creating the responce to show company refrence id and authkey
         */
        $response = array("ref_id"=>$company->ref_id,"authkey"=>$company->authkey);
		return new CustomResource($response);
    }
}
