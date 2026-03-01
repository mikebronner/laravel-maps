<?php namespace GeneaLabs\LaravelMaps\Tests;

use GeneaLabs\LaravelMaps\Providers\Service;

trait CreatesApplication
{
    protected function getPackageProviders($app): array
    {
        return [
            Service::class,
        ];
    }
}
