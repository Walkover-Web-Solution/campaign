<?php

namespace App\Http\Controllers\API;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CopyCampaignRequest;
use App\Http\Requests\CreateCampaignRequest;
use App\Http\Requests\DeleteCampaignRequest;
use App\Http\Requests\FetchActionlogIDRequest;
use App\Http\Requests\GetFieldsRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Resources\CustomResource;
use App\Models\Campaign;
use App\Models\ChannelType;
use App\Models\FlowAction;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $fields = $request->input('fields', 'campaigns.id,campaigns.name,is_active,slug');

        $campaigns = Campaign::select(explode(',', $fields))
            ->leftJoin('flow_actions', 'flow_actions.campaign_id', '=', 'campaigns.id')
            ->where('company_id', $request->company->id)
            ->where(function ($query) use ($request) {
                if ($request->has('name')) {
                    $request->name = str_replace('_', '\_', $request->name);
                    $query->where('campaigns.name', 'like', '%' . $request->name . '%');
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
            })->selectRaw('group_concat(DISTINCT(flow_actions.channel_id)) as channels')
            ->groupBy('campaigns.id');

        $paginator = $campaigns
            ->orderBy('campaigns.id', 'desc')
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
    public function store(CreateCampaignRequest $request)
    {
        //validating request
        $input = $request->validated();

        if (!$input) {
            return new CustomResource(["message" => "Invalid Request Payload"], true);
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
        throw new NotFoundHttpException();
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
            return new CustomResource(["message" => "Module Data doesn't Belong to Unit."], true);
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
        return new CustomResource(['message' => "Deleted Successfully."]);
    }

    public function getFields(GetFieldsRequest $request)
    {
        // get all channel ids from flow actions attached to given campaign
        $channelIds = $request->campaign->flowActions()->pluck('channel_id');

        $obj = new \stdClass();
        $obj->mapping = [];

        //get variables
        $obj->variables = [];
        $variableArray = $request->campaign->variables()->pluck('variables')->toArray();
        foreach ($variableArray as $variable) {
            $obj->variables = array_unique(array_merge($obj->variables, $variable));
        }
        // if (!empty($variables))
        //     collect($variables)->each(function ($variable) use ($obj) {
        //         $obj->variables[$variable] = $variable;
        //     });

        // make validation for every channel id
        collect($channelIds)->each(function ($channelId) use ($obj) {
            // inserting channel configurations->mapping
            $mapping = ChannelType::where('id', $channelId)->pluck('configurations')->first()['mapping'];
            $obj->mapping = array_merge($obj->mapping, $mapping);
        });

        $obj->mapping = collect($obj->mapping)->unique()->toArray();

        return (new CustomResource((array)$obj));
    }

    public function getSnippets(GetFieldsRequest $request)
    {
        $flowActions = $request->campaign->flowActions()->get();
        if (empty($flowActions->toArray())) {
            throw new NotFoundHttpException('No Actions Found!');
        }

        if (!$request->has('version')) {
            $request->version = 'v1';
        }

        $obj = new \stdClass();
        $obj->snippets = [];
        $obj->variables = [];
        $obj->contactVariables = [];
        $obj->ob = [];
        $obj->isEmail = false;

        $obj->jsonTemplate = false;
        switch ($request->version) {
            case 'v1': {
                    $obj->jsonTemplate = false;
                }
                break;
            case 'v2': {
                    $obj->jsonTemplate = true;
                }
                break;
            default: {
                    throw new InvalidRequestException('Invalid version!');
                }
        }

        // endpoint
        $obj->snippets['endpoint'] = env('SNIPPET_HOST_URL') . $request->campaign->slug . '/run';

        //token + authkey in header
        $token = $request->campaign->token()->first();
        $obj->snippets['header'] = array(
            "authkey" => "{your_MSG91_authkey}",
            "token" => $token->token
        );

        // Documentation
        $obj->snippets['documentation'] = [
            "Note: In case of SMS, RCS, Whatsapp priority will be given to variables associated with contacts and in case of Email priority will be given to variables associated with sendto object.",
            "Note: In case of Whatsapp make sure to pass the variables as required by Whatsapp service."
        ];

        // get all channel ids from flow actions attached to given campaign
        $flowActions = collect($flowActions);
        $channelIds = $flowActions->pluck('channel_id')->unique();


        // get all variables of this campaign
        $variables = [];
        $variableArray = $request->campaign->variables()->pluck('variables')->toArray();

        foreach ($variableArray as $variable) {
            $variables = array_unique(array_merge($variables, $variable));
        }

        // As per new request body, supports variables in contact in case mobiles, remove email variables from contactVariables
        $emailVariables = $request->campaign->variables()->where('flow_actions.channel_id', 1)->pluck('variables')->first();
        if (empty($emailVariables)) {
            $emailVariables = [];
        }
        $contactVariables = array_diff($variables, $emailVariables);

        // make variables in key-value format
        collect($variables)->each(function ($variable) use ($obj) {
            $obj->variables[$variable] = $obj->jsonTemplate ? ['type' => '{your_type}', 'value' => "{your_value}"] : "{your_value}";
        });

        collect($contactVariables)->each(function ($variable) use ($obj) {
            $obj->contactVariables[$variable] = $obj->jsonTemplate ? ['type' => '{your_type}', 'value' => "{your_value}"] : "{your_value}";
        });

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
                        $obj->ob['mobiles'] = '911234567890';
                        $obj->ob['variables'] = $obj->contactVariables;
                    }
            }
        });

        // creating snippet requestBody according to object created above
        $obj->snippets['requestBody']['data']['sendTo'][0] = ['to' => [$obj->ob], 'cc' => [$obj->ob], 'bcc' => [$obj->ob]];
        if (!$obj->isEmail) {
            unset($obj->snippets['requestBody']['data']['sendTo'][0]['cc']);
            unset($obj->snippets['requestBody']['data']['sendTo'][0]['bcc']);
        }

        if ($obj->isEmail) {
            // Attachments
            $obj->snippets['requestBody']['data']['attachments'] = [
                [
                    "fileType" => "url or base64",
                    "fileName" => "{your_fileName}",
                    "file" => "{your_file}"
                ]
            ];
            // reply_to
            $obj->snippets['requestBody']['data']['reply_to'] = [
                [
                    "name" => "{your_name}",
                    "email" => "{your_email}"
                ]
            ];
        }

        $obj->snippets['requestBody']['data']['sendTo'][0] = array_merge($obj->snippets['requestBody']['data']['sendTo'][0], ['variables' => $obj->variables]);
        return new CustomResource($obj->snippets);
    }


    /**
     * Copy the whole campaign. OPTIMIZE | TASK
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

        // create new flow actions for new copied campaign, and make map for previous and new flowactions ids.
        collect($oldCampaign->flowActions()->get())->map(function ($action) use ($obj, $campaign) {
            $key = $action->id;
            $flowAction = $campaign->flowActions()->create($action->makeVisible(['channel_id'])->toArray());
            $obj->pair[$key] = $flowAction->id;

            // create template for new flowactions
            if (!empty($flowAction->configurations)) {
                $templateObj = collect($flowAction->configurations)->where('name', 'template')->first();
                $template = null;
                if (!empty($templateObj->template->template_id)) {
                    if ($flowAction->channel_id == 1)
                        $templateObj->template->template_id = $templateObj->template->slug;
                    $template = (array)$templateObj->template;
                    $template['variables'] = empty($templateObj->variables) ? [] : $templateObj->variables;
                    if (empty($flowAction->template)) {
                        $flowAction->template()->create($template);
                    } else {
                        $flowAction->template->template_id = $template['template_id'];
                        $flowAction->template->variables = $template['variables'];
                        $flowAction->template->save();
                    }
                } else {
                    if (!empty($flowAction->template)) {
                        $flowAction->template->delete();
                    }
                }
            }
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
                $key_split = explode('_', $key);
                if (count($key_split) == 2) {
                    if (!empty($obj->pair[$item]))
                        $module_data->$key = $obj->pair[$item];
                }
                if ($key == 'groupNames' && !empty($item)) {
                    $groupNames = collect($item)->map(function ($value) use ($obj) {
                        if (!empty($value))
                            $value->flowAction = $obj->pair[$value->flowAction];
                        return $value;
                    })->toArray();
                    $module_data->$key = $groupNames;
                }
            });
            $flowAction = FlowAction::where('id', $obj->pair[$action->id])->first();
            $flowAction->module_data = $module_data;
            $flowAction->save();
        });

        // return new CustomResource($campaign);
        return new CustomResource(["message" => "Copied Successfully."]);
    }

    public function fetchFlowActionID(FetchActionlogIDRequest $request)
    {
        $flowActionID = $request->campaign->flowActions()->pluck('id')->toarray();
        return new CustomResource($flowActionID);
    }
}
