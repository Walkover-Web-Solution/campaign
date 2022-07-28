<?php

namespace Database\Seeders;

use App\Models\ActionLog;
use App\Models\CampaignLog;
use Illuminate\Database\Seeder;

class UpdateCampaignLogandActionLogStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $campaigns = CampaignLog::where('status','Complete')->get();
        $campaigns->each(function ($campaign) {
            $campaign->status='Completed';
            $campaign->save();
        });

        $actionLogs=ActionLog::where('status','Success')->orWhere('status','Failed')->get();
        $actionLogs->each(function ($actionLog) {
            $actionLog->status='Completed';
            $actionLog->save();
        });
    }
}
