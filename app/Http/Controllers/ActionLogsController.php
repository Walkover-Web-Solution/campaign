<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomResource;
use App\Models\ActionLog;
use App\Models\Campaign;
use Illuminate\Http\Request;

class ActionLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $obj = new \stdClass();
        $obj->actionLogs = [];

        $campaignIds = Campaign::where('company_id', $request->company->id)->pluck('id');

        $itemsPerPage = $request->input('itemsPerPage', 1);

        $fields = $request->input('fields', 'campaign_id,status,reason');

        $paginator = ActionLog::select(explode(',', $fields))->whereIn('campaign_id', $campaignIds)
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
