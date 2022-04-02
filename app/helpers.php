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
    $data = new \stdClass();
    $campaign = Campaign::where('id', $campid)->first();
    $data->id = $campaign['id'];
    $data->name = $campaign['name'];
    $data->style = $campaign['style'];
    $data->module_data = $campaign['module_data'];
    $data->modules = [];
    $flow = FlowAction::where('campaign_id', $campid)->get();

    $modules = collect($flow)->map(function ($val, $key) use ($data) {
        $channel = ChannelType::where('id', $val['channel_id'])->pluck('name')->first();
        if (empty($data->modules[$channel]))
            $data->modules[$channel] = [];
        $temp = new \stdClass();
        $temp->id = $val['id'];
        $temp->name = $val['name'];
        $temp->style = $val['style'];
        $temp->module_data = $val['module_data'];
        array_push($data->modules[$channel], $temp);
        // return $temp;
    });
    // $data->modules = $modules;
    dd($data);
}
