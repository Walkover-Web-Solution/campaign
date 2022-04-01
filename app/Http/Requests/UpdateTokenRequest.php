<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->company->id == $this->token->company_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['nullable', 'string', 'min:3', 'max:50', Rule::unique('tokens', 'name')->where(function ($query) {
                return $query->where('company_id', $this->company->id);
            })->ignore($this->token->id)],
            'is_active' => 'nullable|boolean',
            "throttle_limit" => 'nullable|regex:/^\d+:\d+$/',
            "temporary_throttle_limit" => 'nullable|regex:/^\d+:\d+$/',
            "temporary_throttle_time" => 'nullable|numeric',
        ];
    }
}
