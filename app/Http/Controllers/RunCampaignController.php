<?php

namespace App\Http\Controllers;

use App\Http\Requests\RunCampaignRequest;
use App\Http\Resources\CustomResource;
use App\Libs\MongoDBLib;
use App\Models\FlowAction;

class RunCampaignController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new MongoDBLib();
    }

    public function run(RunCampaignRequest $request)
    {
        $campaign = $request->campaign;
        $flow_action = FlowAction::where('id', $campaign->module_data['op_start'])->where('campaign_id', $campaign->id)->first();
        if (empty($flow_action)) {
            return new CustomResource(['message' => 'Invalid campaign action']);
        }

        // get 'from' data from flowAction configurations if not passed with body
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
            "no_of_records" => $flow_action->channel_id == 1 ? count($request->data['emails']['to']) : ($flow_action->channel_id == 3 ? 1 : count($request->data['mobiles'])),
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
                'requestId' => $actionLog->id,
                'data' => $request->data
            ];
            // insert into mongo
            $mongoId = $this->mongo->collection('run_campaign_data')->insertOne($data);

            // update requestId to created mongoId's alpha numeric key
            $ii = '$oid';
            $mongo_id = (json_decode(json_encode($mongoId))->$ii);
            $this->mongo->collection('run_campaign_data')->update(["requestId" => $actionLog->id], ["requestId" => $mongo_id]);
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
