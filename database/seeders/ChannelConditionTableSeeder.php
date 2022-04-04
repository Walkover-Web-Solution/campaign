<?php

namespace Database\Seeders;

use App\Models\ChannelCondition;
use App\Models\Condition;
use Illuminate\Database\Seeder;

class ChannelConditionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = ChannelCondition::all()->count();
        $arr = [
            [
                'channel_id' => 1,
                'condition_id' => 1,
            ],
            [
                'channel_id' => 1,
                'condition_id' => 2,
            ],
            [
                'channel_id' => 1,
                'condition_id' => 3,
            ],
            [
                'channel_id' => 1,
                'condition_id' => 4,
            ],
            [
                'channel_id' => 2,
                'condition_id' => 1,
            ],
            [
                'channel_id' => 2,
                'condition_id' => 2,
            ]
        ];

        if ($count == count($arr)) {
            return true;
        }

        collect($arr)->map(function ($condition) {
            $conditionObj = ChannelCondition::where('channel_id', $condition['channel_id'])->where('condition_id', $condition['condition_id'])->first();
            if (empty($conditionObj)) {
                ChannelCondition::create($condition);
            } else {
                $conditionObj->update($condition);
            }
        });
    }
}
