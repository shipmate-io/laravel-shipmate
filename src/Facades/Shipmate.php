<?php

namespace Shipmate\Shipmate\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Shipmate\Shipmate\Shipmate
 */
class Shipmate extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Shipmate\Shipmate\Shipmate::class;
    }
}
