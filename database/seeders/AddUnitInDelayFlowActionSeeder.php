<?php

namespace Database\Seeders;

use App\Models\FlowAction;
use Illuminate\Database\Seeder;

class AddUnitInDelayFlowActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $flowActions = FlowAction::all();

        $flowActions->map(function ($flowAction) {
            $configurations = collect($flowAction->configurations)->map(function ($item) {
                if ($item->name == 'delay') {
                    if (empty($item->unit)) {
                        $item->unit = "seconds";
                    }
                }
                return $item;
            })->toArray();
            $flowAction->configurations = $configurations;
            $flowAction->save();
        });
    }
}
