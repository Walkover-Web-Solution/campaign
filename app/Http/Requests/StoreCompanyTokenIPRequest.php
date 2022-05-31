<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyTokenIPRequest extends FormRequest
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
            'op_ip' => 'required|ip',
            'ip_type_id' => 'required|exists:ip_types,id'
        ];
    }

    public function messages()
    {
        return [
            'op_ip.required' => 'The ip field is required.',
            'op_ip.ip' => 'The ip must be a valid IP address.'
        ];
    }
}
