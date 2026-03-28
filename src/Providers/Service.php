<?php namespace GeneaLabs\LaravelMaps\Providers;

use GeneaLabs\LaravelMaps\Map;
use Illuminate\Support\ServiceProvider;

class Service extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('map', function () {
            return new Map(['apiKey' => config('services.google.maps.api-key')]);
        });
    }
}
