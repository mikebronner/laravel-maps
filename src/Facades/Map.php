<?php namespace GeneaLabs\LaravelMaps\Facades;

use Illuminate\Support\Facades\Facade;

class Map extends Facade
{
    protected static function getFacadeAccessor() : string
    {
        return 'map';
    }
}
