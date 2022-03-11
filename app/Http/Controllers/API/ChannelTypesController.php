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
        $channelType=ChannelType::all();
		return new CustomResource($channelType);
    }
}
