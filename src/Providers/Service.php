<?php namespace GeneaLabs\LaravelMaps\Providers;

use GeneaLabs\LaravelMaps\Map;
use Illuminate\Support\ServiceProvider;

class Service extends ServiceProvider
{
    protected $defer = false;

    public function register()
    {
        $this->app->singleton('map', function () {
            return new Map(['apiKey' => config('services.google.maps.api-key')]);
        });
    }

    public function provides() : array
    {
        return array('genealabs-laravel-maps');
    }
}
