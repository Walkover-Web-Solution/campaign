<?php

namespace App\Http\Controllers\API;

use App\Exceptions\ServerErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\DryRunCampaignRequest;
use App\Http\Requests\RunCampaignRequest;
use App\Http\Resources\CustomResource;
use App\Libs\MongoDBLib;
use Carbon\Carbon;
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
        $timeStamps = [];
        $previousTime = 0;
        $campaign = $request->campaign;
        $previousTime = microtime(true) * 1000;

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

        $timeStamps['after_contact_count'] = round(microtime(true) * 1000 - $previousTime, 2);

        // generating random key with time stamp for mongo requestId
        $reqId = preg_replace('/\s+/', '', Carbon::now()->timestamp) . '_' . md5(uniqid(rand(), true));

        // creating campaign log
        $logs = [
            "no_of_contacts" => array_sum($no_of_contacts),
            "mongo_uid" => $reqId,
            'ip' => (!empty(request()->ip)) ? request()->ip : request()->ip(),
            'need_validation' => empty($request->need_validation) ? 0 : $request->need_validation,
            'is_paused' => false
        ];

        $previousTime = microtime(true) * 1000;

        try {

            if ($request->filled('data')) {
                $data = [
                    'requestId' => $reqId,
                    'data' => $request->data
                ];
                // insert into mongo

                $mongoId = $this->mongo->collection('run_campaign_data')->insertOne($data);
            }
        } catch (\Exception $e) {
            logTest("Mongo DB Error", ["exception" => $e->getMessage()], "errorlog");
            throw new ServerErrorException("Internal Server Error! : [Mongo DB Connection Refused]");
        }


        $timeStamps['after_creating_mongo_data'] = round(microtime(true) * 1000 - $previousTime, 2);
        $previousTime = microtime(true) * 1000;

        $campaignLog = $campaign->campaignLogs()->create($logs);

        $timeStamps['after_creating_action_log'] = round(microtime(true) * 1000 - $previousTime, 2);
        $previousTime = microtime(true) * 1000;

        // JobService
        \JOB::processRunCampaign($campaignLog);
        
        $timeStamps['after_creating_job'] = round(microtime(true) * 1000 - $previousTime, 2);
        logTest("Run response time", $timeStamps, "eventlog");

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

        $to = collect($request->data)->where('name', 'to')->first();
        $mobiles = collect($request->data)->where('name', 'mobiles')->first();

        $toArr = [];
        $mobilesArr = [];
        if (!empty($to))
            $toArr = explode(',', $to['value']);
        if (!empty($mobiles))
            $mobilesArr = explode(',', $mobiles['value']);

        // merging to and mobiles into single object
        if (count($mobilesArr) > count($toArr)) {
            $maxCount = count($mobilesArr);
        } else {
            $maxCount = count($toArr);
        }
        for ($i = 0; $i < $maxCount; $i++) {
            array_push(
                $obj->data['sendTo'][0]['to'],
                ['name' => null, 'email' => empty($toArr[$i]) ? null : $toArr[$i], 'mobiles' =>  empty($mobilesArr[$i]) ? null : $mobilesArr[$i]]
            );
        }

        //convert this body to new run request body
        collect($request->data)->each(function ($ob) use ($obj) {
            $key = $ob['name'];
            if ($key == 'cc' || $key == 'bcc') {
                $myArr = explode(',', $ob['value']);
                collect($myArr)->each(function ($item) use ($key, $obj, $ob) {
                    if (!empty($item))
                        array_push($obj->data['sendTo'][0][$key], ['name' => null, 'email' => $item, 'mobiles' => null]);
                });
            } else if (!($key == 'to' || $key == 'mobiles')) {
                //for variables
                $value = $ob['value'];
                $obj->data['sendTo'][0]['variables'][$key] = $value;
            }
        });

        $request->merge(['data' => $obj->data]);
        return new CustomResource($this->commonRun($request)->resource);
    }
}
