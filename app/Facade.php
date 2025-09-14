<?php

namespace App;

use Illuminate\Support\Facades\Facade;

class AppFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'app';
    }
}