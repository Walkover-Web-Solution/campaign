<?php

namespace App\Http\Requests;

use App\Models\Campaign;
use App\Models\FlowAction;
use App\Rules\AttachmentRule;
use App\Rules\BlobRule;
use Illuminate\Foundation\Http\FormRequest;

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

        if (!isset($this->data['sendTo'])) {
            return ['sendToNotFound' => 'required'];
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

        $validaitonArray = [
            'data.sendTo' => 'array',
            'data.sendTo.*.to' => 'required|array|max:50',
            'data.sendTo.*.cc' => 'array|max:50',
            'data.sendTo.*.bcc' => 'array|max:50',
            'data.attachments' => 'array',
            'data.reply_to' => 'array|max:5'
        ];

        if (!$this->checkCount()) {
            return ['checkCount' => 'required'];
        }

        if (!empty($this->data['reply_to'])) {
            $validaitonArray += [
                'data.reply_to.*.email' => 'required|email',
                'data.reply_to.*.name' => 'string',
            ];
        }

        if (!empty($this->data['attachments'])); {
            $validaitonArray += [
                'data.attachments.*' => ['array', function ($key, $value, $fail) {
                    // required msg for fileType
                    if (empty($value['fileType'])) {
                        return $fail($key . '.fileType is required');
                    }
                    // required msg for fileName
                    if (empty($value['fileName'])) {
                        return $fail($key . '.fileName is required');
                    } else {
                        // To avoid dot(.) in fileName, Remove when hendled from EMAIL side - TASK
                        if (!preg_match("/^[a-zA-Z0-9_\s-]+$/", $value['fileName'])) {
                            return $fail($key . '.fileName format is invalid');
                        }
                    }
                    // required msg for file
                    if (empty($value['file'])) {
                        return $fail($key . '.file is required');
                    }
                    if ($value['fileType'] == 'base64') {
                        $rule = new BlobRule();
                    } else if ($value['fileType'] == 'url') {
                        $rule = new AttachmentRule();
                    } else {
                        return $fail($key . '.fileType selected is invalid');
                    }

                    // Give error message if passes function return false
                    if (!$rule->passes($key . '.file', $value['file'])) {
                        $fail($rule->message());
                    }
                }]
            ];
        }
        return $validaitonArray;
    }

    public function messages()
    {
        return [
            'sendToNotFound.required' => 'data.sendTo key is required',
            'is_activeTrue.required' => 'Campaign is Paused. Unable to Execute!',
            'checkCount.required' => 'Data limit should not exceeded more than 1000.',
            'validCamp.required' => 'No start node for Campaign. Unable to Execute!',
            'is_EmptyTrue.required' => 'No Actions Found. Unable to Execute!',
            'is_completedTrue.required' => 'Incomplete Campaign. Unable to Execute!'
        ];
    }

    public function checkCount()
    {
        $maxCount = 1000;
        $totalCount = collect($this->data['sendTo'])->map(function ($item) {
            $toCount = isset($item['to']) ? count(collect($item['to'])) : 0;
            $ccCount = isset($item['cc']) ? count(collect($item['cc'])) : 0;
            $bccCount = isset($item['bcc']) ? count(collect($item['bcc'])) : 0;;
            $count = $toCount + $ccCount + $bccCount;
            return $count;
        })->toArray();
        if (array_sum($totalCount) > $maxCount) {
            return false;
        }
        return true;
    }
}
