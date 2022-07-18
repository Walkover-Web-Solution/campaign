<?php

namespace Database\Seeders;

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
    public function run()
    {
        $campaignLogs = CampaignLog::doesnthave('actionLogs')->get();
        collect($campaignLogs)->map(function ($campaignLog) {
            $failJob = FailedJob::where('uuid', $campaignLog->id)->first();
            $campaignLog->status = 'Error' . (!empty($failJob) ? ' - ' . $failJob->id : '');
            $campaignLog->save();
        });
    }
}
