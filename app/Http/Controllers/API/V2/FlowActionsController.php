<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFlowActionsRequest;
use App\Http\Resources\CustomResource;
use App\Models\ChannelType;
use Illuminate\Http\Request;

class FlowActionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFlowActionsRequest $request)
    {
        $input = $request->validated();
        $data = collect($input['modules'])->map(function ($module, $key) use ($request) {
            $channel_id = ChannelType::where('name', $key)->pluck('id')->first();
            return collect($module)->map(function ($flow) use ($channel_id, $request) {
                $flowAction = [
                    'name' => $flow['name'],
                    "channel_id" => $channel_id,
                    'configurations' => '[]',
                    "linked_type" => "tt",
                    "parent_id" => -1,
                    'is_condition' => false,
                    'style' => $flow['style'],
                    'module_data' => $flow['module_data']
                ];
                return $request->campaign->flowActions()->create($flowAction);
            });
        });
        return new CustomResource($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
