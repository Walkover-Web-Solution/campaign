<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class PauseCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected $paused = false;
    protected $running = false;
    public function authorize()
    {
        $campaign = $this->company->campaigns()->where('slug', $this->slug)->first();

        // return false if not found for required clause
        if (empty($campaign)) {
            return false;
        }

        if (!$campaign->is_active) {
            $this->paused = true;
            return false;
        }

        $runningCampaignLogCount = $campaign->campaignLogs()->where('status', 'Running')->count();
        if ($runningCampaignLogCount == 0) {
            $this->running = true;
            return false;
        }

        // merge campaign to request
        $this->merge([
            'campaign' => $campaign
        ]);
        return true;
    }
    protected function failedAuthorization()
    {
        if ($this->running) {
            throw new AuthorizationException('Campaign is not Running.');
        } else if ($this->paused) {
            throw new AuthorizationException('Campaign is Already Paused.');
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
