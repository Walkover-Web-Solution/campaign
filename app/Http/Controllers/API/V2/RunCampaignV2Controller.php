<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\RunCampaignV2Request;
use App\Http\Resources\CustomResource;
use App\Libs\MongoDBLib;
use App\Models\FlowAction;
use Illuminate\Http\Request;

class RunCampaignV2Controller extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new MongoDBLib();
    }

    public function run(RunCampaignV2Request $request)
    {
        $campaign = $request->campaign;
        $flow_action = FlowAction::where('id', $campaign->module_data['op_start'])->where('campaign_id', $campaign->id)->first();

        // get 'from' data from flowAction configurations if not passed with body
        // dd($request->data['emails']['from']);
        if (empty($request->data['emails']['from'])) {
            $from['from'] = $flow_action->configurations->from;
            $request->data = array_merge($request->data, $from);
        }
        // get 'cc' data from flowAction configurations if cc field is not there in request body, but pass empty if key-with-no-data is there
        if (!isset($request->data['emails']['cc'])) {
            $cc['cc'] = $flow_action->configurations->cc;
            $request->data = array_merge($request->data, $cc);
        }
        // get 'bcc' data from flowAction configurations if cc field is not there in request body, but pass empty if key-with-no-data is there
        if (!isset($request->data['emails']['bcc'])) {
            $bcc['bcc'] = $flow_action->configurations->bcc;
            $request->data = array_merge($request->data, $bcc);
        }

        // insert data in ActionLogs table
        $actionLogData = [
            "no_of_records" => $flow_action->linked_id == 1 ? count($request->data['emails']['to']) : ($flow_action->linked_id == 3 ? 1 : count($request->data['mobiles'])),
            "ip" => request()->ip(),
            "status" => "",
            "reason" => "",
            "ref_id" => "",
            "flow_action_id" => $flow_action->id,
            "mongo_id" => ""
        ];
        $actionLog = $campaign->actionLogs()->create($actionLogData);


        if ($request->filled('data')) {
            $data = [
                'action_log_id' => $actionLog->id,
                'data' => $request->data
            ];
            // insert into mongo
            $mongo_id = $this->mongo->collection('run_campaign_data')->insertOne($data);
        }


        $actionLog->mongo_id = $mongo_id;
        $actionLog->save();

        // JobService
        \JOB::processRunCampaign($actionLog);

        return new CustomResource(['message' => 'Executed Successfully']);
    }

    public function dryRun()
    {
        //
    }
}
