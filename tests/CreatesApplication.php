<?php namespace GeneaLabs\LaravelMaps\Tests;

use GeneaLabs\LaravelMaps\Providers\Service as LaravelMapsService;
use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        $app->register(LaravelMapsService::class);

        return $app;
    }
}
