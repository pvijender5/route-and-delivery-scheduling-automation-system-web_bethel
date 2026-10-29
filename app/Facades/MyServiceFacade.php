<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MyServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Services\MyService::class;
    }
}
