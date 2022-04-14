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
        if (empty($this->campaign->flowActions()->get()->toArray())) {
            return false;
        }
        $obj = new \stdClass();
        $obj->flag = true;
        $flowActionsCount = $this->campaign->flowActions()->where('is_completed', false)->count();
        if ($flowActionsCount > 0)
            return false;


        if (!isset($this->data['sendTo']))
            return false;
        $totalCount = collect($this->data['sendTo'])->map(function ($item) {
            $maxRequest = 1000;
            $count = count(collect($item['to'])) + count(collect($item['cc'])) + count(collect($item['bcc']));
            if ($count >= $maxRequest)

                if ($count > $maxRequest) {
                    return 0;
                }
            return 1;
        })->toArray();
        if (in_array(0, $totalCount))
            return false;
        return true;
    }
}
