<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use App\Models\ChannelType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFlowActionsRequest extends FormRequest
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
            'name' => 'required|string|regex:/^[a-zA-Z0-9-_]+$/',
            'channel_id' => 'required|numeric',
            'style' => 'required|array',
            'module_data' => 'required|array',
            'configurations' => 'required|array',
            'template' => 'array'
        ];

        if (isset(request()->template)) {
            $additionalRules = [
                'template.template_id' => 'required|regex:/^[a-zA-Z0-9-_]+$/',
                'template.name' => 'nullable|string',
                'template.variables' => 'nullable|array',
                'template.meta' => 'nullable'
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

        if (isset($this->template)) {
            $template = $this->template;
        }

        return array(
            'name' => $this->name,
            'channel_id' => $this->channel_id,
            'style' => $this->style,
            'module_data' => $this->module_data,
            'configurations' => empty($this->configurations) ? [] : $this->configurations,
            'template' => empty($template)? [] : $template,
            'token_id' => $token->id,
            'user_id' => $this->user->id,
            'is_active' => true
        );
    }
}
