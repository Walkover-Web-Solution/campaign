<?php

namespace App\Http\Requests;

use App\Models\Company;
use App\Models\Token;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCampaignRequest extends FormRequest
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
        $validationArray = [
            'name' => ['required', 'string', 'min:3', 'max:50', Rule::unique('campaigns', 'name')->where(function ($query) {
                return $query->where('company_id', $this->company->id);
            })],
            'flow_action' => 'required|array',
            'flow_action.*.linked_id' => 'required',
            'flow_action.*.parent_id' => 'nullable|confirmed',
            'flow_action.*.template' => 'required|array',
            'flow_action.*.template.template_id' => 'required',
            'flow_action.*.template.name' => 'required',
            'flow_action.*.template.variables' => 'nullable|array',
            'flow_action.*.template.meta' => 'required'
        ];
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


        $flow_action = $this->flow_action;

        return array(
            'name' => $this->name,
            'configurations' => empty($this->configurations) ? [] : $this->configurations,
            'meta' => [],
            'flow_action' => $flow_action,
            'company_token_id' => $token->id,
            'user_id' => $this->user->id,
            'is_active' => true
        );
    }
}
