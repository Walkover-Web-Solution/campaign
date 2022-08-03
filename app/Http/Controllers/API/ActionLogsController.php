<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShowActionLogRequest;
use App\Http\Resources\CustomResource;
use App\Models\ActionLog;
use App\Models\CampaignLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActionLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $itemsPerPage = $request->input('itemsPerPagepost', 25);

        $campaignLog = CampaignLog::where('mongo_uid', $request->requestId)->first();

        if (empty($campaignLog))
            return new CustomResource(['message' => 'invalid Request Id']);

        $paginator = $campaignLog->actionLogs()
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ShowActionLogRequest $request, $slug, ActionLog $actionLog)
    {
        $data = $actionLog->ref_id();

        $itemsPerPage = $request->input('itemsPerPage', 25);

        $paginator = $data
            ->where(function ($query) use ($request) {
                if ($request->has('ref_id')) {
                    $query->where('ref_id', $request->ref_id);
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
}
