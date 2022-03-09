<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class IndexCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return
     *  bool
     */
    public function authorize()
    {
        // check for authkey also?

        //check for authrization key in header
        if (!($this->hasHeader('authorization'))) {
            return false;
        }
        // decode key
        try {
            $res = JWTdecode(request()->header('authorization'));
        } catch (\Exception $e) {
            return false;
        }

        // get company whose ref_id matches with the company's id found in key
        $company = Company::where('ref_id', $res->company->id)->first();
        if (empty($company)) {
            return false;
        }

        // merge into the request
        $this->merge([
            'company' => $company,
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
