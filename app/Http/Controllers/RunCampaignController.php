<?php

namespace App\Http\Controllers;

use App\Http\Requests\RunCampaignRequest;
use App\Http\Resources\CustomResource;
use App\Libs\MongoDBLib;

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
        $flow_action = $campaign->flowActions()->where('parent_id', null)->first();

        if ($request->filled('data')) {
            $data = [
                'data' => $request->data
            ];
            $mongo_id = $this->mongo->collection('run_campaign_data')->insertOne($data);
        }

        // insert data in ActionLogs table
        $actionLogData = [
            "no_of_records" => count($request->data),
            "ip" => request()->ip(),
            "status" => "",
            "reason" => "",
            "ref_id" => "",
            "flow_action_id" => $flow_action->id,
            "mongo_id" => $mongo_id
        ];
        $actionLog = $campaign->actionLogs()->create($actionLogData);

        // setting values to be send with job
        $values['flow_action_id'] = $flow_action->id;

        // JobService
        \JOB::processRunCampaign($values);

        return new CustomResource(['message' => 'Executed Successfully']);
    }

    public function dryRun()
    {
        //
    }
}
