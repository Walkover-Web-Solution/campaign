<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(!$this->hasHeader('authkey')){
            return false;
        }

        $client=Client::where('authkey',$this->header('authkey'))->first();

        if(empty($client)){
            return false;
        }

        $this->merge([
            'client'=>$client
        ]);

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    public function validated()
    {
        $token=$this->header('authorization');
        try
        {
            $res= JWTDecode($token);
        }
        catch (\Exception $e) {
            return false;
        }
        return [
            'user'=>array(
                'name'=>$res->user->username,
                'email'=>$res->user->email,
                'ref_id'=>$res->user->id,
                'meta'=>$res->user,
            ),
            'company'=>array(
                'ref_id'=>$res->company->id,
                'name'=>$res->company->username,
                'email'=>$res->company->email,
                'meta'=>$res->company
            ),
            'ref_id'=>''
        ];

    }
}
