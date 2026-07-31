<?php

namespace StreetMesh\Domicile;

use Illuminate\Support\ServiceProvider;
use StreetMesh\Protocol\Laravel\Capabilities\Capabilities;

class DomicileServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->make(Capabilities::class)->register(new DomicileCapability);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'domicile');

        /*
         * Mounted where the application says, defaulting to the root only
         * because a server with nothing else installed should still answer
         * somewhere. A prefix set in configuration is how a server that is also
         * a venue keeps both halves reachable.
         */
        $this->app['router']
            ->middleware('web')
            ->prefix((string) config('streetmesh.mount.domicile', ''))
            ->group(__DIR__.'/../routes/web.php');
    }
}
