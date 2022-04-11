<?php

use App\Models\ChannelType;
use App\Models\Template;

function validateFlow($flowAction)
{
    $obj = new \stdClass();
    $obj->flag = true;
    $channel = ChannelType::where('id', $flowAction->channel_id)->first();
    collect($channel->configurations['fields'])->map(function ($item)  use ($flowAction, $obj) {
        if ($item['is_required']) {
            if ($item['name'] == 'template') {
                $flowItem = collect($flowAction->configurations)->where('name', $item['name'])->first();
                if (empty($flowItem->template->template_id)) {
                    $obj->flag = false;
                }
            } else {
                $flowItem = collect($flowAction->configurations)->where('name', $item['name'])->first();
                if (empty($flowItem->value)) {
                    $obj->flag = false;
                }
            }
        }
    });
    return ($obj->flag);
}
