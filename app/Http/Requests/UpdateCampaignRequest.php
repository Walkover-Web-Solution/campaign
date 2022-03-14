<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Models\User;
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
        //check for authorization key in header
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

        // get user whose company matches with the company passed in key
        $user = User::where('company_id', $company->id)->first();
        if (empty($user)) {
            return false;
        }

        // merge into the request
        $this->merge([
            'company' => $company,
            'user' => $user
        ]);
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
            'flow_action.*.template.template_id' => 'nullable|numeric',
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
