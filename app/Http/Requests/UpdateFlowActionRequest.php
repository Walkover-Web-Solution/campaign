<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use App\Models\ChannelType;
use App\Rules\ValidateModuleDataRule;
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
            'name' => 'regex:/^[a-zA-Z0-9-_]+$/',
            'channel_id' => 'numeric',
            'style' => 'array',
            'module_data' => ['array', new ValidateModuleDataRule($this)],
            'configurations' => 'array',
            'template' => 'array',
            'campaign_variables' => 'array'
        ];

        if (!empty($this->campaign_variables)) {
            $validationArray += [
                'campaign_variables.*.id' => 'exists:variables,id,company_id,' . $this->company->id . '|nullable'
            ];
        }

        return $validationArray;
    }
}
