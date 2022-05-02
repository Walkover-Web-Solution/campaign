<?php

namespace Database\Seeders;

use App\Models\FlowAction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentDomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emailFlowAction =  FlowAction::where('channel_id', 1)->get();

        $emailFlowAction->map(function ($item) {

            $domain = collect($item->configurations)->firstWhere('name', 'domain');
            $parent_domain = collect($item->configurations)->firstWhere('name', 'parent_domain');
            if (empty($parent_domain)) {
                $data=new \stdClass();
                $data->config=$item->configurations;
                $pdData = array(
                    "name" => "parent_domain",
                    "type" => 'dropdown',
                    "label" => 'Select Parent Domain',
                    "regex" => "",
                    "source" => "domains?is_enabled=1&status_id=2",
                    "sourceFieldLabel" => "name",
                    "sourceFieldValue" => "name",
                    "is_required" => true,
                    "value" => $domain->value
                );
                array_push($data->config,$pdData);
                $item->configurations=$data->config;
                $item->save();
            }
            else{

                $item->configurations=collect($item->configurations)->map(function($configs) use ($domain){
                    if($configs->name=='parent_domain'){
                        $configs->source=$domain->source;
                        $configs->value=$domain->value;
                    }
                    return $configs;
                });
                $item->save();
            }
        });
    }
}
