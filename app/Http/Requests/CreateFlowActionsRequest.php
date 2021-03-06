<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use App\Rules\ValidateConfigurationRule;
use App\Rules\ValidateModuleDataRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'name' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9-_]+$/',
            'channel_id' => 'required|exists:channel_types,id',
            'style' => 'array',
            'module_data' => ['array', new ValidateModuleDataRule($this)],
            'module_data.groupNames.*.name' => 'regex:/^[a-z0-9A-Z_-]{1,5}$/',
            'configurations' => ['required', 'array', new ValidateConfigurationRule],
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

    public function messages()
    {
        return [
            'module_data.groupNames.*.name.regex' => "Group name's character limit should not be more than 5 and less than 1 and allowed characters are (A-z 0-9 _ -)"
        ];
    }

    public function validated()
    {
        $token = $this->company->tokens()->first();
        if (empty($token)) {
            $token = $this->company->tokens()->create([
                'name' => 'Default Token'
            ]);
        }

        $obj = collect($this->configurations)->where('name', 'template')->first();
        $template = null;
        if (!empty($obj['template']['template_id'])) {
            $template = $obj['template'];
            $template['variables'] = $obj['variables'];
        }

        if (empty($this->module_data['groupNames']) && $this->channel_id == 6) {
            $module_data = $this->module_data;
            $module_data['groupNames'] = [];
            $this->module_data = $module_data;
        }

        return array(
            'name' => $this->name,
            'channel_id' => $this->channel_id,
            'style' => $this->style,
            'module_data' => $this->module_data,
            'configurations' => empty($this->configurations) ? [] : $this->configurations,
            'template' => empty($template) ? [] : $template,
            'token_id' => $token->id,
            'user_id' => $this->user->id,
            'is_active' => true,
            'is_completed' => false
        );
    }
}
