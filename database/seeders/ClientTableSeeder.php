<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientTableSeeder extends Seeder
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
        $clientsCount = Client::all()->count();

        /*
         * creating an array for all the rows 
         */
        $clientsArr = [
            [
                'name' => "Campaign  Admin",
                'email' => 'campaign@gmail.com',
                'meta' => '[]'
            ],
            [
                'name' => "MSG91",
                'email' => 'msg91@gmail.com',
                'meta' => '[]'
            ]
        ];

        /*
         * checks if this array has same length with the number of rows in database
         */
        if ($clientsCount == count($clientsArr)) {
            return true;
        }

        /*
         * checks for every element in array if it is already present and executing query to create if not
         */
        collect($clientsArr)->map(function ($client) {
            $clientObj = Client::where('id', $client['email'])->first();
            if (empty($clientObj)) {
                Client::create($client);
            } else {
                $clientObj->update($client);
            }
        });
    }
}