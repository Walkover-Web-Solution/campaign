<?php

namespace Database\Seeders;

use App\Models\ChannelType;
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
        ChannelTypeCondition::truncate();

        // get all conditions map of name:id
        $conditions = Condition::select('name', 'id')->get()->toArray();
        $conditionsMap = array_column($conditions, 'id', 'name');

        // get all channels map of name:id
        $channels = ChannelType::select('name', 'id')->get()->toArray();
        $channelMap = array_column($channels, 'id', 'name');


        $arr = [
            [
                'channel_type_id' => $channelMap['Email'],
                'condition_id' => $conditionsMap['Success'],
            ],
            [
                'channel_type_id' => $channelMap['Email'],
                'condition_id' => $conditionsMap['Failed'],
            ],
            [
                'channel_type_id' => $channelMap['Email'],
                'condition_id' => $conditionsMap['Read'],
            ],
            [
                'channel_type_id' => $channelMap['Email'],
                'condition_id' => $conditionsMap['Unread'],
            ],
            [
                'channel_type_id' => $channelMap['SMS'],
                'condition_id' => $conditionsMap['Success'],
            ],
            [
                'channel_type_id' => $channelMap['SMS'],
                'condition_id' => $conditionsMap['Failed'],
            ]
        ];


        collect($arr)->map(function ($condition) {
            ChannelTypeCondition::create($condition);
        });
    }
}
