<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomResource;
use App\Models\ActionLog;
use App\Models\Campaign;
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

        $paginator = DB::table('action_logs')
            ->select('campaigns.name as campaign', 'campaigns.slug', 'status', 'reason', 'ip', 'ref_id', 'action_logs.created_at', 'action_logs.updated_at')
            ->join('campaigns', 'campaigns.id', '=', 'action_logs.campaign_id')
            ->where(['campaigns.company_id' => $request->company->id])
            ->where(function ($query) use ($request) {
                if ($request->has('slug')) {
                    $query->where('campaigns.slug', $request->slug);
                }
                if ($request->has('name')) {
                    $query->where('campaigns.name', $request->name);
                }
                if ($request->has('fromDate')) {
                    $query->whereDate('action_logs.created_at', '>=', $request->fromDate);
                }
                if ($request->has('toDate')) {
                    $query->whereDate('action_logs.created_at', '<', $request->toDate);
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
}
