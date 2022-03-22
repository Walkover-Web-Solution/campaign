<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomResource;
use App\Models\Condition;
use Illuminate\Http\Request;

class ConditionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return new CustomResource(Condition::all());
    }
}
