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
        $condition = Condition::all();
        if (!$condition->isEmpty()) {
            return true;
        }

        Condition::create([
            'id' => 1,
            'name' => 'wait',
            'configuration' => array('wait_time' => 'in seconds')
        ]);
    }
}
