<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomResource;
use App\Models\ChannelType;
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
        $channels = ChannelType::with('conditions:name')->get();
        $obj = new \stdClass();
        $obj->channels = [];
        $condition = 5;
        $channels->map(function ($channel) use ($obj, $condition) {
            $channel->is_hidden = false;
            if ($channel->id == $condition)
                $channel->is_hidden = true;
            array_push($obj->channels, $channel);
        });
        return new CustomResource($obj->channels);
    }
}
