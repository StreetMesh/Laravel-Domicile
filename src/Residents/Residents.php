<?php

namespace StreetMesh\Domicile\Residents;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use StreetMesh\Protocol\Laravel\Identity\Identities;
use StreetMesh\Protocol\Laravel\Identity\Identity;
use StreetMesh\Protocol\SigningKey;

/**
 * Giving somebody who lives here an address.
 *
 * An account and an address are different things, and this is what joins them.
 * The account is local — a password, a session, a way back in. The address is
 * not: it is a name other servers resolve, a key they check signatures against,
 * and the only thing that makes this person able to go anywhere on the network.
 *
 * A domicile that hands out accounts without addresses has residents who cannot
 * leave the building. That is what this server did until now, and nothing
 * reported it, because everything local worked.
 */
final readonly class Residents
{
    public function __construct(private Identities $identities) {}

    /**
     * Settle somebody at a name, and hand back what lets them leave.
     *
     * The rotation key is returned and deliberately not kept. Holding the only
     * copy of what lets somebody move would make moving a favour this server
     * grants — so whether it is stored, and where, is the caller's decision to
     * make in the open rather than one made quietly here.
     *
     * @return array{identity: Identity, rotationKey: SigningKey}
     */
    public function settle(Model $user, Handle $handle): array
    {
        if ($this->identities->forUser($user) !== null) {
            throw new RuntimeException('Somebody with an address cannot be given a second one.');
        }

        /*
         * One transaction, because a resident is an account *and* an identity.
         * Either half alone is a person who cannot be dealt with: an account
         * with no address cannot go anywhere, and an address with no owner is a
         * name nobody can sign in as and claim.
         */
        return DB::transaction(function () use ($user, $handle): array {
            $settled = $this->identities->forResident((string) $handle);

            $settled['identity']->owner()->associate($user)->save();

            return $settled;
        });
    }

    /**
     * Everybody who lives here.
     *
     * Ordered by name because that is what somebody is scanning for. The
     * server's own identity is not a resident and never appears: it holds no
     * records of its own and belongs to nobody.
     *
     * @return Collection<int, Identity>
     */
    public function all(string $matching = ''): Collection
    {
        $matching = trim($matching);

        return Identity::query()
            ->where('is_server', false)
            ->when(
                $matching !== '',
                fn (Builder $query) => $query->where('handle', 'like', '%'.$matching.'%'),
            )
            ->orderBy('handle')
            ->get();
    }

    /**
     * Whether a name is free to be taken.
     *
     * Checked here as well as by the database, because the useful answer at a
     * sign-up form is a sentence rather than an integrity violation.
     */
    public function taken(Handle $handle): bool
    {
        return $this->identities->byHandle((string) $handle) !== null;
    }

    /**
     * The host every resident's name sits under — this server's own.
     *
     * Taken from the server's identity rather than from configuration, because
     * it has to match the name the server answers to. If the two disagreed,
     * residents would be issued names this server does not serve.
     */
    public function host(): string
    {
        return $this->identities->forServer()->handle
            ?? throw new RuntimeException('This server has no name of its own, so it cannot give anybody an address under it.');
    }
}
