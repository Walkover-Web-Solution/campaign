<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampaignV2Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $campaign = $this->route('campaign');
        if (empty($campaign)) {
            return false;
        }
        $this->merge([
            'campaign' => $campaign
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
        $validationArray =  [
            'name' => ['nullable', 'string', 'min:3', 'max:50', Rule::unique('campaigns', 'name')->where(function ($query) {
                return $query->where('company_id', $this->company->id);
            })->ignore($this->campaign->id)],
            'style' => 'nullable|array',
            'module_data' => 'nullable|array',
            'token_id' => ['nullable', Rule::exists('tokens', 'id')->where(function ($query) {
                $query->where('company_id', $this->company->id);
            })],
        ];
        return $validationArray;
    }

    public function validated()
    {
        $input = $this->validator->validated();

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
