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
        /*
         * getting count of rows from db table
         */
        $conditionsCount = Condition::all()->count();

        /*
         * creating an array for all the rows 
         */
        $conditionsArr = [
            [
                'id' => 1,
                'name' => 'wait',
                'configuration' => array('wait_time' => 'in seconds')
            ]
        ];

        /*
         * checks if this array has same length with the number of rows in database
         */
        if ($conditionsCount == count($conditionsArr)) {
            return true;
        }

        /*
         * checks for every element in array if it is already present and executing query to create if not
         */
        foreach ($conditionsArr as $condition) {
            $conditionObj = Condition::where('id', $condition['id'])->first();
            if (empty($conditionObj)) {
                Condition::create($condition);
            }
        }
    }
}
