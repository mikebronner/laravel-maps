<?php namespace GeneaLabs\LaravelMaps\Providers;

use GeneaLabs\LaravelMaps\Map;
use GeneaLabs\LaravelMaps\Facades\Map as MapFacade;
use Illuminate\Support\ServiceProvider;

class Service extends ServiceProvider
{
    protected $defer = false;

    public function register()
    {
        $this->app->singleton('map', function () {
            return new Map(['apiKey' => config('services.google.maps.api-key')]);
        });


        AliasLoader::getInstance()->alias('Map', MapFacade::class);
    }

    public function provides() : array
    {
        return array('genealabs-laravel-maps');
    }
}
