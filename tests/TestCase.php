<?php

namespace StreetMesh\Domicile\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use StreetMesh\Domicile\DomicileServiceProvider;
use StreetMesh\Protocol\Laravel\ProtocolServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [ProtocolServiceProvider::class, DomicileServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('streetmesh.host', 'home.test');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../vendor/streetmesh/protocol-laravel/database/migrations');
    }
}
