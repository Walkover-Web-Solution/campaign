<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventTableSeeder extends Seeder
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
        Event::truncate();

        /*
         * creating an array for all the rows
         */
        $eventsArr = [
            [
                'name' => 'Success',
                'is_boolean' => true,
                'wait_to_fail' => true
            ],
            [
                'name' => 'Failed',
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

        Event::insert($eventsArr);
    }
}
