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
        Condition::truncate();

        /*
         * creating an array for all the rows 
         */
        $conditionsArr = [
            [
                'name' => 'Success',
                'is_boolean' => true,
                'wait_to_fail' => true
            ],
            [
                'name' => 'Failure',
                'is_boolean' => true,
                'wait_to_fail' => true
            ],
            [
                'name' => 'Read',
                'is_boolean' => true,
                'wait_to_fail' => true
            ],
            [
                'name' => 'Unread',
                'is_boolean' => true,
                'wait_to_fail' => true
            ],
            [
                'name' => 'Wait',
                'is_boolean' => false,
                'wait_to_fail' => false
            ]
        ];

        /*
         * checks if this array has same length with the number of rows in database
         */

        /*
         * checks for every element in array if it is already present and executing query to create if not
         */
        collect($conditionsArr)->map(function ($condition) {
            Condition::create($condition);
        });
    }
}
