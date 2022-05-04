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
                    "fields" => array(),
                    "mapping" => array()
                )
            ]
        ];

        collect($conditions)->map(function ($condition) {
            Condition::create($condition);
        });
    }
}
