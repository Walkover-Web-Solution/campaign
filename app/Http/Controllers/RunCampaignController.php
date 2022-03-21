<?php

namespace App\Http\Controllers;

use App\Http\Requests\RunCampaignRequest;
use App\Http\Resources\CustomResource;
use App\Libs\MongoDBLib;
use App\Models\Campaign;
use App\Services\JobService;
use Illuminate\Http\Request;

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

        // setting values to be send with job
        $values['campaign_id'] = $campaign->id;
        $values['mongo_id'] = $mongo_id;
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
