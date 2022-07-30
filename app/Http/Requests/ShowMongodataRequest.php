<?php

namespace App\Http\Requests;

use App\Models\ActionLog;
use App\Models\CampaignLog;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShowMongodataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected $invalidActivity = false;
    protected $noLogFound = false;
    public function authorize()
    {
        $campaign = $this->company->campaigns()->where('slug', $this->slug)->first();
        if (empty($campaign)) {
            return false;
        }

        if ((!$this->has('campaignLogID') || !$this->has('actionLogId')) && !$this->has('get')) {
            $this->invalidActivity = true;
            return false;
        }


        // Merge Campaign to request
        $this->merge([
            'campaign' => $campaign,
        ]);

        return true;
    }
    protected function failedAuthorization()
    {
        if ($this->invalidActivity) {
            throw new NotFoundHttpException('Invalid activity.');
        }
        throw new NotFoundHttpException('This action is unauthorized.');
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
