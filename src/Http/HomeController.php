<?php

namespace StreetMesh\Domicile\Http;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use StreetMesh\Protocol\Laravel\Capabilities\Capabilities;
use StreetMesh\Protocol\Laravel\Identity\Identities;

class HomeController
{
    public function __invoke(Factory $views, Identities $identities, Capabilities $capabilities): View
    {
        return $views->make('domicile::home', [
            'identity' => $identities->forServer(),

            // Rendered from whatever is installed rather than from a list kept
            // here, so a venue appearing beside this one shows up without this
            // package knowing it exists.
            'navigation' => $capabilities->navigation(),
        ]);
    }
}
