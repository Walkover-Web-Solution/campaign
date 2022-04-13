<?php

namespace Database\Seeders;

use App\Models\IpType;
use Illuminate\Database\Seeder;

class IPTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IpType::firstOrCreate(['name' => 'Whitelist IP']);
        IPType::firstOrCreate(['name' => 'Blacklist IP']);
        IPType::firstOrCreate(['name' => 'Temporarily Blocked IP']);
        IPType::firstOrCreate(['name' => 'None']);
    }
}
