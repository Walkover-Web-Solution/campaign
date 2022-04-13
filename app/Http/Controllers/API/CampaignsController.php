<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CopyCampaignRequest;
use App\Http\Requests\CreateCampaignRequest;
use App\Http\Requests\CreateCampaignV2Request;
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
                if ($request->has('is_active')) {
                    $query->where('is_active', (bool)$request->is_active);
                }
                if ($request->has('token_id')) {
                    $query->where('token_id', $request->token_id);
                }
                if ($request->has('slug')) {
                    $query->where('slug', $request->slug);
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

        if (!$input) {
            return new CustomResource(["message" => "Invalid Campaign Request"], true);
        }

        // create campaign with the company assoication
        $campaign = $request->company->campaigns()->create($input);

        return new CustomResource($campaign);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Campaign $campaign)
    {
        return new CustomResource(getCampaign($campaign->id));
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

        if (!$input) {
            return new CustomResource(["message" => "Module Data doesn't Belongs to Campaign"], true);
        }

        $campaign->update($input);

        return new CustomResource($campaign);
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
        return new CustomResource(['message' => "Campaign Deleted Successfully."]);
    }

    public function getFields(GetFieldsRequest $request)
    {
        // get all channel ids from flow actions attached to given campaign
        $flowAction = FlowAction::where('campaign_id', $request->campaign->id)->get();

        $obj = new \stdClass();
        $obj->mapping = [];
        $obj->variables = [];
        // make validation for every channel id
        collect($flowAction)->map(function ($channel) use ($obj) {

            // inserting template variables
            $template = $channel->template()->first();
            if (!empty($template)) {
                $variables = collect($template->variables);
                $variables->map(function ($var) use ($obj) {
                    array_push($obj->variables, $var);
                });
                $obj->variables = array_unique($obj->variables);
            }

            // inserting channel configurations->mapping
            $channelType = ChannelType::where('id', $channel->channel_id)->first();
            $mapping = collect($channelType->configurations['mapping']);
            $mapping->map(function ($map) use ($obj) {
                if (!in_array($map, $obj->mapping))
                    array_push($obj->mapping, $map);
            });
        });
        if (empty($obj->variables)) {
            unset($obj->variables);
        }

        return (new CustomResource((array)$obj));
    }

    public function getSnippets(GetFieldsRequest $request)
    {
        $flowActions = $request->campaign->flowActions()->get();
        if (empty($flowActions->toArray())) {
            return new CustomResource(['message' => 'No Actions Found!'], true);
        }

        $obj = new \stdClass();
        $obj->snippets = [];
        $obj->variables = [];
        $obj->ob = [];
        $obj->isEmail = false;

        // endpoint
        $obj->snippets['endpoint'] = env('SNIPPET_HOST_URL') . $request->campaign->slug . '/run';

        //token
        $token = $request->campaign->token()->first();
        $obj->snippets['header'] = array(
            "token" => $token->token
        );

        // get all channel ids from flow actions attached to given campaign
        $flowActions = collect($flowActions);
        $channelIds = $flowActions->pluck('channel_id')->unique();

        // create object of name,email,mobile according to channelIds
        collect($channelIds)->each(function ($channelId) use ($obj) {
            switch ($channelId) {
                case 1: {
                        $obj->ob['name'] = 'name';
                        $obj->ob['email'] = 'name@email.com';
                        $obj->isEmail = true;
                        break;
                    }
                default: {
                        $obj->ob['mobile'] = '911234567890';
                    }
            }
        });

        // get all variables of this campaign
        $variables = [];
        $variableArray = $request->campaign->variables()->pluck('variables')->toArray();
        foreach ($variableArray as $variable) {
            $variables = array_unique(array_merge($variables, $variable));
        }
        collect($variables)->each(function ($variable) use ($obj) {
            $obj->variables[$variable] = $variable;
        });

        // creating snippet requestBody according to object created above
        $obj->snippets['requestBody']['sendTo'][0] = ['to' => [$obj->ob], 'cc' => [$obj->ob], 'bcc' => [$obj->ob]];
        if (!$obj->isEmail) {
            unset($obj->snippets['requestBody']['sendTo'][0]['cc']);
            unset($obj->snippets['requestBody']['sendTo'][0]['bcc']);
        }

        $obj->snippets['requestBody']['sendTo'][0] = array_merge($obj->snippets['requestBody']['sendTo'][0], ['variables' => $obj->variables]);
        return new CustomResource($obj->snippets);
    }


    /**
     * Copy the whole campaign.
     */
    public function copy(CopyCampaignRequest $request)
    {
        // fetch old campaign
        $oldCampaign = $request->campaign->makeVisible(['user_id', 'company_id', 'token_id', 'meta']);
        $campData = $oldCampaign->toArray();
        $campData['name'] = $request->name;
        //create new campaign
        $campaign = $request->company->campaigns()->create($campData);

        $obj = new \stdClass();
        $obj->pair = [];
        $obj->dd = [];

        // create new flow actions for new copied campaign, and make map for previoud and new flowactions ids.
        collect($oldCampaign->flowActions()->get())->map(function ($action) use ($obj, $campaign) {
            $key = $action->id;
            $flow = $campaign->flowActions()->create($action->makeVisible(['channel_id'])->toArray());
            $obj->pair[$key] = $flow->id;
        });

        // change ids in campaign module data according to the map created in above loop
        $module_data = $campaign->module_data;
        $item = $module_data['op_start'];
        if (!empty($obj->pair[$item])) {
            $module_data['op_start'] = $obj->pair[$item];
            $campaign->module_data = $module_data;
            $campaign->save();
        }

        //change ids in flowactions module data according to the map created in above loop
        collect($oldCampaign->flowActions()->get())->map(function ($action) use ($obj) {
            $module_data = $action->module_data;
            collect($module_data)->map(function ($item, $key) use ($obj, $module_data) {
                if (\Str::startsWith($key, 'op')) {
                    if (!empty($obj->pair[$item]))
                        $module_data->$key = $obj->pair[$item];
                }
            });
            $flowAction = FlowAction::where('id', $obj->pair[$action->id])->first();
            $flowAction->module_data = $module_data;
            $flowAction->save();
        });

        // return new CustomResource($campaign);
        return new CustomResource(["message" => "Campaign Copied Successfully."]);
    }
}
