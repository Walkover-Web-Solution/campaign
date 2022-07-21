<?php

namespace App\Http\Requests;

use App\Exceptions\ForbiddenException;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected $flag = false;
    protected $canPerform = false;
    public function authorize()
    {
        $campaign = $this->company->campaigns()->where('slug', $this->slug)->first();
        if (empty($campaign)) {
            return false;
        }

        $campaignLog = $campaign->campaignLogs()->where('id', $this->campaignLogId)->first();
        if (empty($campaignLog)) {
            return false;
        }

        switch (strtolower($this->activity)) {
            case 'pause': {
                    if ($campaignLog->status == 'Running') {
                        $this->canPerform = true;
                    }
                }
                break;
            case 'play': {

                    if ($campaignLog->status == 'Paused') {
                        $this->canPerform = true;
                    }
                }
                break;
            case 'stop': {
                    if ($campaignLog->status == 'Running' || $campaignLog->status == 'Paused') {
                        $this->canPerform = true;
                    }
                }
                break;
            case 'retry': {
                    if (\Str::startsWith($campaignLog->status, 'Error')) {
                        $this->canPerform = true;
                    }
                }
                break;
            default: {
                    $this->invalidActivity = true;
                    return false;
                }
        }
        if (!$this->canPerform) {
            $this->flag = true;
            return false;
        }

        // Merge Campaign and CampaignLog to request
        $this->merge([
            'campaign' => $campaign,
            'campaignLog' => $campaignLog
        ]);

        return true;
    }
    protected function failedAuthorization()
    {
        if ($this->flag)
            throw new ForbiddenException('Unable to perform activity');
        else if ($this->invalidActivity)
            throw new NotFoundHttpException('Invalid activity.');
        else
            throw new NotFoundHttpException('Not found.');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
