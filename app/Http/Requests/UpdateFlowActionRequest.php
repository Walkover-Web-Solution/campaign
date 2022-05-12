<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use App\Models\ChannelType;
use App\Rules\ValidateModuleData;
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
            'module_data' => ['array', new ValidateModuleData($this)],
            'configurations' => 'array',
            'template' => 'array'
        ];
        return $validationArray;
    }
}
