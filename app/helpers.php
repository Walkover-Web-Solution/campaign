<?php

use App\Http\Resources\CustomResource;
use App\Models\Campaign;
use App\Models\ChannelType;
use App\Models\FlowAction;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Broadcasting\Channel;

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
