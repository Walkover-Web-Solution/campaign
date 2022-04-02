<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCampaignV2Request extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:50', Rule::unique('campaigns', 'name')->where(function ($query) {
                return $query->where('company_id', $this->company->id);
            })],
            'modules' => 'array',
        ];

        // if flow_action is set then only fields are required (cause flow_action is not required above)
        // if (isset(request()->modules)) {
        //     $additionalRules = [
        //         'modules.*.name' => ['required', 'string', 'min:3', 'max:50', Rule::unique('flowActions', 'name')->where(function ($query) {
        //             return $query->where('campaign_id', $this->company->id);
        //         })],
        //         'modules.*.style' => 'required|array',
        //         'modules.*.module_data' => 'required|array'
        //     ];
        //     $validationArray = $validationArray + $additionalRules;
        // }
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

        if (isset($this->modules))
            $modules = $this->modules;
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
