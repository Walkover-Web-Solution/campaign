<?php

namespace Database\Seeders;

use App\Libs\MongoDBLib;
use App\Models\ActionLog;
use App\Models\CampaignLog;
use App\Models\FailedJob;
use App\Models\FlowAction;
use Illuminate\Database\Seeder;

class UpdateCampaignLogStatusFor1KDataFail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $mongo;
    public function run()
    {
        if (empty($this->mongo)) {
            $this->mongo = new MongoDBLib;
        }
        $campaignLogs = CampaignLog::doesnthave('actionLogs')->get();
        collect($campaignLogs)->map(function ($campaignLog) {
            $failJob = FailedJob::where('uuid', $campaignLog->id)->first();
            $campaignLog->status = 'Error' . (!empty($failJob) ? ' - ' . $failJob->id : '');

            $data = $this->mongo->collection('run_campaign_data')->find([
                'requestId' => $campaignLog->mongo_uid
            ]);
            if (!empty($data)) {
                $campaignLog->can_retry = true;
            }
            $campaignLog->save();
        });
    }
}
