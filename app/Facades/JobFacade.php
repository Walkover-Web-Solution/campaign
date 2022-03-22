<?php

namespace  App\Facades;

use Illuminate\Support\Facades\Facade;

class JobFacade extends Facade{

	protected static function getFacadeAccessor(){
        return 'job';
    }


}