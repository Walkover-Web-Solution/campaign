<?php

namespace Database\Seeders;

use App\Models\ChannelTypeCondition;
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
        $count = ChannelTypeCondition::all()->count();
        $arr = [
            [
                'channel_type_id' => 1,
                'condition_id' => 1,
            ],
            [
                'channel_type_id' => 1,
                'condition_id' => 2,
            ],
            [
                'channel_type_id' => 1,
                'condition_id' => 3,
            ],
            [
                'channel_type_id' => 1,
                'condition_id' => 4,
            ],
            [
                'channel_type_id' => 2,
                'condition_id' => 1,
            ],
            [
                'channel_type_id' => 2,
                'condition_id' => 2,
            ]
        ];

        if ($count == count($arr)) {
            return true;
        }

        collect($arr)->map(function ($condition) {
            $conditionObj = ChannelTypeCondition::where('channel_type_id', $condition['channel_type_id'])->where('condition_id', $condition['condition_id'])->first();
            if (empty($conditionObj)) {
                ChannelTypeCondition::create($condition);
            } else {
                $conditionObj->update($condition);
            }
        });
    }
}
