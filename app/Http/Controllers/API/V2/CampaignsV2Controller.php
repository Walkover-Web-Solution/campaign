<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\CopyCampaignRequest;
use App\Http\Requests\CreateCampaignV2Request;
use App\Http\Requests\UpdateCampaignV2Request;
use App\Http\Resources\CustomResource;
use App\Models\Campaign;
use App\Models\FlowAction;
use Illuminate\Http\Request;

class CampaignsV2Controller extends Controller
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
    public function store(CreateCampaignV2Request $request)
    {
        //validating request
        $input = $request->validated();

        // create campaign with the company assoication
        $campaign = $request->company->campaigns()->create($input);

        // if (!empty($input['modules'])) {
        //     $data = getFlows($input['modules']);

        //     $campaign->flowActions()->createMany($data);
        // }

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
    public function update(UpdateCampaignV2Request $request, Campaign $campaign)
    {
        $input = $request->validated();

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
        return new CustomResource(['message' => "Delete Campaign successfully"]);
    }

};
