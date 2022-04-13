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
        return [];
    }

    public function validated()
    {
        $obj = new \stdClass();
        $obj->flag = true;
        $flowActionsCount = $this->campaign->flowActions()->where('is_completed',false)->count();
        if($flowActionsCount > 0)
            return false;
        return $obj->flag;
    }

    private function getChannelCategory(ChannelType $channel)
    {
        # This function will return category of channel type as a string
        switch ($channel->id) {
            case 1:
                return "email";
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
