<?php

use App\Http\Resources\CustomResource;
use App\Jobs\RabbitMQJob;
use App\Models\Campaign;
use App\Models\ChannelType;
use App\Models\FlowAction;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Log;

function ISTToGMT($date)
{
    $date = new \DateTime($date);
    return $date->modify('- 5 hours - 30 minutes')->format('Y-m-d H:i:s');
}

function GMTToIST($date)
{
    $date = new \DateTime($date);
    return $date->modify('+ 5 hours + 30 minutes')->format('Y-m-d H:i:s');
}

function filterMobileNumber($mobileNumber)
{
    if (strlen($mobileNumber) == 10) {
        return '91' . $mobileNumber;
    }
    return $mobileNumber;
}

function JWTEncode($value)
{
    $key = config('services.msg91.jwt_secret');
    return JWT::encode($value, $key, 'HS256');
}

function JWTDecode($value)
{
    $key = config('services.msg91.jwt_secret');
    return JWT::decode($value, new Key($key, 'HS256'));
}

function getFlows($modules)
{
    $obj = new \stdClass();
    $obj->ar = [];
    collect($modules)->map(function ($arr, $key) use ($obj) {
        $channelId = ChannelType::where('name', $key)->pluck('id')->first();
        collect($arr)->map(function ($flow, $key) use ($obj, $channelId) {
            $temp = [];
            $temp['channel_id'] = $channelId;
            $temp['name'] = $flow['name'];
            $temp['configurations'] = '[]';
            $temp['style'] = $flow['style'];
            $temp['module_data'] = $flow['module_data'];
            array_push($obj->ar, $temp);
        });
    });
    return $obj->ar;
}

function getCampaign($campid)
{
    $campaign = Campaign::select('id', 'name', 'style', 'module_data', 'token_id')->where('id', $campid)->first();
    $campaign->token = $campaign->token()->get(['name', 'token'])->first();
    $data = new \stdClass();
    $data->modules = [];

    $flow = FlowAction::where('campaign_id', $campid)->get();
    collect($flow)->map(function ($val) use ($data) {
        $channel = ChannelType::where('id', $val['channel_id'])->pluck('name')->first();
        if (empty($data->modules[$channel]))
            $data->modules[$channel] = new \stdClass;
        $temp = new \stdClass();
        $temp->id = $val['id'];
        $temp->name = $val['name'];
        $temp->style = $val['style'];
        $temp->module_data = $val['module_data'];
        $temp->configurations = $val['configurations'];
        $temp->template = $val->template()->first();
        $temp->is_completed = $val['is_completed'];
        $flow_key = $val['id'];
        $data->modules[$channel]->$flow_key = $temp;
    });
    $campaign->modules = $data->modules;

    return ($campaign);
}

function printLog($message, $data = null, $log = 1)
{
    switch ($log) {
        case 1: {
                if (empty($data))
                    Log::debug($message);
                else
                    Log::debug($message, $data);
                break;
            }
        case 2: {
                Log::info($message);
                break;
            }
        case 3: {
                Log::alert($message);
                break;
            }
        case 4: {
                Log::notice($message);
                break;
            }
        case 5: {
                Log::error($message);
                break;
            }
        case 6: {
                Log::warning($message);
                break;
            }
        case 7: {
                Log::critical($message);
                break;
            }
        case 8: {
                Log::emergency($message);
                break;
            }
    }
}


function getQueue($channel_id)
{
    switch ($channel_id) {
        case 1:
            return 'run_email_campaigns';
        case 2:
            return 'run_sms_campaigns';
        case 3:
            return 'run_whastapp_campaigns';
        case 4:
            return 'run_voice_campaigns';
        case 5:
            return 'run_rcs_campaigns';
        case 6:
            return 'condition_queue';
    }
}

function createNewJob($channel_id, $input)
{
    //selecting the queue name as per the flow channel id
    $queue = getQueue($channel_id);

    if (env('APP_ENV') == 'local') {
        RabbitMQJob::dispatch($input)->onQueue($queue)->onConnection('rabbitmqlocal'); //dispatching the job
    } else {
        RabbitMQJob::dispatch($input)->onQueue($queue); //dispatching the job
    }
}
