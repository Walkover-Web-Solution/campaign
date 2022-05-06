<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomResource;
use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConditionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $obj = new \stdClass();
        $obj->conditions = [];
        $obj->filters = [];

        $conditons = Condition::all();

        $conditons->map(function ($condition) use ($obj) {
            $filters = $condition->filters()->get();
            $filters->map(function ($filter) use ($obj) {
                if (empty($filter->source)) {
                    $name = [$filter->name];
                } else {
                    $countriesJson = Cache::get('countriesJson');
                    if (empty($countriesJson)) {
                        $countriesJson = json_decode(file_get_contents($filter->source));
                        Cache::put('countriesJson', $countriesJson, 86400);
                    }
                    $name = collect($countriesJson)->pluck('Country code')->toArray();
                }
                $obj->filters = $name;
            });
            $condition = $condition->toArray();
            // change conditions to filters when UI get updated - TASK
            $condition['conditions'] = $obj->filters;
            $obj->filters = [];
            array_push($obj->conditions, $condition);
        });
        return new CustomResource($obj->conditions);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
