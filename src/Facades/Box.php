<?php

namespace Dcblogdev\Box\Facades;

use Illuminate\Support\Facades\Facade;

class Box extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'box';
    }
}
