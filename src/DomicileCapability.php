<?php

namespace StreetMesh\Domicile;

use StreetMesh\Protocol\Laravel\Capabilities\Capability;
use StreetMesh\Protocol\Laravel\Capabilities\Widget;
use StreetMesh\Protocol\Laravel\Identity\Identities;

/**
 * This server is somewhere people live.
 *
 * It says so on the wire, offers something to greet strangers with, and offers
 * a panel for a signed-in person's home page. It does not decide where any of
 * that goes, because a server may offer more than one capability and only the
 * application can arrange them.
 */
final class DomicileCapability implements Capability
{
    public function name(): string
    {
        return 'domicile';
    }

    public function serviceType(): string
    {
        return 'AtprotoPersonalDataServer';
    }

    public function frontPage(): string
    {
        return 'domicile::front';
    }

    /**
     * @return array{label: string, route: string}
     */
    public function frontAction(): array
    {
        return ['label' => 'Sign in', 'route' => 'login'];
    }

    /**
     * A resident: an account here, and a session the framework understands.
     *
     * @return null|array{name: string, leave: array{label: string, route: string}}
     */
    public function whoever(): ?array
    {
        $user = auth()->user();

        if ($user === null) {
            return null;
        }

        return [
            'name' => (string) (app(Identities::class)->forUser($user)?->handle ?? $user->name),
            'leave' => ['label' => 'Log out', 'route' => 'logout'],
        ];
    }

    /**
     * @return array<int, Widget>
     */
    public function widgets(): array
    {
        return [new DomicileWidget];
    }

    /**
     * @return array<int, array{label: string, route: string, icon?: string}>
     */
    public function navigation(): array
    {
        return [
            ['label' => 'Directory', 'route' => 'domicile.directory', 'icon' => 'user-group'],
        ];
    }
}
