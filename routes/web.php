<?php

use Illuminate\Support\Facades\Route;

/*
 * One screen, at a name nothing else wants, drawn by a Livewire component this
 * package brought with it rather than published into the host.
 */
Route::livewire('residents', 'domicile::residents')->name('domicile.residents');
