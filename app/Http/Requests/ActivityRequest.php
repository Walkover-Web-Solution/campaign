<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected $pause = false;
    protected $play = false;
    protected $running = true;
    protected $invalidActivity = false;
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
                    if ($campaignLog->is_paused) {
                        $this->pause = true;
                        return false;
                    }
                    if ($campaignLog->status == 'Complete') {
                        $this->running = false;
                        return false;
                    }
                }
                break;
            case 'play': {
                    if ($campaignLog->status == 'Complete') {
                        $this->running = false;
                        return false;
                    }
                    if (!$campaignLog->is_paused) {
                        $this->play = true;
                        return false;
                    }
                }
                break;
            default: {
                    $this->invalidActivity = true;
                    return false;
                }
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
        if ($this->play) {
            throw new AuthorizationException('Campaign is Already Playing.');
        } else if ($this->pause) {
            throw new AuthorizationException('Campaign is Already Paused.');
        } else if (!$this->running) {
            throw new AuthorizationException('Campaign is Already Completed.');
        } else if ($this->invalidActivity) {
            throw new AuthorizationException('Invalid Activity.');
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
        return [
            //
        ];
    }
}
