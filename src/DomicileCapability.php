<?php

namespace StreetMesh\Domicile;

use StreetMesh\Protocol\Laravel\Capabilities\Capability;

/**
 * This server is somewhere people live.
 *
 * It says so on the wire and in the navigation, and it does not say where it
 * sits — a capability that claimed the front page would be claiming ground it
 * shares with whatever else is installed.
 */
final class DomicileCapability implements Capability
{
    public function name(): string
    {
        return 'domicile';
    }

    public function serviceType(): string
    {
        // The name ATProtocol uses for the same thing, so a stranger reading
        // this document knows what it is without knowing what StreetMesh is.
        return 'AtprotoPersonalDataServer';
    }

    public function home(): string
    {
        return 'domicile.home';
    }

    /**
     * @return array<int, array{label: string, route: string, icon?: string}>
     */
    public function navigation(): array
    {
        return [
            ['label' => 'Home', 'route' => 'domicile.home', 'icon' => 'home'],
        ];
    }
}
