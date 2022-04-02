<?php

namespace App\Http\Requests;

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
        // get all channel ids from flow actions attached to given campaign
        $channelIds = FlowAction::where('linked_type', 'App\Models\ChannelType')
            ->where('campaign_id', $this->campaign->id)
            ->pluck('linked_id')
            ->toArray();

        $obj = new \stdClass();
        $obj->validationArray = [];

        // make validation for every channel id
        collect($channelIds)->map(function ($channelId) use ($obj) {
            $channelType = ChannelType::where('id', $channelId)->first();
            $channelCategory = $this->getChannelCategory($channelType);
            // validations for is_required taken from conigurations->mapping array
            $mapping = collect($channelType->configurations['mapping']);
            $mapping->each(function ($map) use ($obj, $channelCategory) {

                if ($channelCategory == 'mobiles') {
                    if ($map['is_required'])
                        $obj->validationArray['data.' . $channelCategory . '.*.' . $map['name']] = 'required';
                } else {
                    if ($map['is_required']) {
                        $obj->validationArray['data.' . $channelCategory . '.' . $map['name']] = $map['is_array'] ? 'required | array' : 'required';
                    } else {
                        $obj->validationArray['data.' . $channelCategory . '.' . $map['name']] = $map['is_array'] ? 'array' : '';
                    }
                }
                // $obj->validationArray['data.' . $map['name']] = ($map['is_required'] ? 'required | ' : '') . ($map['is_array'] ? 'array' : ''); //($map['is_array'] ? 'required|array' : 'required') : '';
            });
        });
        
        return ($obj->validationArray);
    }

    private function getChannelCategory(ChannelType $channel)
    {
        # This function will return category of channel type as a string
        switch ($channel->id) {
            case 1:
                return "emails";
                break;
            case 2:
                return "mobiles";
                break;
            case 3:
                return "mobile";
                break;
            case 4:
                return "mobiles";
                break;
            case 5:
                return "mobiles";
                break;
        }
    }
}
