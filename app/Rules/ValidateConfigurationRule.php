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
