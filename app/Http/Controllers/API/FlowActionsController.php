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
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use stdClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $flowAction->update($input);

        if (isset($input['configurations'])) {
            $obj = collect($input['configurations'])->where('name', 'template')->first();
            $template = null;
            if (!empty($obj['template']['template_id'])) {
                if ($flowAction->channel_id == 1)
                    $obj['template']['template_id'] = $obj['template']['slug'];
                $template = $obj['template'];
                $template['variables'] = isset($obj['variables']) ? $obj['variables'] : [];
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

        $newVariables = collect($input['campaign_variables'])->where('id', null)->toArray();
        $existingVariables = collect($input['campaign_variables'])->whereNotNull('id')->pluck('id')->toArray();
        $flowAction->campaign_variables()->sync($existingVariables);
        try {
            $flowAction->campaign_variables()->createMany($newVariables);
        } catch (QueryException $e) {
            return new CustomResource(['message' => $e->getBindings()[0] . ' Already Exists!'], true);
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

        $obj = new \stdClass();
        $obj->wrongParent = false;
        if (isset($request->parent_data)) {
            collect($request->parent_data)->map(function ($parent) use ($campaign, $flowAction, $obj) {
                if (!empty($parent['op_type']) && $parent['op_type'] == "op_start") {
                    $campaign->module_data = array(
                        "op_start" => null,
                        "op_start_type" => null
                    );
                    $campaign->update();
                } else {
                    $parentFlow = FlowAction::where('id', $parent['module_id'])->where('campaign_id', $campaign->id)->first();
                    if (!empty($parentFlow)) {
                        //fetch previous module_data
                        $module_data = $parentFlow->module_data;

                        $obj->nullIds = [];
                        // In case of condition as parent node, null every key which is connected to this flowAction
                        if ($parentFlow->channel_id == 6) {
                            $changedModuleData = collect($module_data)->map(function ($item, $key) use ($flowAction, $obj) {
                                $keySplit = explode('_', $key);
                                if (count($keySplit) == 2) {
                                    if ($item == $flowAction->id) {
                                        if ($keySplit[1] != 'others') {
                                            array_push($obj->nullIds, $item);
                                        }
                                        return null;
                                    } else {
                                        return $item;
                                    }
                                } else if ($key != 'groupNames') {
                                    return $item;
                                }
                            })->toArray();
                            $obj->nullIds = array_unique($obj->nullIds);
                            if (!empty($module_data->groupNames)) {
                                $groupNames = collect($module_data->groupNames)->map(function ($item) use ($obj) {
                                    if (in_array($item->flowAction, $obj->nullIds)) {
                                        $item->flowAction = null;
                                    }
                                    return $item;
                                })->toArray();
                                $changedModuleData['groupNames'] = $groupNames;
                                $module_data = $changedModuleData;
                            }
                        } else {
                            if (empty($parent['op_type'])) {
                                $obj->wrongParent = true;
                                return;
                            }
                            $op_type = $parent['op_type'];
                            if ($parentFlow->op_type != $flowAction->id) {
                                $obj->wrongParent = true;
                                return;
                            }
                            //modify it
                            $module_data->$op_type = null;
                        }

                        //saved to db back
                        $parentFlow->module_data = $module_data;
                        $parentFlow->save();
                    } else {
                        $obj->wrongParent = true;
                    }
                }
            });
        }
        if ($obj->wrongParent) {
            throw new NotFoundHttpException("Parent node doesn't exists");
        }
        $flowAction->delete();

        return new CustomResource(['message' => "Flowaction Deleted Successfully."]);
    }
}
