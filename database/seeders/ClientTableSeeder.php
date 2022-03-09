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
<<<<<<< HEAD
        $clients = Client::all();
        if (!$clients->isEmpty()) {
            return true;
        }

        Client::create([
            'name' => "Campaign  Admin",
            'email' => 'campaign@gmail.com',
            'meta' => []
        ]);

        Client::create([
            'name' => "MSG91",
            'email' => 'msg91@gmail.com',
            'meta' => []
        ]);
=======
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
        foreach ($clientsArr as $client) {
            $clientObj = Client::where('id', $client['email'])->first();
            if (empty($clientObj)) {
                Client::create($client);
            } else {
                $clientObj->update($client);
            }
        }
>>>>>>> 298e3cb61d62ef84171033bacb48a895e7316b65
    }
}
