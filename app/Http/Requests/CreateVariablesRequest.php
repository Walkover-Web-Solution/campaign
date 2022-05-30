<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateVariablesRequest extends FormRequest
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
            'name' => [
                'required', 'string', 'min:3', 'regex:/^[a-zA-Z0-9_]+$/', 'max:20', Rule::unique('variables', 'name')->where(function ($query) {
                    return $query->where('company_id', $this->company->id);
                })
            ]
        ];
    }
}
