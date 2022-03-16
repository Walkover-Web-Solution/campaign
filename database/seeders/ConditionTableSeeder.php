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
                'name' => 'Wait',
                'is_boolean' => false,
                'wait_to_fail' => false
            ],
            [
                'name' => 'Read',
                'is_boolean' => true,
                'wait_to_fail' => true
            ],
            [
                'name' => 'Delivered',
                'is_boolean' => true,
                'wait_to_fail' => true
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
        collect($conditionsArr)->map(function ($condition) {
            $conditionObj = Condition::where('name', $condition['name'])->first();
            if (empty($conditionObj)) {
                Condition::create($condition);
            } else {
                $conditionObj->update($condition);
            }
        });
    }
}
