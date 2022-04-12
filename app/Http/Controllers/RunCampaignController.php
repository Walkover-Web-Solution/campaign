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
        $count = collect($request->data['sendTo'])->map(function ($item) {
            $countEmail = count(collect($item['to'])->pluck('email')) + count(collect($item['cc'])->pluck('email')) + count(collect($item['bcc'])->pluck('email'));
            $countMobile = count(collect($item['to'])->pluck('mobile')) + count(collect($item['cc'])->pluck('mobile')) + count(collect($item['bcc'])->pluck('mobile'));
            return ($countEmail + $countMobile);
        });

        // generating random key with time stamp for mongo requestId
        $reqId = preg_replace('/\s+/', '', now()) . '_' . md5(uniqid(rand(), true));

        // creating campaign log
        $logs = [
            "no_of_records" => array_sum($count->toArray()),
            "mongo_uid"=>$reqId
        ];
        $campaignLog = $campaign->campaignLogs()->create($logs);

        if ($request->filled('data')) {
            $data = [
                'requestId' => $campaignLog->mongo_uid,
                'data' => $request->data
            ];
            // insert into mongo
            $mongoId = $this->mongo->collection('run_campaign_data')->insertOne($data);
        }

        // JobService
        \JOB::processRunCampaign($campaignLog);

        return new CustomResource(['message' => 'Executed Successfully']);
    }

    public function dryRun()
    {
        //
    }
}
