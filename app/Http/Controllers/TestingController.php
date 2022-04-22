<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomResource;
use App\Models\ActionLog;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Bus\Dispatcher;

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

    public function getActionLogFromCampaignRequest()
    {
    }

    public function encodeData(Request $request)
    {
        if (request()->header('testerKey') != 'testerKey') {
            return new CustomResource(["message" => 'invalid request']);
        }
        try {
            $input = $request->all();
            return new CustomResource(["authorization" => JWTEncode($input)]);
        } catch (\Exception $e) {
            return new CustomResource(["message" => $e->getMessage()]);
        }
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
