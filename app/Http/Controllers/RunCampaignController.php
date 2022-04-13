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
        $validate = $request->validated();
        if (!$validate) {
            return new CustomResource(['message' => 'Incomplete Campaign'], true);
        }

        $campaign = $request->campaign;
        $flow_action = FlowAction::where('id', $campaign->module_data['op_start'])->where('campaign_id', $campaign->id)->first();
        if (empty($flow_action)) {
            return new CustomResource(['message' => 'Invalid campaign action'], true);
        }

        // creating campaign log
        $campaignLog = $campaign->campaignLogs()->create();

        //count total number of records
        $count = collect($request->data)->map(function ($item) {
            return count($item['sendTo']);
        })->toArray();

        // generating random key with time stamp for mongo requestId
        $reqId = preg_replace('/\s+/', '', now()) . '_' . md5(uniqid(rand(), true));

        // insert data in ActionLogs table
        $actionLogData = [
            "no_of_records" => array_sum($count),
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

        return new CustomResource(['message' => 'Your request has been queued successfully.']);
    }

    public function dryRun()
    {
        //
    }
}
