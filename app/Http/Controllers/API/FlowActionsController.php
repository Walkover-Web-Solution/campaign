<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFlowActionsRequest;
use App\Http\Requests\UpdateFlowActionRequest;
use App\Http\Resources\CustomResource;
use App\Models\Campaign;
use App\Models\ChannelType;
use App\Models\FlowAction;
use App\Models\TemplateDetail;
use Illuminate\Http\Request;

class FlowActionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(CreateFlowActionsRequest $request)
    {
        $input = $request->validated();

        if (!$input) {
            return new CustomResource(["message" => "Module Data doesn't Belongs to Campaign"], true);
        }

        //create flowAction
        $flowAction = $request->campaign->flowActions()->create($input);

        //create Template
        if (!empty($input['template'])) {
            $template = $flowAction->template()->create($input['template']);
        }

        $flowAction->is_completed = validateFlow($flowAction);
        $flowAction->save();

        return new CustomResource($flowAction);
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
    public function update(UpdateFlowActionRequest $request, $slug, FlowAction $flowAction)
    {
        $input = $request->validated();

        // validate if any id from module data belongs to this campaign or not
        if (isset($request->module_data)) {
            $obj = new \stdClass();
            $obj->flag = false;
            $conditions = ChannelType::where('id', $request->flowAction->channel_id)->first()->conditions()->pluck('name');
            $conditions->map(function ($item) use ($obj, $request) {
                $key = 'op_' . strtolower($item);
                if (isset($request->module_data[$key]) &&  $request->module_data[$key] != null) {
                    $flow = $request->campaign->flowActions()->where('id', $request->module_data[$key])->first();
                    if (empty($flow)) {
                        $obj->flag = true;
                        return $key;
                    }
                }
            });
            if ($obj->flag)
                return new CustomResource(["message" => "Module Data doesn't Belongs to Campaign"], true);
        }

        $flowAction->update($input);

        if (isset($input['configurations'])) {
            $obj = collect($input['configurations'])->where('name', 'template')->first();
            $template = null;
            if (!empty($obj['template']['template_id'])) {
                $template = $obj['template'];
                $template['variables'] = $obj['variables'];
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

        //validate if is_completed
        $flowAction->is_completed = validateFlow($flowAction);
        $flowAction->save();

        return new CustomResource($flowAction);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $slug, FlowAction $flowAction)
    {
        $campaign = Campaign::where('slug', $slug)->where('company_id', $request->company->id)->first();
        if (empty($campaign)) {
            return new CustomResource(['message' => "Campaign Not Found."]);
        }

        //delete template related to this flowAction
        $flowAction->template()->delete();

        if (isset($request->parent_data)) {
            collect($request->parent_data)->map(function ($parent) use ($campaign) {
                $parentFlow = FlowAction::where('id', $parent['module_id'])->where('campaign_id', $campaign->id)->first();
                if (!empty($parentFlow)) {
                    //fetch previous module_data
                    $module_data = $parentFlow->module_data;

                    //modify it
                    $op_type = $parent['op_type'];
                    $module_data->$op_type = null;

                    //saved to db back
                    $parentFlow->module_data = $module_data;
                    $parentFlow->save();
                }
            });
        }
        $flowAction->delete();

        return new CustomResource(['message' => "Flowaction Deleted Successfully."]);
    }
}
