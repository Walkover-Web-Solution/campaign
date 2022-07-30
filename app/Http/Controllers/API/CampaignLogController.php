<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivitiesRequest;
use App\Http\Requests\ActivityRequest;
use App\Http\Resources\CustomResource;
use App\Models\Campaign;
use App\Models\CampaignLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CampaignLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $slug)
    {
        $campaign = Campaign::where('slug', $slug)->where('company_id', $request->company->id)->first();
        if (empty($campaign)) {
            throw new NotFoundHttpException('Invalid Campaign');
        }

        $campaignLogs = $campaign->campaignLogs();

        $itemsPerPage = $request->input('itemsPerPage', 25);

        $paginator = $campaignLogs
            ->where(function ($query) use ($request) {
                if ($request->has('status')) {
                    $statusSplit = explode(',', $request->status);
                    $query->whereIn('status', $statusSplit);
                }
                if ($request->has('requestId')) {
                    $query->where('mongo_uid', $request->requestId);
                }
            })
            ->orderBy('id', 'desc')
            ->paginate($itemsPerPage, ['*'], 'pageNo');

        return new CustomResource([
            'data' => $paginator->items(),
            'itemsPerPage' => $itemsPerPage,
            'pageNo' => $paginator->currentPage(),
            'pageNumber' => $paginator->currentPage(),
            'totalEntityCount' => $paginator->total(),
            'totalPageCount' => ceil($paginator->total() / $paginator->perPage())
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        throw new NotFoundHttpException();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        throw new NotFoundHttpException();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug, CampaignLog $campaignLog)
    {
        $itemsPerPage = $request->input('itemsPerPage', 25);

        $campaign = Campaign::where('slug', $slug)->where('company_id', $request->company->id)->first();
        if (empty($campaign)) {
            throw new NotFoundHttpException('Invalid Campaign');
        }

        if ($campaignLog->campaign_id != $campaign->id) {
            throw new NotFoundHttpException('No Action Logs Found');
        }

        $actionLogs = $campaignLog->actionLogs()->join('flow_actions', 'flow_actions.id', '=', 'action_logs.flow_action_id');

        $paginator = $actionLogs
            ->select('action_logs.id', 'flow_actions.name', 'action_logs.campaign_id', 'campaign_log_id', 'status', 'report_status', 'response', 'ref_id', 'no_of_records', 'action_logs.created_at', 'action_logs.event_received')
            ->where(function ($query) use ($request) {
                if ($request->has('status')) {
                    $statusSplit = explode(',', $request->status);
                    $query->whereIn('status', $statusSplit);
                }
                if ($request->has('report_status')) {
                    $query->where('report_status', $request->status);
                }
            })
            ->orderBy('action_logs.id', 'desc')
            ->paginate($itemsPerPage, ['*'], 'pageNo');


        return new CustomResource([
            'data' => $paginator->items(),
            'itemsPerPage' => $itemsPerPage,
            'pageNo' => $paginator->currentPage(),
            'pageNumber' => $paginator->currentPage(),
            'totalEntityCount' => $paginator->total(),
            'totalPageCount' => ceil($paginator->total() / $paginator->perPage())
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        throw new NotFoundHttpException();
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
        throw new NotFoundHttpException();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        throw new NotFoundHttpException();
    }

    /**
     * Play and Pause all Campaign Logs belong to the Campaign.
     */
    public function activities(ActivitiesRequest $request)
    {
        $obj = new \stdClass();
        $obj->count = false;
        switch (strtolower($request->activity)) {
            case 'pause': {
                    $campaignLogs = $request->campaign->campaignLogs()->where('status', 'Running')->where('is_paused', false)->get();
                    $campaignLogs->map(function ($campaignLog) use ($obj) {
                        $obj->count = true;
                        $campaignLog->is_paused = true;
                        $campaignLog->status = "Paused";
                        $campaignLog->save();
                    });
                    if ($obj->count)
                        return new CustomResource(['message' => 'Activity performed successfully.']);
                    return new CustomResource(['message' => 'No log found to perform activity']);
                }
            case 'play': {
                    $campaignLogs = $request->campaign->campaignLogs()->where('is_paused', true)->get();
                    $campaignLogs->map(function ($campaignLog) use ($obj) {
                        $obj->count = true;
                        $campaignLog->is_paused = false;
                        $campaignLog->status = "Running";
                        $campaignLog->save();
                        $this->playCampaign($campaignLog);
                    });
                    if ($obj->count)
                        return new CustomResource(['message' => 'Activity performed successfully.']);
                    return new CustomResource(['message' => 'No log found to perform activity']);
                }

            case 'stop': {
                    $campaignLogs = $request->campaign->campaignLogs()->where('status', 'Running')->where('is_paused', false)->get();
                    $campaignLogs->map(function ($campaignLog) use ($obj) {
                        $obj->count = true;
                        $campaignLog->status = 'Stopped';
                        $campaignLog->save();
                    });
                    if ($obj->count)
                        return new CustomResource(['message' => 'Activity performed successfully.']);
                    return new CustomResource(['message' => 'No log found to perform activity']);
                }
            default:
                throw new NotFoundHttpException('Invalid activity.');
        }
    }

    /**
     * Play and Pause a single Campaign Log belongs to the Campaign.
     */
    public function activity(ActivityRequest $request)
    {
        switch (strtolower($request->activity)) {
            case 'pause': {
                    $request->campaignLog->status = "Paused";
                    $request->campaignLog->is_paused = true;
                    $request->campaignLog->save();
                    return new CustomResource(['message' => 'Activity performed successfully.']);
                }
            case 'play': {
                    $request->campaignLog->status = "Running";
                    $request->campaignLog->is_paused = false;
                    $request->campaignLog->save();
                    $this->playCampaign($request->campaignLog);
                    return new CustomResource(['message' => 'Activity performed successfully.']);
                }
            case 'stop': {
                    $request->campaignLog->status = 'Stopped';
                    $request->campaignLog->save();
                    return new CustomResource(['message' => 'Activity performed successfully.']);
                }
            case 'retry': {
                    $request->campaignLog->status = 'Running';
                    $request->campaignLog->canRetry = false;
                    $request->campaignLog->save();
                    \JOB::processRunCampaign($request->campaignLog);
                    return new CustomResource(['message' => 'Activity performed successfully.']);
                }
            default:
                throw new NotFoundHttpException('Invalid activity.');
        }
    }

    /**
     * Play Campaign from where it was stopped
     */
    public function playCampaign($campaignLog)
    {
        $actionLogs = $campaignLog->actionLogs()->where('status', 'pending')->get();
        collect($actionLogs)->map(function ($actionLog) {
            $input = new \stdClass();
            $input->action_log_id =  $actionLog->id;
            $flowAction = $actionLog->flowAction()->first();
            $delay = collect($flowAction->configurations)->firstWhere('name', 'delay');
            $delayValue = getSeconds($delay->unit, $delay->value);
            createNewJob($flowAction->channel_id, $input, $delayValue);
        });
    }
}
