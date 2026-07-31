<?php

namespace StreetMesh\Domicile\Tests;

use StreetMesh\Protocol\Laravel\Capabilities\Capabilities;

class DomicileTest extends TestCase
{
    public function test_installing_it_makes_the_server_say_it_hosts_residents(): void
    {
        $capabilities = $this->app->make(Capabilities::class);

        $this->assertTrue($capabilities->has('domicile'));
        $this->assertSame(['domicile'], $capabilities->names());
    }

    /**
     * The wire and the interface read the same list, so they cannot come to
     * disagree about what this server does.
     */
    public function test_the_did_document_says_so_too(): void
    {
        $document = $this->get('/.well-known/did.json')->assertOk()->json();

        $this->assertSame('AtprotoPersonalDataServer', $document['service'][0]['type']);
    }

    public function test_it_serves_a_home(): void
    {
        $this->get('/')->assertOk()->assertSee('A home on StreetMesh');
    }

    /**
     * Alone, it may have the front page. That it does not *claim* it is the
     * point — the application grants it.
     */
    public function test_it_takes_the_front_page_only_because_nothing_else_wants_it(): void
    {
        $this->assertSame('domicile.home', $this->app->make(Capabilities::class)->homeRoute());
    }
}
