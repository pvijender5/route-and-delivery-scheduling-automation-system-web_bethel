<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AcumeticaFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Services\AcumeticaService::class;
    }
}
