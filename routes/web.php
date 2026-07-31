<?php

use Illuminate\Support\Facades\Route;
use StreetMesh\Domicile\Http\HomeController;

/*
 * Nothing here claims the front page. The application mounts these wherever it
 * decides, because a server that is also a venue cannot give both halves the
 * same door and neither half can settle which one gets it.
 */
Route::get('/', HomeController::class)->name('domicile.home');
