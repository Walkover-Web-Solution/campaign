<?php

namespace Database\Seeders;

use App\Models\ChannelType;
use App\Models\ChannelTypeEvent;
use App\Models\Event;
use Illuminate\Database\Seeder;

class ChannelEventTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ChannelTypeEvent::truncate();

        // get all events map of name:id
        $events = Event::select('name', 'id')->get()->toArray();
        $eventsMap = array_column($events, 'id', 'name');

        // get all channels map of name:id
        $channels = ChannelType::select('name', 'id')->get()->toArray();
        $channelMap = array_column($channels, 'id', 'name');


        $arr = [
            [
                'channel_type_id' => $channelMap['Email'],
                'event_id' => $eventsMap['Success'],
            ],
            [
                'channel_type_id' => $channelMap['Email'],
                'event_id' => $eventsMap['Failed'],
            ],
            // [
            //     'channel_type_id' => $channelMap['Email'],
            //     'event_id' => $eventsMap['Read'],
            // ],
            // [
            //     'channel_type_id' => $channelMap['Email'],
            //     'event_id' => $eventsMap['Unread'],
            // ],
            [
                'channel_type_id' => $channelMap['SMS'],
                'event_id' => $eventsMap['Success'],
            ],
            [
                'channel_type_id' => $channelMap['SMS'],
                'event_id' => $eventsMap['Failed'],
            ],
            [
                'channel_type_id' => $channelMap['RCS'],
                'event_id' => $eventsMap['Success'],
            ],
            [
                'channel_type_id' => $channelMap['RCS'],
                'event_id' => $eventsMap['Failed'],
            ],
            [
                'channel_type_id' => $channelMap['Whatsapp'],
                'event_id' => $eventsMap['Success'],
            ],
            [
                'channel_type_id' => $channelMap['Whatsapp'],
                'event_id' => $eventsMap['Failed'],
            ]
        ];


        ChannelTypeEvent::insert($arr);
    }
}
