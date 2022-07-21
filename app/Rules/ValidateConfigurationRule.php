<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateConfigurationRule implements Rule
{
    protected $errorMsg;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check if delay value is integer
        $delay = collect($value)->where('name', 'delay')->first();
        if (!empty($delay)) {
            if ($delay['value'] != '0') {
                if (!filter_var($delay['value'], FILTER_VALIDATE_INT)) {
                    $this->errorMsg = "Delay must be a whole number";
                    return false;
                }
            }
            $delayTime = getSeconds($delay['unit'], $delay['value']);

            if ($delayTime > 604800) {
                $this->errorMsg = "Delay must be less than 7 days";
                return false;
            }
        }

        $cc = collect($value)->where('name', 'cc')->first();
        if (!empty($cc)) {
            if (!empty($cc['value'])) {
                $ccCount = count(explode(',', $cc['value']));
                if ($ccCount > 50) {
                    $this->errorMsg = 'cc recipients email count must be less than 50';
                    return false;
                }
            }
        }
        $bcc = collect($value)->where('name', 'bcc')->first();
        if (!empty($bcc)) {
            if (!empty($bcc['value'])) {
                $bccCount = count(explode(',', $bcc['value']));
                if ($bccCount > 50) {
                    $this->errorMsg = 'bcc recipients email count must be less than 50';
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMsg;
    }
}
