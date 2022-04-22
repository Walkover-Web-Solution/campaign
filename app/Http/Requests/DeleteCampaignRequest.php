<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class DeleteCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return false if not found for required clause
        if ($this->campaign->company_id != $this->company->id) {
            return false;
        }

        return true;
    }
    protected function failedAuthorization()
    {
        throw new AuthorizationException('Campaign Not Found!');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (!empty($this->campaign->campaignLogs()->where('status', '!=', 'Running')->pluck('id')->toArray())) {
            return [
                'is_running' => 'required'
            ];
        }
        return [];
    }

    public function messages()
    {
        return [
            'is_running.required' => 'Campaign is in Running Condition. Can not Delete!'
        ];
    }
}
