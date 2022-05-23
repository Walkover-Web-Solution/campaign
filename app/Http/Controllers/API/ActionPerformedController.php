<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActionPerformedRequest;
use App\Http\Resources\CustomResource;
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
        $action_log = $request->action_log;
        $channel_id = $request->channel_id;

        if (empty($this->mongo)) {
            $this->mongo = new MongoDBLib;
        }
        // Fetch data from mongo
        $mongo_data = $this->mongo->collection('flow_action_data')->find([
            'requestId' => $action_log->mongo_id
        ]);
        $mongo_data = json_decode(json_encode($mongo_data))[0];

        // Filter data according to events
        $filteredData = $this->getEventFilteredData($request->data, $channel_id, $mongo_data);

        collect($action_log->flowAction->module_data)->map(function ($flowActionId, $key) use ($filteredData, $action_log) {
            if (\Str::startsWith($key, 'op_')) {
                $keySplit = explode('_', $key);
                if (count($keySplit) == 2) {
                    if (!empty($flowActionId)) {
                        $flowAction = FlowAction::where('id', $flowActionId)->first();
                        // get delay count
                        $delay = (int)collect($flowAction->configurations)->firstWhere('name', 'delay')->value;
                        // create next action_log
                        $next_action_log = $this->createNextActionLog($flowAction, ucfirst($keySplit[1]), $action_log, $filteredData[$keySplit[1]]);
                        if (!empty($next_action_log)) {
                            $input = new \stdClass();
                            $input->action_log_id =  $next_action_log->id;
                            // create job for next_action_log
                            createNewJob($flowAction->channel_id, $input, $delay);
                        }
                    }
                }
            }
        });

        return new CustomResource(['message' => 'We have successfully recieved your response. Thank You!']);
    }

    /**
     * Filters data from webhook, according to event
     */
    public function getEventFilteredData($requestData, $channel_id, $mongo_data)
    {
        $obj = new \stdClass();
        $obj->data = [];
        $obj->data['success'] = [];
        $obj->data['failed'] = [];
        $obj->data['read'] = [];
        $obj->data['unread'] = [];

        collect($requestData)->map(function ($item) use ($mongo_data, $obj, $channel_id) {
            collect($mongo_data->data)->map(function ($contacts, $field) use ($obj, $item, $channel_id) {
                if ($channel_id == 1) {
                    $contact = (array)collect($contacts)->firstWhere('email', $item['email']);
                } else {
                    $contact = (array)collect($contacts)->firstWhere('mobiles', $item['mobile']);
                }
                // Add all events in condition which are recieved from microservices
                if (!empty($contact)) {
                    if ($item['event'] == 'Success') {
                        if (empty($obj->data['success'][$field]))
                            $obj->data['success'][$field] = [];
                        array_push($obj->data['success'][$field], $contact);
                    } else if ($item['event'] == 'Failed') {
                        if (empty($obj->data['failed'][$field]))
                            $obj->data['failed'][$field] = [];
                        array_push($obj->data['failed'][$field], $contact);
                    } else if ($item['event'] == 'Read') {
                        if (empty($obj->data['read'][$field]))
                            $obj->data['read'][$field] = [];
                        array_push($obj->data['read'][$field], $contact);
                    } else if ($item['event'] == 'Unread') {
                        if (empty($obj->data['unread'][$field]))
                            $obj->data['unread'][$field] = [];
                        array_push($obj->data['unread'][$field], $contact);
                    }
                }
            });
        });
        return $obj->data;
    }

    /**
     * Create next Action Log from webhook
     */
    public function createNextActionLog($flowAction, $event, $action_log, $filteredData)
    {
        // generating random key with time stamp for mongo requestId
        $reqId = preg_replace('/\s+/', '', Carbon::now()->timestamp) . '_' . md5(uniqid(rand(), true));

        // Store data in mongo and get requestId
        $data = [
            'requestId' => $reqId,
            'data' => $filteredData
        ];
        $this->mongo->collection('flow_action_data')->insertOne($data);

        //generating an array of all the events belong to flowaction's channel_type
        $events = ChannelType::where('id', $flowAction->channel_id)->first()->events()->pluck('name')->toArray();

        if (in_array($event, $events)) {

            if (!empty($flowAction)) {
                printLog("Found next flow action.");
                $actionLogData = [
                    "campaign_id" => $action_log->campaign_id,
                    "no_of_records" => $action_log->no_of_records,
                    "response" => "",
                    "status" => "pending",
                    "report_status" => "pending",
                    "ref_id" => "",
                    "flow_action_id" => $flowAction->id,
                    "mongo_id" => $reqId,
                    'campaign_log_id' => $action_log->campaign_log_id
                ];
                printLog('Creating new action as per channel id ');
                $actionLog = ActionLog::create($actionLogData);
                return $actionLog;
            } else {
                printLog("Didn't found next flow action.");
            }
        }
        return;
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
