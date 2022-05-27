<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActionPerformedRequest;
use App\Http\Resources\CustomResource;
use App\Libs\JobLib;
use App\Libs\MongoDBLib;
use App\Models\ActionLog;
use App\Models\ChannelType;
use App\Models\FlowAction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActionPerformedController extends Controller
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
    public function store(ActionPerformedRequest $request)
    {
        if (empty($this->mongo)) {
            $this->mongo = new MongoDBLib;
        }
        $reqId = preg_replace('/\s+/', '', Carbon::now()->timestamp) . '_' . md5(uniqid(rand(), true));
        $data = [
            'requestId' => $reqId,
            'data' => $request->validated()
        ];
        // insert into mongo
        $this->mongo->collection('event_action_data')->insertOne($data);

        // Create job for event_processing

        $input = new \stdClass();
        $input->eventMongoId = $reqId;
        if (empty($this->lib)) {
            $this->lib = new JobLib();
        }
        $this->lib->enqueue('event_processing', $input);

        return new CustomResource(['message' => 'We have successfully recieved your response. Thank You!']);
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
