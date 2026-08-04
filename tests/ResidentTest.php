<?php

namespace StreetMesh\Domicile\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use StreetMesh\Domicile\Residents\Handle;
use StreetMesh\Domicile\Residents\Residents;
use StreetMesh\Domicile\Tests\Fixtures\Resident;
use StreetMesh\Protocol\Laravel\Identity\Identities;
use StreetMesh\Protocol\Laravel\Identity\Identity;

/**
 * Giving somebody who lives here an address.
 *
 * The account is local and the address is not, and everything here is about
 * that difference. A domicile that hands out accounts without addresses has
 * residents who cannot leave the building — which is what this server did for
 * as long as it existed, while every local thing worked perfectly.
 */
class ResidentTest extends TestCase
{
    private function residents(): Residents
    {
        return $this->app->make(Residents::class);
    }

    private function user(string $email = 'alice@home.test'): Resident
    {
        return Resident::create([
            'name' => 'Alice',
            'email' => $email,
            'password' => 'irrelevant',
        ]);
    }

    private function serverExists(): void
    {
        $this->app->make(Identities::class)->forServer();
    }

    public function test_somebody_who_lives_here_is_given_a_name_under_this_servers_own(): void
    {
        $this->serverExists();

        $settled = $this->residents()->settle(
            $this->user(),
            Handle::for('alice', $this->residents()->host()),
        );

        $this->assertSame('alice.home.test', $settled['identity']->handle);
        $this->assertFalse($settled['identity']->is_server);
    }

    /**
     * The address has to be findable *from the account*, because that is the
     * direction every screen asks in: somebody is signed in, and we want to know
     * what they are called on the network.
     */
    public function test_the_address_can_be_found_from_the_account(): void
    {
        $this->serverExists();
        $user = $this->user();

        $this->residents()->settle($user, Handle::for('alice', $this->residents()->host()));

        $found = $this->app->make(Identities::class)->forUser($user);

        $this->assertNotNull($found);
        $this->assertSame('alice.home.test', $found->handle);
    }

    /**
     * Returned rather than kept. Holding the only copy of what lets somebody
     * move would make moving a favour this server grants.
     */
    public function test_what_lets_somebody_leave_is_handed_to_them_and_not_kept(): void
    {
        $this->serverExists();

        $settled = $this->residents()->settle(
            $this->user(),
            Handle::for('alice', $this->residents()->host()),
        );

        $this->assertNotNull($settled['rotationKey']);
        $this->assertNull($settled['identity']->fresh()->rotation_key);
    }

    public function test_a_name_somebody_already_has_is_not_free(): void
    {
        $this->serverExists();
        $handle = Handle::for('alice', $this->residents()->host());

        $this->assertFalse($this->residents()->taken($handle));

        $this->residents()->settle($this->user(), $handle);

        $this->assertTrue($this->residents()->taken($handle));
    }

    public function test_nobody_is_given_a_second_address(): void
    {
        $this->serverExists();
        $user = $this->user();

        $this->residents()->settle($user, Handle::for('alice', $this->residents()->host()));

        $this->expectException(RuntimeException::class);

        $this->residents()->settle($user, Handle::for('alice2', $this->residents()->host()));
    }

    /**
     * An account with no address cannot go anywhere, and an address with no
     * owner is a name nobody can sign in as and claim. Half of a resident is
     * worse than none, so a failure has to leave neither half behind.
     */
    public function test_a_failure_partway_through_leaves_nobody_half_settled(): void
    {
        $this->serverExists();
        $user = $this->user();
        $before = Identity::query()->count();

        $this->residents()->settle($user, Handle::for('alice', $this->residents()->host()));

        try {
            $this->residents()->settle($this->user('bob@home.test'), Handle::for('alice', $this->residents()->host()));
        } catch (RuntimeException) {
            // The name is taken, which is the failure being provoked.
        }

        $this->assertSame($before + 1, Identity::query()->count());
    }

    /**
     * A handle is a hostname. Everything rejected here is rejected because DNS
     * would not carry it, not because of house style — a name outside these
     * rules is one that resolves nowhere.
     */
    #[DataProvider('impossibleNames')]
    public function test_a_name_that_could_not_be_a_hostname_is_refused(string $label): void
    {
        $this->expectException(InvalidArgumentException::class);

        Handle::for($label, 'home.test');
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function impossibleNames(): array
    {
        return [
            'empty' => [''],
            'only spaces' => ['   '],
            'a dot, which would claim somebody else' => ['alice.bob'],
            'leading hyphen' => ['-alice'],
            'trailing hyphen' => ['alice-'],
            'underscore' => ['alice_smith'],
            'a space inside' => ['alice smith'],
            'too long' => [str_repeat('a', 64)],
            'kept for the server' => ['www'],
            'kept for the protocol' => ['xrpc'],
        ];
    }

    /**
     * The label is the resident's to choose and the host is not. Given one
     * string, somebody would eventually type a whole hostname into the box and
     * claim a name this server has no business issuing.
     */
    public function test_a_resident_cannot_choose_which_server_they_appear_to_live_on(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Handle::for('alice.somewhere-else.test', 'home.test');
    }

    public function test_a_name_is_the_same_name_however_it_was_typed(): void
    {
        $this->assertSame('alice.home.test', (string) Handle::for('  ALICE  ', 'home.test'));
    }
}
