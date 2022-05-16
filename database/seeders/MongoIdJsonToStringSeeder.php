<?php

namespace Database\Seeders;

use App\Models\ActionLog;
use Illuminate\Database\Seeder;

class MongoIdJsonToStringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actionLogs = ActionLog::all();
        collect($actionLogs)->map(function ($actionLog) {
            $mongoId = $actionLog->mongo_id;
            $mongoId = str_replace('"', '', $mongoId);
            $actionLog->mongo_id = $mongoId;
            $actionLog->save();
        });
    }
}
