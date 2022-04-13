<?php

namespace App\Http\Controllers;

use App\Http\Requests\RunCampaignRequest;
use App\Http\Resources\CustomResource;
use App\Libs\MongoDBLib;
use App\Models\ChannelType;
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

        // creating campaign log
        $campaignLog = $campaign->campaignLogs()->create();

        // generating random key with time stamp for mongo requestId
        $reqId = preg_replace('/\s+/', '', now()) . '_' . md5(uniqid(rand(), true));

        // insert data in ActionLogs table
        $actionLogData = [
            "no_of_records" => $flow_action->channel_id == 1 ? count($request->data['emails']['to']) : ($flow_action->channel_id == 3 ? 1 : count($request->data['mobiles'])),
            "ip" => request()->ip(),
            "status" => "pending",
            "reason" => "",
            "ref_id" => "",
            "flow_action_id" => $flow_action->id,
            "mongo_id" => $reqId,
            'uid' => $campaignLog->id
        ];
        $actionLog = $campaign->actionLogs()->create($actionLogData);


        if ($request->filled('data')) {
            $data = [
                'requestId' => $actionLog->mongo_id,
                'data' => $request->data
            ];
            // insert into mongo
            $mongoId = $this->mongo->collection('run_campaign_data')->insertOne($data);
        }

        // JobService
        \JOB::processRunCampaign($actionLog);

        return new CustomResource(['message' => 'Executed Successfully']);
    }

    public function dryRun(RunCampaignRequest $request)
    {
        $obj = new \stdClass();
        $obj->data = [];
        $obj->data['sendTo'] = [[]];
        $obj->data['sendTo'][0]['to'] = [];

        //get variables for this campaign
        $variables = [];
        $variableArray = $request->campaign->variables()->pluck('variables')->toArray();
        foreach ($variableArray as $variable) {
            $variables = array_unique(array_merge($variables, $variable));
        }
        collect($variables)->each(function ($variable) use ($obj) {
            $obj->variables[$variable] = $variable;
        });

        //convert this body to new run request body
        collect($request->data)->each(function ($ob) use ($obj) {
            $key = $ob['name'];
            if ($key != 'mobiles')
                if (empty($obj->data['sendTo'][0][$key])) {
                    $obj->data['sendTo'][0][$key] = [];
                }
            $myArr = explode(',', $ob['value']);
            collect($myArr)->each(function ($item) use ($key, $obj) {
                if (!empty($item))
                    if ($key == 'mobiles')
                        array_push($obj->data['sendTo'][0]['to'], ['name' => '', 'email' => '', 'mobile' => $item]);
                    else
                        array_push($obj->data['sendTo'][0][$key], ['name' => '', 'email' => $item, 'mobile' => '']);
            });
        });

        //merge variables array to data object
        $obj->data['sendTo'][0] = array_merge($obj->data['sendTo'][0], ['variables' => $obj->variables]);
        return new CustomResource($obj->data);
        $this->run($request);
    }
}
