<?php

namespace App\Http\Requests;

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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $email = 1;
        $sms = 2;
        $otp = 3;
        $whasapp = 4;
        $voice = 5;

        $validationArray = [
            'request_id' => '',
            "data" => ['array', 'max:2000']
        ];




        // if (!empty($this->data)) {
        //     if ($this->campaign->campaign_type_id != $otp || $this->campaign->campaign_type_id != $flow) {
        //         $fields = collect($this->campaign->campaignType->configurations->fields)->where('is_receiver_variable', true);
        //         $fields->each(function ($field) use (&$validationArray) {
        //             if (isset($field->validations)) {
        //                 $validationArray['data.*.' . $field->name] = $field->validations;
        //             }
        //         });
        //     }
        // }


        return  $validationArray;
    }
}
