<?php

namespace App\Rules;

use App\Models\ChannelType;
use Illuminate\Contracts\Validation\Rule;

class ValidateModuleDataRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $request;
    protected $update = false;
    protected $invalidModule = false;
    protected $groupLimit = false;
    public function __construct($request)
    {
        $this->request = $request;
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

        foreach ($value as $opKey => $opVal) {
            $keySplit = explode('_', $opKey);
            if (count($keySplit) == 2) {
                if (!empty($this->request->module_data[$opKey])) {
                    $flow = $this->request->campaign->flowActions()->where('id', $this->request->module_data[$opKey])->first();
                    $updateFlowActionId = 0;
                    if (!empty($this->request->flowAction)) {
                        $updateFlowActionId = $this->request->flowAction->id;
                    }
                    if (empty($flow)) {
                        $this->invalidModule = true;
                        return false;
                    } else if ($updateFlowActionId == $flow->id) {
                        $this->update = true;
                        return false;
                    }
                }
            }
        }

        if (!empty($value['groupNames'])) {
            // As discussed with Shubhendra Agrawal, Can not create more than 9 Groups + 1(others)
            if (count($value['groupNames']) > 9) {
                $this->groupLimit = true;
                return false;
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
        if ($this->update) {
            return  "Next node can not be same as current.";
        } else if ($this->invalidModule) {
            return "Module data doesn't belongs to Campaign";
        } else if ($this->groupLimit) {
            return "You can not create more than 10 groups.";
        }
        return 'Something went wrong!';
    }
}
