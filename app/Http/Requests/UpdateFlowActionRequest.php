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
            'name' => 'nullable|regex:/^[a-zA-Z0-9-_]+$/',
            'channel_id' => 'nullable|numeric',
            'style' => 'nullable|array',
            'module_data' => 'nullable|array',
            'configurations' => 'nullable|array',
            'template' => 'nullable|array'
        ];
        return $validationArray;
    }
}
