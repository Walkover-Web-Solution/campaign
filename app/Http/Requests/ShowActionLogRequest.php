<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowActionLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $campaign = $this->company->campaigns()->where('slug', $this->slug)->first();

        if (empty($campaign)) {
            return false;
        }

        if ($this->actionLog->campaign->id != $campaign->id) {
            return false;
        }

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
        return [
            //
        ];
    }
}
