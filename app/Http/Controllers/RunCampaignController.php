<?php

namespace App\Http\Controllers;

use App\Http\Requests\DryRunCampaignRequest;
use App\Http\Requests\RunCampaignRequest;
use App\Http\Resources\CustomResource;
use App\Libs\MongoDBLib;
use App\Models\ChannelType;
use App\Models\FlowAction;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Http\FormRequest;

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
        return new CustomResource($this->commonRun($request)->resource);
    }


    public function commonRun(FormRequest $request)
    {
        if (!($request->validated()))
            return new CustomResource(["message" => "Data limit should not exceeded more than 1000."]);

        $campaign = $request->campaign;
        $flow_action = FlowAction::where('id', $campaign->module_data['op_start'])->where('campaign_id', $campaign->id)->first();
        if (empty($flow_action)) {
            return new CustomResource(['message' => 'Invalid campaign action'], true);
        }

        $no_of_contacts = collect($request->data['sendTo'])->map(function ($item) {
            $count = 0;
            if (isset($item['to']))
                $count += count($item['to']);
            if (isset($item['cc']))
                $count += count($item['cc']);
            if (isset($item['bcc']))
                $count += count($item['bcc']);
            return ($count);
        })->toArray();

        // generating random key with time stamp for mongo requestId
        $reqId = preg_replace('/\s+/', '', Carbon::now()->timestamp) . '_' . md5(uniqid(rand(), true));

        // creating campaign log
        $logs = [
            "no_of_contacts" => array_sum($no_of_contacts),
            "mongo_uid" => $reqId
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

        return new CustomResource(['message' => 'Your request has been queued successfully.', 'request_id' => $campaignLog->mongo_uid]);
    }

    public function dryRun(DryRunCampaignRequest $request)
    {
        if (empty($request->data)) {
            return new CustomResource(['message' => 'Invalid Data'], true);
        }

        $validate = $request->validated();
        if (!$validate) {
            return new CustomResource(['message' => 'Incomplete Campaign'], true);
        }

        $obj = new \stdClass();
        $obj->data = [];
        $obj->data['sendTo'] = [[]];
        $obj->data['sendTo'][0]['to'] = [];

        //get variables for this campaign
        $variables = [];
        $variableArray = [];
        try {
            $variableArray = $request->campaign->variables()->pluck('variables')->toArray();
            foreach ($variableArray as $variable) {
                $variables = array_unique(array_merge($variables, $variable));
            }
            collect($variables)->each(function ($variable) use ($obj) {
                $obj->variables[$variable] = $variable;
            });
        } catch (Exception $ex) {
        }

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
                        array_push($obj->data['sendTo'][0]['to'], ['name' => '', 'email' => '', 'mobiles' => $item]);
                    else
                        array_push($obj->data['sendTo'][0][$key], ['name' => '', 'email' => $item, 'mobiles' => '']);
            });
        });

        if (!empty($variableArray)) {
            //merge variables array to data object
            $obj->data['sendTo'][0] = array_merge($obj->data['sendTo'][0], ['variables' => $obj->variables]);
        }
        $request->merge(['data' => $obj->data]);
        return new CustomResource($this->commonRun($request)->resource);
    }
}
