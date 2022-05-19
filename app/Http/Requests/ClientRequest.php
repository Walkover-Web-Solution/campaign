<?php

namespace App\Http\Requests;

use App\Exceptions\InvalidRequestException;
use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //check for secret key in header
        if (!($this->hasHeader('secretKey'))) {
            throw new InvalidRequestException("Invalid Request");
        }

        //check if key matches to the key (below only)->just a random generated key, fixed
        if (request()->header('secretKey') != "CzzaUozgPuIRJ61TmsNWdYOqxZenlwTW") {
            return false;
        }

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
}
