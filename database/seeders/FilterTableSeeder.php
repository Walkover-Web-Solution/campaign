<?php

namespace Database\Seeders;

use App\Models\Filter;
use Illuminate\Database\Seeder;

class FilterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Filter::truncate();

        $filters = [
            [
                "name" => "Countries",
                "source" => "https://bucketcampaign.s3.ap-east-1.amazonaws.com/country_codes.json"
            ]
        ];
        collect($filters)->map(function ($filter) {
            Filter::create($filter);
        });
    }
}
