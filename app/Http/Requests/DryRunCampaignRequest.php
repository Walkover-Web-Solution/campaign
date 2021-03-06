<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use App\Models\ChannelType;
use App\Models\FlowAction;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class DryRunCampaignRequest extends FormRequest
{
    protected $case = '';
    protected $limit = true;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // API REMOVED
        return true;

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

        if (!$this->checkDayLimit()) {
            $this->limit = false;
            return false;
        }

        return true;
    }
    protected function failedAuthorization()
    {
        if (!$this->limit) {
            throw new AuthorizationException('Limit for today exceeded by 5.');
        }
        throw new AuthorizationException('This action is unauthorized.');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // API REMOVED
        return [];

        $validationArray = [
            'data' => 'array'
        ];

        if (empty($this->data)) {
            return ['is_dataEmpty' => 'required'];
        }

        if (!empty($this->checkCount())) {
            $this->case = $this->checkCount();
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

        $obj = new \stdClass();
        $obj->reqNames = [];
        //get all unique channelIds of this campaign
        $channelIds = $this->campaign->flowActions()->pluck('channel_id')->unique();
        $channelIds->map(function ($channelId) use ($obj) {
            $reqNames = collect(ChannelType::where('id', $channelId)
                ->pluck('configurations')->first()['mapping'])->where('is_required', true)->pluck('name')->toArray();
            $obj->reqNames  = array_merge($obj->reqNames, $reqNames);
        });

        $nameList = implode(',', $obj->reqNames);


        $validationArray += [
            'data.*.value' => 'required_if:data.*.name,' . $nameList
        ];

        return $validationArray;
    }

    public function messages()
    {
        return [
            'is_dataEmpty.required' => 'Invalid Data. Unable to Execute!',
            'checkCount.required' => 'Data limit should not exceeded more than 5 in ' . $this->case,
            'validCamp.required' => 'No start node for Campaign. Unable to Execute!',
            'is_EmptyTrue.required' => 'No Actions Found. Unable to Execute!',
            'is_completedTrue.required' => 'Incomplete Campaign. Unable to Execute!'
        ];
    }

    public function checkCount()
    {
        $obj = new \stdClass();
        $obj->case = '';
        $keyArr = ['to', 'cc', 'bcc', 'mobiles'];
        collect($this->data)->map(function ($item) use ($obj, $keyArr) {
            // Exclude variables to explode
            if (in_array($item['name'], $keyArr))
                if (count(explode(',', $item['value'])) > 5) {
                    $obj->case .= empty($obj->case) ? $item['name'] : ',' . $item['name'];
                }
        });
        return $obj->case;
    }
    public function checkDayLimit()
    {
        $dayLimit = 5;
        $request_ip = !empty(request()->ip) ? request()->ip : request()->ip();
        $campaignLogs = $this->campaign->campaignLogs()->where('created_at', '>=', Carbon::parse('-24 hours'))->where('ip', $request_ip)->get();
        if (count($campaignLogs) >= $dayLimit)
            return false;
        return true;
    }
}
