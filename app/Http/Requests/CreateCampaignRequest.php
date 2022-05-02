<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCampaignRequest extends FormRequest
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
        $validationArray = [
            'name' => ['required', 'string', 'min:3', 'regex:/^[a-zA-Z0-9_\s]+$/', 'max:50', Rule::unique('campaigns', 'name')->where(function ($query) {
                return $query->where('company_id', $this->company->id);
            })],
            'style' => 'array',
            'module_data' => 'array',
        ];
        return $validationArray;
    }

    public function validated()
    {
        $token = $this->company->tokens()->first();
        if (empty($token)) {
            $token = $this->company->tokens()->create([
                'name' => 'Default Token'
            ]);
        }

        if (isset($this->module_data) && !empty($this->module_data)) {
            if (($this->module_data['op_start']) != null) {
                return false;
            }
        }

        if (isset($this->modules))
            $modules = $this->modules;

        //trim middle spaces in name of campaign
        $this->name = preg_replace('!\s+!', ' ', $this->name);

        return array(
            'name' => $this->name,
            'configurations' => empty($this->configurations) ? [] : $this->configurations,
            'meta' => [],
            'style' => $this->style,
            'module_data' => $this->module_data,
            'modules' => empty($modules) ? [] : $modules,
            'token_id' => $token->id,
            'user_id' => $this->user->id,
            'is_active' => true
        );
    }
}
