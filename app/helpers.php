<?php

use App\Http\Resources\CustomResource;
use App\Models\Campaign;
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

    $data = collect($modules)->map(function ($arr) {
        $obj = new \stdClass();
        $obj->ar = [];
        collect($arr)->map(function ($flow, $key) use ($obj) {
            $temp = new \stdClass();
            $temp->name = $flow['name'];
            $temp->style = $flow['style'];
            $temp->module_data = $flow['module_data'];
            array_push($obj->ar, $temp);
        });
        return ($obj->ar);
    });
    return $data;
}

function getCampaign($campid)
{
    $data = new \stdClass();
    $campaign = Campaign::where('id', $campid)->first();
    $data->id = $campaign['id'];
    $data->name = $campaign['name'];
    $data->style = $campaign['style'];
    $data->module_data=$campaign['module_data'];
    $flow = FlowAction::where('campaign_id',$campid)->get();
    collect($flow)->map(function($val){

        
    });
    dd($data);
}
