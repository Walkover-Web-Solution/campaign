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
        $campaign = Campaign::where('slug', $this->slug)->first();
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
        $channelIds = FlowAction::where('linked_type', 'App\Models\ChannelType')
            ->where('campaign_id', $this->campaign->id)
            ->pluck('linked_id')
            ->toArray();

        $obj = new \stdClass();
        $obj->validationArray = [];

        if (!empty($this->data)) {
            collect($channelIds)->map(function ($channelId) use ($obj) {
                $channelType = ChannelType::where('id', $channelId)->first();

                $mapping = collect($channelType->configurations['mapping']);
                $mapping->each(function ($map) use ($obj) {
                    echo  $map['name'] . ' ';
                    $obj->validationArray['data.*.' . $map['name']] = 'required';
                });
            });
        }
        return ($obj->validationArray);
    }
}
