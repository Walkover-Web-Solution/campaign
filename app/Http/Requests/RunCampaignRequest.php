<?php

namespace App\Http\Requests;

use App\Http\Resources\CustomResource;
use App\Models\Campaign;
use App\Models\ChannelType;
use App\Models\FlowAction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RunCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // get campaign using slug for same company
        $campaign = Campaign::where('slug', $this->slug)
            ->where('company_id', $this->company->id)
            ->first();

        // return false if not found for required clause
        if (empty($campaign)) {
            return false;
        }

        // merge campaign to request
        $this->merge([
            'campaign' => $campaign
        ]);
        // printLog("Found campaign to run id: " . $campaign->id);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (!$this->campaign->is_active) {
            return ['is_activeTrue' => 'required'];
        }

        if (!$this->checkCount()) {
            return ['checkCount' => 'required'];
        }

        $flow_action = FlowAction::where('id', $this->campaign->module_data['op_start'])->where('campaign_id', $this->campaign->id)->first();
        if (empty($flow_action)) {
            return ['validCamp' => 'required'];
        }

        if (empty($this->campaign->flowActions()->get()->toArray())) {
            return ['is_EmptyTrue' => 'required'];
        }

        $flowActionsCount = $this->campaign->flowActions()->where('is_completed', false)->count();
        if ($flowActionsCount > 0) {
            return ['is_completedTrue' => 'required'];
        }
        return [];
    }

    public function messages()
    {
        return [
            'is_activeTrue.required' => 'Campaign is Paused. Unable to Execute!',
            'checkCount.required' => 'Data limit should not exceeded more than 1000.',
            'validCamp.required' => 'Invaid Campaign Action. Unable to Execute!',
            'is_EmptyTrue.required' => 'No Actions Found. Unable to Execute!',
            'is_completedTrue.required' => 'Incomplete Campaign. Unable to Execute!'
        ];
    }

    public function checkCount()
    {
        if (!isset($this->data['sendTo']))
            return false;
        $totalCount = collect($this->data['sendTo'])->map(function ($item) {
            $maxCount = 1000;
            $toCount = isset($item['to'])?count(collect($item['to'])):0;
            $ccCount = isset($item['cc'])?count(collect($item['cc'])):0;;
            $bccCount = isset($item['bcc'])?count(collect($item['bcc'])):0;;
            $count = $toCount+$ccCount+$bccCount;
            if ($count >= $maxCount)
                if ($count > $maxCount) {
                    return false;
                }
            return true;
        })->toArray();
        if (in_array(false, $totalCount))
            return false;
        return true;
    }
}
