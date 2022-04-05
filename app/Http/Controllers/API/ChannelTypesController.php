<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomResource;
use App\Models\ChannelCondition;
use App\Models\ChannelType;
use App\Models\Condition;
use Illuminate\Http\Request;

class ChannelTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $channelTypes = ChannelType::all();
        $obj = new \stdClass();
        $obj->channels = [];
        $channelTypes->map(function ($channel) use ($obj) {
            $temp = new \stdClass();
            $temp = $channel;
            $obj->daa = [];

            // getting array for condition ids related to this channel
            $map = ChannelCondition::where('channel_id', $channel->id)->pluck('condition_id')->toArray();

            //getting names collection from above array and making array of object
            $names = Condition::whereIn('id', $map)->pluck('name');
            $names->map(function ($name) use ($obj) {
                array_push($obj->daa, ["name" => $name]);
            });
            $temp->conditions = $obj->daa;
            array_push($obj->channels, $temp);
        });
        return new CustomResource($obj->channels);
    }
}
