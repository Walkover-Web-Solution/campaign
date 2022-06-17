<?php

namespace Database\Seeders;

use App\Models\FlowAction;
use Illuminate\Database\Seeder;

class AddSubpartKeyInDelayFlowActionSeeder extends Seeder
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
                    if (empty($item->subpart)) {
                        $item->subpart = array(
                            "name" => "time",
                            "label" => "in",
                            "type" => "dropdown",
                            "source" => "/units?unit=time",
                            "sourceFieldLabel" => "data",
                            "sourceFieldValue" => "",
                            "is_required" => true,
                            "value" => "seconds"
                        );
                    }
                }

                return $item;
            })->toArray();
            $flowAction->configurations = $configurations;
            $flowAction->save();
        });
    }
}
