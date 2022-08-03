<?php

namespace Database\Seeders;

use App\Models\ActionLog;
use Illuminate\Database\Seeder;

class CreateRefIdRelationForExistingRefIds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actionLogs = ActionLog::where('ref_id', '!=', "")->get();

        collect($actionLogs)->map(function ($actionLog) {
            $actionLog->ref_id()->create([
                'ref_id' => $actionLog->ref_id,
                'status' => $actionLog->status,
                'response' => $actionLog->response,
                'no_of_records' => $actionLog->no_of_records
            ]);
            $actionLog->ref_id = "";
            $actionLog->save();
        });
    }
}
