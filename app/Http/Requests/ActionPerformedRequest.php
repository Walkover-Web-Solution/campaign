<?php

namespace App\Http\Requests;

use App\Models\ActionLog;
use Illuminate\Foundation\Http\FormRequest;

class ActionPerformedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'campaign_id' => 'required|string',
            'data' => 'required|array',
            'data.*.event' => 'required'
        ];
        $campaign_id = $this->campaign_id;
        $campaign_id_split = explode('_', $campaign_id);
        $actionLogId = $campaign_id_split[0];

        $action_log = ActionLog::where('id', (int)$actionLogId)->first();
        if (!empty($action_log)) {
            $channel_id = $action_log->flowAction()->first()->channel_id;
            if ($channel_id == 1) {
                $validationArray += ['data.*.email' => 'required|email'];
            } else {
                $validationArray += ['data.*.mobile' => 'required|mobile'];
            }
        }

        return $validationArray;
    }

    public function messages()
    {
        return [
            'campaign_id.required' => 'campaign_id field is required.'
        ];
    }
}
