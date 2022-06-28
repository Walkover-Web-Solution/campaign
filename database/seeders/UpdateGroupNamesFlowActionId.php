<?php

namespace Database\Seeders;

use App\Models\FlowAction;
use Illuminate\Database\Seeder;

class UpdateGroupNamesFlowActionId extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $obj = new \stdClass();
        $conditionFlowActions = FlowAction::where('channel_id', 6)->get();
        collect($conditionFlowActions)->map(function ($condition) use ($obj) {
            if (!empty($condition->module_data->groupNames)) {
                $campaign = $condition->campaign()->first();
                $data = collect($condition->module_data->groupNames)->map(function ($value) use ($campaign, $condition, $obj) {
                    $flowActionId = "";
                    try {
                        $flowActionId = $value->flowAction;
                    } catch (\Exception $e) {
                        printLog("Attempt to read property flowAction on String, on Condition ID :" . $condition->id . " : ", [$value]);
                    }
                    $flowAction = $campaign->flowActions()->where('id', $flowActionId)->first();
                    if (empty($flowAction)) {
                        $obj->id = null;
                        collect($condition->module_data)->map(function ($module_value, $module_key) use ($value, $condition, $obj) {
                            $key_split = explode('_', $module_key);
                            if (count($key_split) == 4) {
                                if ($module_value == $value->id) {
                                    $grp_key_split = explode('_', $module_key);
                                    $op_key = $grp_key_split[0] . '_' . $grp_key_split[1];
                                    $obj->id = $condition->module_data->$op_key;
                                }
                            }
                        });
                        $value->flowAction = $obj->id;
                        return $value;
                    }
                    return $value;
                })->toArray();
            }
            if (!empty($obj->id)) {
                $module_data = $condition->module_data;
                $module_data->groupNames = $data;
                $condition->module_data = $module_data;
                $condition->save();
            }
        });
    }
}
