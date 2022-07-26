<?php

namespace Database\Seeders;

use App\Models\FlowAction;
use Illuminate\Database\Seeder;
use stdClass;

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
            $obj = new stdClass();
            $obj->isDelayKey = false;
            $configurations = collect($flowAction->configurations)->map(function ($item) use ($obj) {
                if ($item->name == 'delay') {
                    $obj->isDelayKey = true;
                    if (empty($item->unit)) {
                        $item->unit = "seconds";
                    }
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
            if (!$obj->isDelayKey) {
                $delay = array(
                    "name" => "delay",
                    "label" => "Delay for",
                    "type" => "text",
                    "source" => "",
                    "sourceFieldLabel" => "",
                    "sourceFieldValue" => "",
                    "is_required" => false,
                    "unit" => "seconds",
                    "value" => "0",
                    "subpart" => array(
                        "name" => "time",
                        "label" => "in",
                        "type" => "dropdown",
                        "source" => "/units?unit=time",
                        "sourceFieldLabel" => "data",
                        "sourceFieldValue" => "",
                        "is_required" => true,
                        "value" => "seconds"
                    )
                );
                array_push($configurations, $delay);
            }
            $flowAction->configurations = $configurations;
            $flowAction->save();
        });
    }
}
