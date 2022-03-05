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
    }
}
