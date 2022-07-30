<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowMongodataRequest;
use App\Http\Resources\CustomResource;
use App\Libs\MongoDBLib;
use App\Models\ActionLog;
use App\Models\CampaignLog;
use League\CommonMark\Parser\CursorState;

class ShowMongoDataController extends Controller
{
    protected $mongo;
    public function mongoDataActivity(ShowMongodataRequest $request)
    {
        if (empty($this->mongo)) {
            $this->mongo = new MongoDBLib;
        }
        switch ($request->get) {
            case 'data':
                if ($request->has('campaignLogID')) {
                    $logModel = new CampaignLog();
                    $logId = $request->campaignLogID;
                    $collectionName = "run_campaign_data";
                    $flag = 1;
                } else if ($request->has('actionLogID')) {
                    $logModel = new ActionLog();
                    $logId = $request->actionLogID;
                    $collectionName = "flow_action_data";
                    $flag = 0;
                }
                $log = $logModel->where('id', $logId)->first();
                if (empty($log)) {
                    return new CustomResource(['message' => 'No Logs found']);
                }

                $data = $this->mongo->collection($collectionName)->find([
                    'requestId' => ($flag) ? $log->mongo_uid : $log->mongo_id
                ]);

                if (empty($data)) {
                    return new CustomResource(['message' => 'No data found']);
                }
                return new CustomResource($data);
                break;
            case 'count':

                if ($request->has('campaignLogID')) {
                    $obj=new \stdClass();
                    $obj->actionDataCount = 0;
                    $obj->campaignDataCount = 0;
                    $logs = CampaignLog::where('id', $request->campaignLogID)->with('actionLogs')->first();
                    if (empty($log)) {
                        return new CustomResource(['message' => 'No Logs found']);
                    }
                    collect($logs->actionLogs)->map(function ($actionLog)  use ($obj) {
                        $filter = array("requestId" => $actionLog->mongo_id);
                        $data = $this->mongo->collection('flow_action_data')->find($filter);
                        if (!empty($data)) {
                            $obj->actionDataCount++;
                        }
                    });

                    $data = $this->mongo->collection('run_campaign_data')->find(["requestId" => $logs->mongo_uid]);
                    if (!empty($data)) {
                        $obj->campaignDataCount++;
                    }

                    return new CustomResource(["count" => ["campaignLog Data Count " => $obj->campaignDataCount, "actionLog data count " => $obj->actionDataCount]]);
                }
                else {
                    return new CustomResource(['message' => 'count activity only works with campaign log id']);
                }
                break;
        }
    }
}
