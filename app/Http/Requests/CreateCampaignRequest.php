<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Models\Token;
use App\Models\User;
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
            'flow_action.*.linked_id' => 'numeric',
            'flow_action.*.parent_id' => 'nullable|confirmed',
            'flow_action.*.configurations' => 'nullable',
            'flow_action.*.template' => 'array',
            'flow_action.*.template.template_id' => 'numeric',
            'flow_action.*.template.name' => 'string',
            'flow_action.*.template.variables' => 'nullable|array',
            'flow_action.*.template.meta' => 'nullable'
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
