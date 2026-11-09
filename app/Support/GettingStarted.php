<?php

namespace App\Support;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * The checklist of the first steps on the dashboard, as the home page tells how Spot in® works: join an organization,
 * create a spot, download its QR code to print it and, for the responsible users, invite a colleague. Each step is done
 * from what the user did, not ticked by hand. It stays until the user hides it.
 */
final readonly class GettingStarted
{
    /**
     * @param  list<array{key: string, done: bool, url: ?string}>  $steps
     */
    public function __construct(
        public array $steps,
        public bool $enabled,
    ) {}

    /**
     * Build the checklist of the user.
     */
    public static function for(User $user): self
    {
        $organization = $user->organizations()->orderBy('organizations.name')->first();
        $latestSpot = Spot::mine($user)->latest('id')->first();

        $steps = [
            [
                'key' => 'organization',
                'done' => $organization !== null,
                'url' => $organization ? route('organizations.show', ['organization' => $organization->uuid]) : null,
            ],
            [
                'key' => 'spot',
                'done' => $latestSpot !== null,
                'url' => $organization ? route('spots.create') : null,
            ],
            [
                'key' => 'qr_code',
                'done' => Spot::mine($user)->whereNotNull('qr_code_downloaded_at')->exists(),
                'url' => $latestSpot ? route('spots.show', ['spot' => $latestSpot->uuid]) : null,
            ],
        ];

        // Only the responsible users invite: to the first organization they are responsible for
        $responsibleOf = fn () => $user->organizations()->wherePivot('role', OrganizationRole::Responsible->value);
        $invitingTo = $responsibleOf()->orderBy('organizations.name')->first();

        if ($invitingTo) {
            $steps[] = [
                'key' => 'invite',
                // Someone else joined, or is invited to, one of their organizations
                'done' => Organization::whereIn('id', $responsibleOf()->select('organizations.id'))
                    ->where(fn (Builder $query) => $query
                        ->has('users', '>', 1)
                        ->orWhereHas('invitations', fn (Builder $query) => $query->active()))
                    ->exists(),
                'url' => route('organizations.invitations.create', ['organization' => $invitingTo->uuid]),
            ];
        }

        // The administrators never get it: its steps would lead them to create spots and invitations only to tick them
        return new self($steps, enabled: ! $user->isAdmin() && $user->show_getting_started);
    }

    /**
     * The number of steps done.
     */
    public function completed(): int
    {
        return count(array_filter($this->steps, fn (array $step) => $step['done']));
    }

    /**
     * The number of steps.
     */
    public function total(): int
    {
        return count($this->steps);
    }

    /**
     * Whether every step is done.
     */
    public function isCompleted(): bool
    {
        return $this->completed() === $this->total();
    }

    /**
     * Whether to show the checklist: until the user hides it, even once done, so they see they finished.
     */
    public function isVisible(): bool
    {
        return $this->enabled;
    }
}
