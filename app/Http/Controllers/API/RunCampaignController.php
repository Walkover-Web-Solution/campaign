<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
        return new CustomResource($this->commonRun($request)->resource);
    }


    public function commonRun(FormRequest $request)
    {
        $campaign = $request->campaign;

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
            "mongo_uid" => $reqId,
            'ip' => request()->ip(),
            'need_validation' => empty($request->need_validation) ? 0 : $request->need_validation
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
        $obj = new \stdClass();
        $obj->data = [];
        $obj->data['sendTo'] = [[]];
        $obj->data['sendTo'][0]['to'] = [];
        $obj->data['sendTo'][0]['cc'] = [];
        $obj->data['sendTo'][0]['bcc'] = [];
        $obj->data['sendTo'][0]['variables'] = [];


        //convert this body to new run request body
        collect($request->data)->each(function ($ob) use ($obj) {
            $key = $ob['name'];
            $myArr = explode(',', $ob['value']);
            collect($myArr)->each(function ($item) use ($key, $obj, $ob) {
                if (!empty($item))
                    if ($key == 'mobiles')
                        array_push($obj->data['sendTo'][0]['to'], ['name' => null, 'email' => null, 'mobiles' => $item]);
                    else if ($key == 'to' || $key == 'cc' || $key == 'bcc')
                        array_push($obj->data['sendTo'][0][$key], ['name' => null, 'email' => $item, 'mobiles' => null]);
                    else {
                        $value = $ob['value'];
                        $obj->data['sendTo'][0]['variables'][$key] = $value;
                    }
            });
        });

        $request->merge(['data' => $obj->data]);
        return new CustomResource($this->commonRun($request)->resource);
    }
}
