<?php

namespace Database\Seeders;

use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The number of verified users.
     */
    private const VERIFIED_USERS = 145;

    /**
     * The number of unverified users.
     */
    private const UNVERIFIED_USERS = 5;

    /**
     * The number of organizations.
     */
    private const ORGANIZATIONS = 60;

    /**
     * The number of organizations the named responsible and member users belong to.
     */
    private const NAMED_USER_ORGANIZATIONS = 30;

    /**
     * The number of organizations with enough invitations to span several pages.
     */
    private const LARGE_INVITATION_ORGANIZATIONS = 3;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([ProductionSeeder::class]);

        // Seed the faker generator with a fixed seed to ensure consistent results across runs
        fake()->seed(2);

        // Create a named organization
        $organization = Organization::factory()->create(['name' => 'Organization']);

        // Create a named responsible user and a named member user
        $responsible = User::factory()->create([
            'name' => 'Responsible User',
            'email' => 'responsible@example.com',
        ]);

        $member = User::factory()->create([
            'name' => 'Member User',
            'email' => 'member@example.com',
        ]);

        // Assign the named users to the named organization
        $organization->users()->attach($responsible, ['role' => OrganizationRole::Responsible]);
        $organization->users()->attach($member, ['role' => OrganizationRole::Member]);

        // Seed the rest of the users
        $users = $this->seedUsers();

        // Seed the organizations
        $organizations = $this->seedOrganizations($users);

        // Assign the named users to some of the organizations
        $this->assignNamedUsersToOrganizations($organizations, $responsible, $member);

        // Seed invitations for some of the organizations
        $this->seedInvitations($organizations);

        // Seed spots, created by members of their organization
        $this->seedSpots($organizations);

        // Seed named spots, linked to the named users and organization
        $this->seedNamedSpots($organization, $responsible, $member);
    }

    /**
     * Spots with a known name for testing: created by each kind of user, by an administrator who
     * does not belong to the organization, by nobody anymore, and in another organization.
     */
    private function seedNamedSpots(Organization $organization, User $responsible, User $member): void
    {
        $admin = User::where('is_admin', true)->first();
        $other = $member->organizations()->whereKeyNot($organization->getKey())->orderBy('organizations.id')->first();

        $spots = [
            ['Responsible Spot', $organization, $responsible],
            ['Member Spot', $organization, $member],
            ['Admin Spot', $organization, $admin],
            ['Orphan Spot', $organization, null],
            ['Other Organization Spot', $other, $member],
        ];

        foreach ($spots as [$name, $spotOrganization, $creator]) {
            if ($spotOrganization === null) {
                continue;
            }

            Spot::factory()->for($spotOrganization)->create(['name' => $name, 'created_by' => $creator?->id]);
        }
    }

    /**
     * Pick random items with Faker, so the choice follows its seed (unlike Collection::random()).
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  Collection<int, TModel>  $items
     * @return Collection<int, TModel>
     */
    private function pick(Collection $items, int $count): Collection
    {
        return new Collection(fake()->randomElements($items->all(), min($count, $items->count())));
    }

    /**
     * The people the organizations are filled with.
     *
     * @return Collection<int, User>
     */
    private function seedUsers(): Collection
    {
        return User::factory()->count(self::VERIFIED_USERS)->create()
            ->concat(User::factory()->unverified()->count(self::UNVERIFIED_USERS)->create());
    }

    /**
     * Many organizations of different sizes, with one or two responsible users each.
     *
     * @param  Collection<int, User>  $people
     * @return Collection<int, Organization>
     */
    private function seedOrganizations(Collection $people): Collection
    {
        $organizations = Organization::factory()
            ->count(self::ORGANIZATIONS)
            ->sequence(fn () => ['name' => fake()->unique()->company()])
            ->create();

        foreach ($organizations as $organization) {
            $this->attachMembers($organization, $this->pick($people, fake()->numberBetween(0, 60)), responsibles: fake()->numberBetween(1, 2));
        }

        return $organizations;
    }

    /**
     * Pending invitations, some of them expired, in about a third of the organizations.
     *
     * @param  Collection<int, Organization>  $organizations
     */
    private function seedInvitations(Collection $organizations): void
    {
        foreach ($this->pick($organizations, (int) ($organizations->count() / 3)) as $index => $organization) {
            // The first organizations get enough invitations to span several pages.
            $count = $index < self::LARGE_INVITATION_ORGANIZATIONS ? fake()->numberBetween(26, 40) : fake()->numberBetween(1, 6);

            $this->inviteSomeone($organization, $count, expired: fake()->numberBetween(0, (int) ($count / 4)));
        }
    }

    /**
     * Make the named responsible and member users belong to the organizations.
     *
     * @param  Collection<int, Organization>  $organizations
     */
    private function assignNamedUsersToOrganizations(Collection $organizations, User $responsible, User $member): void
    {
        // Leave the empty organizations empty, otherwise they would end up without a responsible user.
        $organizations = $organizations->filter(fn (Organization $organization) => $organization->users()->exists());

        // Add responsible in one organization out of five, a plain member in the others.
        foreach ($this->pick($organizations, self::NAMED_USER_ORGANIZATIONS) as $index => $organization) {
            $organization->users()->syncWithoutDetaching([
                $responsible->id => ['role' => $index % 5 === 0 ? OrganizationRole::Responsible : OrganizationRole::Member],
            ]);
        }

        // Add member in one organization out of seven, a plain member in the others.
        foreach ($this->pick($organizations, self::NAMED_USER_ORGANIZATIONS) as $index => $organization) {
            $organization->users()->syncWithoutDetaching([
                $member->id => ['role' => $index % 7 === 0 ? OrganizationRole::Responsible : OrganizationRole::Member],
            ]);
        }
    }

    /**
     * Add users to an organization, the first ones as responsible users.
     *
     * @param  Collection<int, User>  $users
     */
    private function attachMembers(Organization $organization, Collection $users, int $responsibles): void
    {
        $organization->users()->attach(
            $users->values()->mapWithKeys(fn (User $user, int $index) => [
                $user->id => ['role' => $index < $responsibles ? OrganizationRole::Responsible : OrganizationRole::Member],
            ])->all(),
        );
    }

    /**
     * Create pending invitations sent by a responsible user of the organization.
     */
    private function inviteSomeone(Organization $organization, int $count, int $expired = 0): void
    {
        $inviter = $this->pick($organization->responsibles()->get(), 1)->first();

        $roles = fn () => ['role' => fake()->boolean(15) ? OrganizationRole::Responsible : OrganizationRole::Member];

        Invitation::factory()->for($organization)->count($count - $expired)
            ->sequence($roles)
            ->create(['invited_by' => $inviter?->id]);

        if ($expired > 0) {
            Invitation::factory()->for($organization)->expired()->count($expired)
                ->sequence($roles)
                ->create(['invited_by' => $inviter?->id]);
        }
    }

    /**
     * A few spots in each organization that has members, created by one of them.
     *
     * @param  Collection<int, Organization>  $organizations
     */
    private function seedSpots(Collection $organizations): void
    {
        foreach ($organizations as $organization) {
            $members = $organization->users()->get();

            if ($members->isEmpty()) {
                continue;
            }

            foreach (range(1, fake()->numberBetween(0, 5)) as $ignored) {
                Spot::factory()->for($organization)->create([
                    'created_by' => $this->pick($members, 1)->first()->id,
                ]);
            }
        }
    }
}
