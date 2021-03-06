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
        // No need to run client seeder for now
        // $this->call(ClientTableSeeder::class);
        $this->call(ChannelTypeTableSeeder::class);
        $this->call(IPTypeTableSeeder::class);
        $this->call(EventTableSeeder::class);
        $this->call(ChannelEventTableSeeder::class);
        $this->call(ConditionTableSeeder::class);
        $this->call(FilterTableSeeder::class);
        // $this->call(AddUnitInDelayFlowActionSeeder::class);
        // $this->call(RemoveReadUnreadFromEmailModuleData::class);
        // $this->call(UpdateGroupNamesFlowActionId::class);
        $this->call(CreateRefIdRelationForExistingRefIds::class);
        $this->call(UpdateCampaignLogStatusFor1KDataFail::class);
        $this->call(UpdateCampaignLogandActionLogStatusSeeder::class);

        // $this->call(MongoIdJsonToStringSeeder::class);
    }
}
