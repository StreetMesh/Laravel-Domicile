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
         * At its own name, with no prefix. There is nothing here another
         * capability would also want, so there is nothing to arrange — the two
         * surfaces that overlap, the front page and the home page, belong to
         * the application.
         */
        $this->app['router']
            ->middleware('web')
            ->group(__DIR__.'/../routes/web.php');
    }
}
