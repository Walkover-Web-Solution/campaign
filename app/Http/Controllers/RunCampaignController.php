<?php

namespace App\Http\Controllers;

use App\Http\Requests\RunCampaignRequest;
use App\Http\Resources\CustomResource;
use App\Libs\MongoDBLib;
<<<<<<< HEAD
use App\Models\Campaign;
use App\Services\JobService;
use Illuminate\Http\Request;
=======
>>>>>>> a8bb31924646972d41c79f1dd0be3c9ea082f559

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
            $mongo_id['_id'] = $this->mongo->collection('run_campaign_data')->insertOne($data);
        }

        // insert data in ActionLogs table
        $actionLogData = [
            "no_of_records" => count($request->data),
            "ip" => request()->ip(),
            "status" => "",
            "reason" => "",
            "ref_id" => "",
            "flow_action_id" => $flow_action->id
        ];
        $actionLog = $campaign->actionLogs()->create($actionLogData);

        // setting values to be send with job
        $values['campaign_id'] = $campaign->id;
        $values['mongo_id'] = $mongo_id;
        $values['flow_action_id'] = $flow_action->id;
        $values['action_log_id'] = $actionLog->id;

        // JobService
        \JOB::processRunCampaign($values);

        return new CustomResource(['message' => 'Executed Successfully']);
    }

    public function dryRun()
    {
        //
    }
}
