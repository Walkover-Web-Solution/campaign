<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFlowActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $campaign = Campaign::where('slug', $this->slug)->where('company_id', $this->company->id)->first();
        if (empty($campaign))
            return false;
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
            'name' => ['nullable', 'string', 'min:3', 'max:50', Rule::unique('flow_actions', 'name')->where(function ($query) {
                return $query->where('campaign_id', $this->campaign->id);
            })->ignore($this->flowAction->id)],
            'channel_id' => 'nullable|numeric',
            'style' => 'nullable|array',
            'module_data' => 'nullable|array',
            'configurations' => 'nullable|array'
        ];
        return $validationArray;
    }
}
