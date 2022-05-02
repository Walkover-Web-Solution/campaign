<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Seeder;

class UpdateSlugToCampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $campaigns = Campaign::whereNull('slug')->get();
        $campaigns->each(function ($campaign) {
            $campaign->slug = \Str::slug($campaign->name, '-');
            $campaign->save();
        });
    }
}
