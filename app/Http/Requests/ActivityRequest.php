<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            throw new \Exception('Campaign is already playing.');
        } else if ($this->pause) {
            throw new \Exception('Campaign is already paused.');
        } else if (!$this->running) {
            throw new \Exception('Campaign Completed. Unable to perform activity!');
        } else if ($this->invalidActivity) {
            throw new NotFoundHttpException('Invalid activity.');
        }
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
