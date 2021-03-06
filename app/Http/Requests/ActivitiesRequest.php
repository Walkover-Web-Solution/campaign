<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActivitiesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected $invalidActivity = false;
    public function authorize()
    {
        $campaign = $this->company->campaigns()->where('slug', $this->slug)->first();
        if (empty($campaign)) {
            return false;
        }

        if (!$this->has('activity')) {
            $this->invalidActivity = true;
            return false;
        }

        // Merge Campaign to request
        $this->merge([
            'campaign' => $campaign
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
