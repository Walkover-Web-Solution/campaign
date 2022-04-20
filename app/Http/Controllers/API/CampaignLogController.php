<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomResource;
use App\Models\Campaign;
use App\Models\CampaignLog;
use Illuminate\Http\Request;

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
            return new CustomResource(['message' => 'Invalid Campaign']);
        }

        $campaignLogs = $campaign->campaignLogs();

        $itemsPerPage = $request->input('itemsPerPage', 25);

        $paginator = $campaignLogs
            ->where(function ($query) use ($request) {
                if ($request->has('status')) {
                    $query->where('status', $request->status);
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
            return new CustomResource(['message' => 'Invalid Campaign']);
        }

        if ($campaignLog->campaign_id != $campaign->id) {
            return new CustomResource([
                'data' => [],
                'message' => 'No Action Logs Found'
            ]);
        }

        $actionLogs = $campaignLog->actionLogs();

        //change reason to response
        $paginator = $actionLogs
            ->select('id', 'campaign_id', 'campaign_log_id', 'status', 'report_status', 'response', 'ref_id', 'no_of_records', 'created_at')
            ->where(function ($query) use ($request) {
                if ($request->has('status')) {
                    $query->where('status', $request->status);
                }
                if ($request->has('report_status')) {
                    $query->where('report_status', $request->status);
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
