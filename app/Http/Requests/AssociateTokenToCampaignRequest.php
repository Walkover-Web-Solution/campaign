<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssociateTokenToCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return ($this->token->company_id == $this->company->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'campaigns' => 'nullable|array',
            'campaigns.*.slug' => ['required', Rule::exists('campaigns', 'slug')->where(function ($query) {
                $query->where('company_id', $this->company->id);
            })]
        ];
    }
}
