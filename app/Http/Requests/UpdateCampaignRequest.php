<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->route('campaign')->company_id != $this->company->id) {
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
            'name' => ['nullable', 'string', 'min:3', 'max:50', Rule::unique('campaigns', 'name')->where(function ($query) {
                return $query->where('company_id', $this->company->id);
            })->ignore($this->campaign->id)],
            'token_id' => ['nullable', Rule::exists('tokens', 'id')->where(function ($query) {
                $query->where('company_id', $this->company->id);
            })],
            'flow_action' => 'nullable|array',
            'is_active' => 'nullable',
            'flow_action.*.template' => 'nullable|array',
            'flow_action.*.template.template_id' => 'nullable|regex:/^[a-zA-Z0-9-_]+$/',
            'flow_action.*.template.name' => 'nullable|string',
            'flow_action.*.template.content' => 'nullable',
            'flow_action.*.template.variables' => 'nullable|array',
            'flow_action.*.template.meta' => 'nullable'
        ];
    }

    public function validated()
    {
        $input = $this->validator->validated();

        if (isset($this->flow_action)) {
            $input['flow_action'] = $this->flow_action;
        } else {
            $input['flow_action'] = [];
        }

        if (isset($this->configurations)) {
            $input['configurations'] = $this->configurations;
        } else {
            $input['configurations'] = [];
        }

        $token = $this->company->tokens()->first();
        if (empty($token)) {
            $token = $this->company->tokens()->create([
                'name' => 'Default Token'
            ]);
        }
        $input['token_id'] = $token->id;
        $input['user_id'] = $this->user->id;

        if (isset($this->is_active)) {
            $input['is_active'] = $this->is_active;
        }

        if (empty($this->token_id)) {
            $input['token_id'] = $this->campaign->token_id;
        }
        return $input;
    }
}
