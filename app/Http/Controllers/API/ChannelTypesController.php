<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomResource;
use App\Models\ChannelType;
use App\Models\ChannelTypeCondition;
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
        $channels = ChannelType::with('conditions:name')->get();
        return new CustomResource($channels);
    }
}
