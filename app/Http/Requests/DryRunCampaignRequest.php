<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use App\Models\ChannelType;
use Illuminate\Foundation\Http\FormRequest;

class DryRunCampaignRequest extends FormRequest
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
            ->where('company_id', $this->company->id)->where('is_active', '1')
            ->first();

        // return false if not found for required clause
        if (empty($campaign)) {
            return false;
        }

        // merge campaign to request
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
        $validationArray = [
            'data' => 'array'
        ];

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

    public function validated()
    {
        if (empty($this->campaign->flowActions()->get()->toArray())) {
            return false;
        }
        $obj = new \stdClass();
        $obj->flag = true;
        $flowActionsCount = $this->campaign->flowActions()->where('is_completed', false)->count();
        if ($flowActionsCount > 0)
            return false;
        return $obj->flag;
    }
}
