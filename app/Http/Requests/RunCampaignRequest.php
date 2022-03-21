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

            // validations for is_required taken from conigurations->mapping array
            $mapping = collect($channelType->configurations['mapping']);
            $mapping->each(function ($map) use ($obj) {
                if ($map['is_required'])
                    $obj->validationArray['data.' . $map['name']] = 'required';
            });
        });
        return ($obj->validationArray);
    }
}
