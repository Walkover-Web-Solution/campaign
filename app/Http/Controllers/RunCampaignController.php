<?php

namespace App\Http\Controllers;

use App\Http\Requests\RunCampaignRequest;
use App\Http\Resources\CustomResource;
// use App\Libs\MongoDBLib;
use App\Models\Campaign;
use Illuminate\Http\Request;

class RunCampaignController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        // $this->mongo = new MongoDBLib();
    }

    public function run(RunCampaignRequest $request)
    {
        $campaign = $request->campaign;
        $flow_action = $campaign->flowActions()->where('parent_id', null);

        if ($request->filled('data')) {
            $result = $this->mongo->collection('run_campaign_data')->insertOne([
                'data' => $request->data
            ]);
            $mongo_id = $result->getInsertedId();
        }
        $values['campaign_id'] = $campaign->id;
        $values['mongo_id'] = $mongo_id;
        $values['flow_action_id'] = $flow_action;

        \JOB::processRunCampaign($values);

        return new CustomResource(['message' => 'Executed Successfully']);
    }

    public function dryRun()
    {
        //
    }
}
