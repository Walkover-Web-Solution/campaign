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
    protected $ref_id;
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
            'campaign_id' => 'string',
            'data' => 'required|array',
            'data.*.event' => 'required'
        ];
        $this->ref_id = $this->campaign_id;

        if (empty($this->campaign_id)) {
            unset($validationArray['campaign_id']);
            $validationArray += [
                'request_id' => 'required|string'
            ];
            $this->ref_id = $this->request_id;
        }
        $action_log = ActionLog::where('ref_id', $this->ref_id)->first();
        if (!empty($action_log)) {
            $this->merge([
                'action_log' => $action_log
            ]);
            if ($action_log->flowAction()->first()->channel_id == 1) {
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
            'request_id.required' => 'request_id or campaign_id is required.'
        ];
    }
}
