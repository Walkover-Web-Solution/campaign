<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Resources\CustomResource;
use App\Models\Campaign;
use App\Models\ChannelType;
use App\Models\FailedJob;
use App\Models\FlowAction;
use App\Models\Template;
use Illuminate\Http\Request;

class TestingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $campaign = Campaign::where('id', 3)->first();
        dd($campaign->campaignLogs()->where('status', '!=', 'Running')->pluck('id'));
        //  getFlows($request);
        // $action= ActionLog::find(124);
        // dd($action);
        getCampaign(1);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function oneCampaign(Request $request)
    {
        Campaign::truncate();
        FlowAction::truncate();
        Template::truncate();
        $input = $request->data;
        $campaignBody = [
            'name' => $input['name'],
            'module_data' => $input['module_data'],
            'style' => $input['style'],
            'configurations' => [],
            'meta' => [],
            'user_id' => $request->user->id,
            'token_id' => $request->company->tokens()->first()->id,
        ];

        $campaign = $request->company->campaigns()->create($campaignBody);

        $obj = new \stdClass();
        $obj->keyMap = [];

        $modules = $input['modules'];
        collect($modules)->map(function ($channel, $key) use ($obj, $campaign) {
            collect($channel)->map(function ($flowAction, $id) use ($obj, $campaign, $key) {
                $flowAction['channel_id'] = ChannelType::where('name', $key)->pluck('id')->first();
                $flowAction = collect($flowAction)->whereNotNull()->toArray();
                $flowActionModel = $campaign->flowActions()->create($flowAction);
                if (!empty($flowAction['template']))
                    $flowActionModel->template()->create($flowAction['template']);
                $obj->keyMap[$id] = $flowActionModel->id;
            });
        });

        // Update Campaign's op_start
        $campModuleData = $campaign->module_data;
        $campModuleData['op_start'] = $obj->keyMap[$campModuleData['op_start']];
        $campaign->module_data = $campModuleData;
        $campaign->save();

        $flowActions = $campaign->flowActions()->get();
        $flowActions->map(function ($flowAction) use ($obj) {
            $module_data = collect($flowAction->module_data)->map(function ($value, $key) use ($obj) {
                $key_split = explode('_', $key);
                if (count($key_split) == 2 && !empty($value)) {
                    return $obj->keyMap[$value];
                }
                return $value;
            })->toArray();
            if ($flowAction->channel_id == 6) {
                if (!empty($module_data['groupNames'])) {
                    $groupNames = collect($flowAction->module_data->groupNames)->map(function ($value) use ($obj) {
                        $value->flowAction = $obj->keyMap[$value->flowAction];
                        return $value;
                    })->toArray();
                    $module_data['groupNames'] = $groupNames;
                }
            }
            $flowAction->module_data = $module_data;
            $flowAction->save();
        });

        return new CustomResource($campaign);
    }

    /*--- function to create flow actions,template and template details ---
    private function createFlowAction(Campaign $campaign, $input)
    {
        $obj = new \stdClass();
        $obj->parent_id = null;
        //taking each element of flow_action array and perform action individually
        collect($input['flow_action'])->map(function ($action) use ($obj, $campaign) {
            $action['configurations'] = empty($action['configurations']) ? [] : $action['configurations'];

            //set parent id to previously created flow_action and if first set to null
            $action['parent_id'] = $obj->parent_id;

            //create flow_action with created campaign
            $flow_action = $campaign->flowActions()->create($action);

            //check if type is channel or condition and set is_condition to true if condition
            if ($action['type'] == 'channel') {
                $linked = ChannelType::where('id', $action['linked_id'])->first();
            } else if ($action['type'] == 'condition') {
                $flow_action->is_condition = true;
                $linked = Condition::where('id', $action['linked_id'])->first();
            }
            //link flow_action to the respected linked Model
            $linked->flowActions()->save($flow_action);

            $flow_action->save();
            //set parent_id to created flow_action for use of next iteration
            $obj->parent_id = $flow_action->id;

            //check only if flow action is channel then only create template
            if ($action['type'] == 'channel') {

                //set variables and content to the template array
                $template = $action['template'];
                if (!isset($template['variables'])) {
                    $template['variables'] = [];
                }
                $template['content'] = 'dummy content';
                $template['channel_type_id'] = $flow_action->linked_id;

                //create Template
                $tmp = $flow_action->template()->create($template);
                //check if its details present in TemplateDetail table, if not create one
                $template_detail = TemplateDetail::where('template_id', $tmp->template_id)
                    ->where('channel_type_id', $flow_action->linked_id)
                    ->first();
                if (empty($template_detail)) {
                    $temp_det = $tmp->templateDetails()->create($template);
                }
            }
        });
    }
    */
}
