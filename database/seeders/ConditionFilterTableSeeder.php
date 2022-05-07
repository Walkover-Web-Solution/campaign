<?php

namespace Database\Seeders;

use App\Models\Condition;
use App\Models\ConditionFilter;
use App\Models\Filter;
use Illuminate\Database\Seeder;

class ConditionFilterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConditionFilter::truncate();

        // get all condition map of name:id
        $conditions = Condition::select('name', 'id')->get()->toArray();
        $conditionMap = array_column($conditions, 'id', 'name');

        // get all filter map of name:id
        $filters = Filter::select('name', 'id')->get()->toArray();
        $filterMap = array_column($filters, 'id', 'name');


        $arr = [
            [
                'condition_id' => $conditionMap['Countries'],
                'filter_id' => $filterMap['Countries'],
            ]
        ];


        ConditionFilter::insert($arr);
    }
}
