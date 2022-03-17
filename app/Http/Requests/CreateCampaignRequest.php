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
            'name' => ['required', 'string', 'min:3', 'max:50', Rule::unique('campaigns', 'name')->where(function ($query) {
                return $query->where('company_id', $this->company->id);
            })],
            'flow_action' => 'array',
        ];

        // if flow_action is set then only fields are required (cause flow_action is not required above)
        if (isset(request()->flow_action)) {
            $additionalRules = [
                'flow_action.*.linked_id' => 'required|numeric',
                'flow_action.*.parent_id' => 'nullable|confirmed',
                'flow_action.*.configurations' => 'nullable',
                'flow_action.*.type' => 'required|string',
                'flow_action.*.template' => 'required_if:flow_action.*.type,channel|array', //template and its fields are required only if flow_action.*.type is channel
                'flow_action.*.template.template_id' => 'required_if:flow_action.*.type,channel|numeric',
                'flow_action.*.template.name' => 'required_if:flow_action.*.type,channel|string',
                'flow_action.*.template.variables' => 'nullable|array',
                'flow_action.*.template.meta' => 'required_if:flow_action.*.type,channel|nullable'
            ];
            $validationArray = $validationArray + $additionalRules;
        }

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


        if (isset($this['flow_action']))
            $flow_action = $this->flow_action;

        return array(
            'name' => $this->name,
            'configurations' => empty($this->configurations) ? [] : $this->configurations,
            'meta' => [],
            'flow_action' => empty($flow_action) ? [] : $flow_action,
            'token_id' => $token->id,
            'user_id' => $this->user->id,
            'is_active' => true
        );
    }
}
