<?php

use Illuminate\Support\Facades\Route;
use StreetMesh\Domicile\Http\ResidentsController;

/*
 * One screen, at a name nothing else wants. Drawn by a controller rather than a
 * Livewire component until the mechanism for a package to ship one is settled —
 * see ResidentsController.
 */
Route::get('residents', ResidentsController::class)->name('domicile.residents');
