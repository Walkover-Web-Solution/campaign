<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isEmpty()) {
                if (isset($this->temporary_throttle_limit) ||  isset($this->throttle_limit)) {
                    if (isset($this->throttle_limit)) {
                        $throttleArr = explode(':', $this->throttle_limit);
                        $throttleLimit = $throttleArr[1];
                        $throttleLimitCount = $throttleArr[0];
                    } else {
                        $throttleArr =  explode(':', $this->token->throttle_limit);
                        $throttleLimit = $throttleArr[1];
                        $throttleLimitCount = $throttleArr[0];
                    }

                    if (isset($this->temporary_throttle_limit)) {
                        $temporaryThrottleArr = explode(':', $this->temporary_throttle_limit);
                        $temporaryThrottleLimit = $temporaryThrottleArr[1];
                        $temporaryThrottleLimitCount = $temporaryThrottleArr[0];
                    } else {
                        $temporaryThrottleArr = explode(':', $this->token->temporary_throttle_limit);
                        $temporaryThrottleLimit = $temporaryThrottleArr[1];
                        $temporaryThrottleLimitCount = $temporaryThrottleArr[0];
                    }

                    if ($temporaryThrottleLimit  < $throttleLimit || $temporaryThrottleLimitCount < $throttleLimitCount) {
                        $validator->errors()->add('temporary_throttle_limit', 'temporary_throttle_limit Can not less than throttle_limit');
                    }
                }
            }
        });
}
