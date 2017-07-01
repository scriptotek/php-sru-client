<?php

namespace Scriptotek\Sru\Facades;

use Illuminate\Support\Facades\Facade;

class SruClient extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Scriptotek\Sru\Client::class;
    }
}
