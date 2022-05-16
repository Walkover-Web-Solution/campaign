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
    protected $nameLimit = false;
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
            if (\Str::startsWith($opKey, 'op') && !(\Str::endsWith($opKey, 'grp_id'))) {
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
            foreach ($value['groupNames'] as $grpId => $grpName) {
                $nameLength = \Str::length($grpName);
                if (!($nameLength >= 1 && $nameLength <= 5)) {
                    $this->nameLimit = true;
                    return false;
                }
            }

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
        } else if ($this->nameLimit) {
            return "Group name's character limit should not be more than 5 and less than 1.";
        }
        return 'Something went wrong!';
    }
}
