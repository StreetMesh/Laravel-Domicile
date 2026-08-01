<?php

namespace StreetMesh\Domicile\Http;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class ResidentsController
{
    public function __invoke(Factory $views): View
    {
        return $views->make('domicile::residents');
    }
}
