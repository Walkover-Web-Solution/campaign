<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCampaignRequest;
use App\Http\Requests\GetFieldsRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Resources\CustomResource;
use App\Models\Campaign;
use App\Models\ChannelType;
use App\Models\Condition;
use App\Models\FlowAction;
use App\Models\TemplateDetail;
use App\Models\Token;
use Illuminate\Http\Request;

class CampaignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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


        return new CustomResource([
            'data' => $paginator->items(),
            'itemsPerPage' => $itemsPerPage,
            'pageNo' => $paginator->currentPage(),
            'pageNumber' => $paginator->currentPage(),
            'totalEntityCount' => $request->company->campaigns()->count(),
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
    public function show(Request $request, Campaign $campaign)
    {
        if ($campaign->company_id == $request->company->id) {

            $campaign["token"] = $campaign->token()->first();
            $campaign["flow_actions"] = $campaign->flowActions()
                ->with(['template'])->get();

            return new CustomResource($campaign);
        }

        return new CustomResource(['message' => 'Campaign Not Found']);
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
        $obj = new \stdClass();
        $obj->parent_id = null;
        //taking each element of flow_action array and perform action individually
        collect($input['flow_action'])->map(function ($action) use ($obj, $campaign) {
            $action['configurations'] = empty($action['configurations']) ? [] : $action['configurations'];

            //set parent id to previously created flow_action and if first set to null
            $action['parent_id'] = $obj->parent_id;

            //create flow_action with created campaign
            $flow_action = $campaign->flowActions()->create($action);

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
            $obj->parent_id = $flow_action->id;

            //check only if flow action is channel then only create template
            if ($action['type'] == 'channel') {

                //set variables and content to the template array
                $template = $action['template'];
                if (!isset($template['variables'])) {
                    $template['variables'] = [];
                }
                $template['content'] = 'dummy content';
                $template['channel_type_id'] = $flow_action->linked_id;

                //create Template
                $tmp = $flow_action->template()->create($template);
                //check if its details present in TemplateDetail table, if not create one
                $template_detail = TemplateDetail::where('template_id', $tmp->template_id)
                    ->where('channel_type_id', $flow_action->linked_id)
                    ->first();
                if (empty($template_detail)) {
                    $temp_det = $tmp->templateDetails()->create($template);
                }
            }
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Campaign $campaign)
    {
        // check if the campaign is of request's company or not
        if ($campaign->company_id != $request->company->id) {
            return new CustomResource(['message' => "Campaign Not Found"]);
        }

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

    public function getFields(GetFieldsRequest $request)
    {
        // get all channel ids from flow actions attached to given campaign
        $flowAction = FlowAction::where('linked_type', 'App\Models\ChannelType')
            ->where('campaign_id', $request->campaign->id)->get();

        $obj = new \stdClass();
        $obj->mapping = [];
        $obj->variables = [];
        // make validation for every channel id
        collect($flowAction)->map(function ($channel) use ($obj) {

            // inserting template variables
            $template = $channel->template()->first();
            $varaibles = collect($template->variables);
            $varaibles->map(function ($var) use ($obj) {
                array_push($obj->variables, $var);
            });
            $obj->variables = array_unique($obj->variables);

            // inserting channel configurations->mapping
            $channelType = ChannelType::where('id', $channel->linked_id)->first();
            $mapping = collect($channelType->configurations['mapping']);
            $mapping->map(function ($map) use ($obj) {
                if (!in_array($map, $obj->mapping))
                    array_push($obj->mapping, $map);
            });
        });
        return (new CustomResource(['mapping' => $obj->mapping, 'variables' => $obj->variables]));
    }

    public function getSnippets(GetFieldsRequest $request)
    {
        $sampleData = [
            "mobiles" => array('1234567890', '3216549870'),
            "emails" => array(
                "to" => array("name@email.com", "name2@email.com"),
                "cc" => array("name@email.com", "name2@email.com"),
                "bcc" => array("name@email.com", "name2@email.com"),
            )
        ];

        // get all channel ids from flow actions attached to given campaign
        $flowAction = FlowAction::where('linked_type', 'App\Models\ChannelType')
            ->where('campaign_id', $request->campaign->id)->get();

        $obj = new \stdClass();
        $obj->snippets = [];
        $obj->variables = [];
        $obj->inc = 1;

        // make validation for every channel id
        collect($flowAction)->map(function ($channel) use ($obj, $sampleData) {
            $email = 1;
            // according to channel type get data from sampleData
            if ($channel->linked_id == $email) {
                $obj->snippets['emails'] = $sampleData['emails'];
            } else {
                $obj->snippets['mobiles'] = $sampleData['mobiles'];
            }

            // inserting template variables
            $template = $channel->template()->first();
            $varaibles = collect($template->variables);
            $varaibles->map(function ($var) use ($obj) {
                if (!in_array($var, $obj->variables)) {
                    $obj->variables['var' . $obj->inc] = $var;
                    $obj->inc++;
                }
            });
        });

        $obj->snippets = array_merge($obj->snippets, $obj->variables);
        return new CustomResource($obj->snippets);
    }
}
