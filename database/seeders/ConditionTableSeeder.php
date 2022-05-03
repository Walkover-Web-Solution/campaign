<?php

namespace Database\Seeders;

use App\Models\Condition;
use Illuminate\Database\Seeder;

class ConditionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Condition::truncate();

        $conditions = [
            [
                "name" => "Countries",
                "configurations" => array(
                    "fields" => array(
                        array(
                            "name" => "delay",
                            "label" => "Delay for",
                            "type" => "text",
                            "source" => "",
                            "sourceFieldLabel" => "",
                            "sourceFieldValue" => "",
                            "is_required" => false,
                            "value" => "0"
                        )
                    ),
                    "mapping" => array()
                )
            ]
        ];

        collect($conditions)->map(function ($condition) {
            Condition::create($condition);
        });
    }
}
