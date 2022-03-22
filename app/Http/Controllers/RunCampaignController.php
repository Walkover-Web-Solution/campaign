<?php

namespace App\Http\Controllers;

use App\Http\Requests\RunCampaignRequest;
use Illuminate\Http\Request;

class RunCampaignController extends Controller
{
    public function run(RunCampaignRequest $request)
    {
        $campaign = $request->campaign;
        $input = $request->all();

        \JOB::processRunCampaign($input, $campaign);

        $message = 'Campaign Run Successfully';

        return response([
            'message' => $message
        ]);
    }
    public function runBulk(RunCampaignRequest $request, MongoDBLib  $mongo, MSG91Service $msg91)
    {
        $campaign = $request->campaign;


        if ($request->filled('request_id') && $request->filled('data')) {

            // adding data to request data
            $bulkRequest = $mongo->collection('run_campaign_data')->findOne(['request_id' => $request->request_id]);

            $input = $bulkRequest['data']->jsonSerialize();

            $data = array_merge($input, $request->data);

            $mongo->collection('run_campaign_data')->update(
                ['request_id' => $request->request_id],
                ['data' => $data]
            );

            return new CustomResource(['message' => 'Batch added successfully']);
        } elseif ($request->has('request_id') && $request->missing('data')) {
            // close the request
            $bulkCampaignRequest = BulkCampaignRequest::where('request_id', $request->request_id)->first();
            $bulkCampaignRequest->update([
                'run_at' => date('Y-m-d H:i:s')
            ]);
            \RMQ::processBulkCampaignRequest($bulkCampaignRequest);
            //$msg91->sendBulkDataToCampaign($bulkCampaignRequest);
            $bcrReq = $mongo->collection('run_campaign_data')->findOne(['request_id' => $request->request_id]);
            $totalRecords = count($bcrReq['data']);
            return new CustomResource(
                [
                    'message' => 'Request closed for run',
                    'total_records' => $totalRecords
                ]
            );
        } else {
            // create bulk request 
            $bulkRequest = BulkCampaignRequest::create([
                'campaign_id' => $campaign->id
            ]);
            $mongo->collection('run_campaign_data')->insertOne([
                'request_id' => $bulkRequest->request_id,
                'data' => []
            ]);
            return new CustomResource($bulkRequest->toArray());
        }
    }
}
