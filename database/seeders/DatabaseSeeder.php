<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(ClientTableSeeder::class);
        $this->call(ChannelTypeTableSeeder::class);
<<<<<<< HEAD
        $this->call(IPTypesTableSeeder::class);
=======
        $this->call(IPTypeTableSeeder::class);
        $this->call(ConditionTableSeeder::class);
>>>>>>> 298e3cb61d62ef84171033bacb48a895e7316b65
    }
}
