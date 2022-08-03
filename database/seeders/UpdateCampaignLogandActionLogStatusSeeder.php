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
        CampaignLog::where('status', 'Complete')->update([
            'status' => 'Completed'
        ]);

        ActionLog::where('status', 'Success')->orWhere('status', 'Failed')->update([
            'status' => 'Completed'
        ]);
    }
}
