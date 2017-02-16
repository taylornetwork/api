<?php

namespace TaylorNetwork\API\Facades;

use Illuminate\Support\Facades\Facade;

class APIFacade extends Facade
{
    /**
     * Get the facade accessor
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'API';
    }
}