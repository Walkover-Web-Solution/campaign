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
        $campaignLog = CampaignLog::all();
        collect($campaignLog)->map(function ($campLog) {
            $actionLog = $campLog->actionLogs()->first();
            if (empty($actionLog)) {
                $failJobId = FailedJob::where('uuid', $campLog->id)->first();
                $campLog->status = 'Error - ' . $failJobId->id;
                $campLog->save();
            }
        });
    }
}
