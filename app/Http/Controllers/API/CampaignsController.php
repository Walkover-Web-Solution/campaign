<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCampaignRequest;
use App\Http\Requests\DeleteCampaignRequest;
use App\Http\Requests\IndexCampaignRequest;
use App\Http\Requests\RestoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Resources\CustomResource;
use App\Models\Campaign;
use App\Models\ChannelType;
use App\Models\Condition;
use App\Models\TemplateDetail;
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

        $fields = $request->input('fields', 'id,name,is_active,slug');


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
                    $query->where('token_id', $request->token_id);
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
        //validating request
        $input = $request->validated();

        // create campaign with the company assoication
        $campaign = $request->company->campaigns()->create($input);

        $parent_id = null;

        if (isset($input['flow_action'])) {
            $this->createFlowAction($campaign, $input);
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
    public function update(UpdateCampaignRequest $request, Campaign $campaign)
    {
        $input = $request->validated();
        $campaign->update($input);


        if (isset($input['flow_action'])) {

            // delete all templates attachted to every flowactions of this campaign
            $campaign->flowActions()->get()->map(function ($item) {
                $item->template()->delete();
            });

            // delete all flow actions related to this campaign
            $campaign->flowActions()->delete();

            $this->createFlowAction($campaign, $input);
        }
        return new CustomResource($campaign);
    }

    private function createFlowAction(Campaign $campaign, $input)
    {
        $parent_id = null;
        //taking each element of flow_action array and perform action individually
        foreach ($input['flow_action'] as $action) {
            $action['configurations'] = empty($action['configurations']) ? [] : $action['configurations'];

            //create flow_action with created campaign
            $flow_action = $campaign->flowActions()->create($action);

            //set parent id to previously created flow_action and if first set to null
            $flow_action->parent_id = $parent_id;

            //check if type is channel or condition and set is_condition to true if condition
            if ($action['type'] == 'channel') {
                $linked = ChannelType::where('id', $action['linked_id'])->first();
            } else if ($action['type'] == 'condition') {
                $flow_action->is_condition = true;
                $linked = Condition::where('id', $action['linked_id'])->first();
            }
            //link flow_action to the respected linked Model
            $linked->flowActions()->save($flow_action);

            $flow_action->save();
            //set parent_id to created flow_action for use of next iteration
            $parent_id = $flow_action->id;


            //set variables and content to the template array
            $template = $action['template'];
            if (!isset($template['variables'])) {
                $template['variables'] = [];
            }
            $template['content'] = 'dummy content';

            //create Template
            $tmp = $flow_action->template()->create($template);
            //check if its details present in TemplateDetail table, if not create one
            $template_detail = TemplateDetail::where('template_id', $tmp->template_id)->first();
            if (empty($template_detail)) {
                $tmp->templateDetails()->create($template);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteCampaignRequest $request, Campaign $campaign)
    {
        // delete all templates related to this campaign via flowActions
        $campaign->flowActions()->get()->map(function ($item) {
            $item->template()->delete();
        });

        // deletes all flow actions realated to this campaign
        $campaign->flowActions()->delete();

        // delete this campaign
        $campaign->delete();
        return new CustomResource(['message' => "Delete Campaign successfully"]);
    }
}
