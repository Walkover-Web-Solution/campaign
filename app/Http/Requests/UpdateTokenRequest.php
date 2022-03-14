<?php

namespace App\Http\Requests;

use App\Models\Company;
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
          //check for authrization key in header
          if (!($this->hasHeader('authorization'))) {
            return false;
        }
        // decode key
        try {
            $res = JWTdecode(request()->header('authorization'));
        } catch (\Exception $e) {
            return false;
        }
        // get company whose ref_id matches with the company's id found in key
        $company = Company::where('ref_id', $res->company->id)->first();
        if (empty($company)) {
            return false;
        }
        // merge into the request
        $this->merge([
            'company' => $company,
        ]);
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
            'name' => ['nullable', 'string', 'min:3', 'max:50', 'alpha_dash', Rule::unique('tokens', 'name')->where(function ($query) {
                return $query->where('company_id', $this->company->id);
            })->ignore($this->token->id)],
            'is_active' => 'nullable|boolean',
            "throttle_limit" => 'nullable|regex:/^\d+:\d+$/',
            "temporary_throttle_limit" => 'nullable|regex:/^\d+:\d+$/',
            "temporary_throttle_time" => 'nullable|numeric',
        ];
    }


}
