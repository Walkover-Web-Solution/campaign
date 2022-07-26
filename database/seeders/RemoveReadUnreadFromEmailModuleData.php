<?php

namespace Database\Seeders;

use App\Models\FlowAction;
use Illuminate\Database\Seeder;

class RemoveReadUnreadFromEmailModuleData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emailNodes = FlowAction::where('channel_id', 1)->get();
        collect($emailNodes)->map(function ($email) {
            $module_data = (array)$email->module_data;
            unset($module_data['op_read']);
            unset($module_data['op_read_type']);
            unset($module_data['op_unread']);
            unset($module_data['op_unread_type']);
            $email->module_data = $module_data;
            $email->save();
        });
    }
}
