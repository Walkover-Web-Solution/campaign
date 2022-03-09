<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCampaignRequest;
use App\Http\Requests\IndexCampaignRequest;
use App\Http\Resources\CustomResource;
use App\Models\Campaign;
use App\Models\ChannelType;
use App\Models\Condition;
use App\Models\Template;
use Illuminate\Http\Request;

class CampaignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexCampaignRequest $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 25);

        $fields = $request->input('fields', 'id,name,is_active,configurations');


        $paginator = Campaign::select(explode(',', $fields))->where('company_id', $request->company->id)
            ->where(function ($query) use ($request) {
                if ($request->has('name')) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                }
                // if ($request->flow_action->has('linked_id')) {
                //     $query->where('linked_id', $request->flow_action->linked_id);
                // }

                if ($request->has('is_active')) {
                    $query->where('is_active', (bool)$request->is_active);
                }
                if ($request->has('token_id')) {
                    $query->where('company_token_id', $request->token_id);
                }
            })
            ->orderBy('id', 'desc')
            ->paginate($itemsPerPage, ['*'], 'pageNo');

        return response([
            'status' => 'success',
            'hasError' => false,
            'data' => array(
                'data' => $paginator->items(),
                'itemsPerPage' => $itemsPerPage,
                'pageNo' => $paginator->currentPage(),
                'pageNumber' => $paginator->currentPage(),
                'totalEntityCount' => $request->company->campaigns()->count(),
                'totalPageCount' => ceil($paginator->total() / $paginator->perPage())
            )
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
    public function store(CreateCampaignRequest $request)
    {
        $input = $request->validated();

        $campaign = $request->company->campaigns()->create($input);

        $parent_id = null;
        foreach ($input['flow_action'] as $action) {
            $flow_action = $campaign->flowActions()->create($action);
            $flow_action->parent_id = $parent_id;
            if ($action['type'] == 'channel') {
                $linked = ChannelType::where('id', $action['linked_id'])->first();
            }
            if ($action['type'] == 'condition') {
                $linked = Condition::where('id', $action['linked_id'])->first();
            }
            $linked->flowActions()->save($flow_action);

            if ($action['type'] == 'condition') {
                $flow_action->is_condition = true;
            }
            $flow_action->save();
            $parent_id = $flow_action->id;

            // $template = Template::where('flow_action_id', $flow_action->id);
            // if (empty($template)) {
            //     $flow_action->template()->create($action['template']);
            // }
        }


        return new CustomResource($campaign);
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
