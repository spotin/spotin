<?php

namespace App\Support;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use League\HTMLToMarkdown\HtmlConverter;
use RuntimeException;

/**
 * The import of the users and spots of the previous Spot in® application, a NestJS one on PostgreSQL, from its tables
 * exported as JSON, with a mapping of its users to the organizations, which it did not have.
 *
 * Nothing is imported while one of the problems of `problems()` remains. The import can be run again: the users are
 * found by their UUID or email address, the organizations by their name and the spots by their UUID, which their printed
 * QR codes hold, and what already exists is left as it is.
 */
class LegacyImport
{
    /**
     * The role of the users left out of the import in the mapping, with their spots: with no organization.
     */
    private const SKIP = 'skip';

    /**
     * The role of the users left out of the import in the mapping, but not their spots: imported without a creator in
     * the organization of the line, their QR codes keep working.
     */
    private const SKIP_USER = 'skip-user';

    /**
     * The old users, by their lowercased email address.
     *
     * @var Collection<string, object>
     */
    private Collection $users;

    /**
     * The lowercased email addresses of the old users, all of them: two of them can be the same once lowercased.
     *
     * @var list<string>
     */
    private array $emails;

    /**
     * The old spots.
     *
     * @var Collection<int, object>
     */
    private Collection $spots;

    /**
     * The rows of the mapping: `email`, `organization` and `role`, in their order.
     *
     * @var Collection<int, array{email: string, organization: string, role: string, line: int}>
     */
    private Collection $mapping;

    /**
     * What the import created or found, by kind.
     *
     * @var array<string, int>
     */
    private array $counts = [];

    /**
     * What the import did, but should be checked after it.
     *
     * @var list<string>
     */
    private array $warnings = [];

    /**
     * Read the `users.json` and `spots.json` exports of the directory, and the mapping, a CSV file.
     *
     * @throws RuntimeException When a file is missing or unreadable
     */
    public function __construct(string $directory, string $mapping)
    {
        $users = $this->readJson($directory.'/users.json');

        $this->emails = array_map(fn (object $user) => Str::lower($user->email), $users);
        $this->users = collect($users)->keyBy(fn (object $user) => Str::lower($user->email));
        $this->spots = collect($this->readJson($directory.'/spots.json'));
        $this->mapping = $this->readMapping($mapping);
    }

    /**
     * What prevents the import: nothing is imported until it is fixed.
     *
     * @return list<string>
     */
    public function problems(): array
    {
        $problems = [];

        foreach ($this->duplicateEmails() as $email) {
            $problems[] = "Several old users have the email address {$email} once lowercased: only one account can have it.";
        }

        foreach ($this->mapping as $row) {
            if (! $this->users->has($row['email'])) {
                $problems[] = "Line {$row['line']} of the mapping: no old user has the email address {$row['email']}.";
            }

            if ($row['organization'] !== '' && $row['role'] !== self::SKIP_USER && OrganizationRole::tryFrom($row['role']) === null) {
                $problems[] = "Line {$row['line']} of the mapping: the role must be `responsible`, `member` or `skip-user`, not `{$row['role']}`.";
            }

            if ($row['organization'] !== '' && $row['role'] === self::SKIP) {
                $problems[] = "Line {$row['line']} of the mapping: a user skipped with their spots has no organization, leave it empty.";
            }

            if ($row['organization'] === '' && $row['role'] === self::SKIP_USER) {
                $problems[] = "Line {$row['line']} of the mapping: a user skipped without their spots needs the organization to import their spots in.";
            }
        }

        foreach ($this->mapping->duplicates(fn (array $row) => $row['email'].' '.$row['organization']) as $index => $_) {
            $row = $this->mapping[$index];
            $problems[] = "Line {$row['line']} of the mapping: {$row['email']} is already in {$row['organization']}.";
        }

        foreach ($this->users as $email => $user) {
            if (! $this->mapping->contains('email', $email)) {
                $problems[] = "The old user {$user->username} ({$email}) is not in the mapping.";
            } elseif ($this->isSkipped($email) && $this->mapping->where('email', $email)->count() > 1) {
                $problems[] = "The old user {$user->username} ({$email}) is skipped, but also on other lines of the mapping.";
            } elseif (! $this->isSkipped($email) && $this->organizationOf($email) === null && $this->spots->contains('user_id', $user->id)) {
                $problems[] = "The old user {$user->username} ({$email}) has spots, but no organization in the mapping to put them in.";
            }
        }

        $organizations = $this->mapping->where('organization', '!==', '')->groupBy('organization');

        foreach ($organizations as $name => $rows) {
            if (! $rows->contains('role', OrganizationRole::Responsible->value)) {
                $problems[] = "The organization {$name} has no responsible user in the mapping.";
            }
        }

        return $problems;
    }

    /**
     * Import the users, the organizations of the mapping with their members, then the spots, in one transaction. With
     * `$dryRun`, the transaction is rolled back: the counts and warnings tell what the import would do.
     *
     * @throws RuntimeException When a problem of `problems()` remains
     */
    public function run(bool $dryRun = false): void
    {
        if ($this->problems() !== []) {
            throw new RuntimeException('The legacy data cannot be imported until its problems are fixed.');
        }

        DB::beginTransaction();

        try {
            $users = $this->importUsers();
            $organizations = $this->importOrganizations($users);
            $this->importSpots($users, $organizations);
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        $dryRun ? DB::rollBack() : DB::commit();
    }

    /**
     * What the import created or found, by kind.
     *
     * @return array<string, int>
     */
    public function counts(): array
    {
        return $this->counts;
    }

    /**
     * What the import did, but should be checked after it.
     *
     * @return list<string>
     */
    public function warnings(): array
    {
        return $this->warnings;
    }

    /**
     * The users, found by their UUID, the ID of the old one, or by their email address, such as the administrator of the
     * production seeder; otherwise created with the password of the old one.
     *
     * The users are inserted without the model: its `hashed` cast refuses the old hashes, made with more threads than the
     * ones of this application. They are checked as they are, then hashed again with its options at the next login.
     *
     * @return array<string, User> The users, by the lowercased email address of the old ones
     */
    private function importUsers(): array
    {
        $users = [];

        foreach ($this->users as $email => $old) {
            if ($this->isSkipped($email)) {
                $this->count('users skipped');

                $spots = $this->spots->where('user_id', $old->id)->count();

                if ($spots > 0 && ! $this->keepsSpots($email)) {
                    $this->warnings[] = "{$email} was skipped with their {$spots} spot(s): their QR codes lead to a page not found.";
                }

                continue;
            }

            $user = User::where('uuid', $old->id)->first() ?? User::where('email', $email)->first();

            if ($user === null) {
                DB::table('users')->insert([
                    'uuid' => $old->id,
                    'name' => $old->username,
                    'email' => $email,
                    // The old application had no verification: its addresses were used as they were
                    'email_verified_at' => $this->date($old->created_at),
                    'password' => $old->password,
                    'is_admin' => $old->role === 'ADMIN',
                    'created_at' => $this->date($old->created_at),
                    'updated_at' => $this->date($old->updated_at),
                ]);

                $user = User::where('uuid', $old->id)->firstOrFail();
                $this->count('users created');

                if (mb_strlen($old->username) > 50) {
                    $this->warnings[] = "The name of {$email}, {$old->username}, is longer than the 50 characters of the profile form.";
                }
            } else {
                $this->count('users already there');

                if ($old->role === 'ADMIN' && ! $user->is_admin) {
                    $user->is_admin = true;
                    $user->save();
                }
            }

            if (! $old->enabled || $old->deleted_at !== null) {
                $this->warnings[] = "{$email} was ".($old->enabled ? 'deleted' : 'disabled').' in the old application, and can log in to this one.';
            }

            $users[$email] = $user;
        }

        return $users;
    }

    /**
     * The organizations of the mapping, found by their name or created, with their members.
     *
     * @param  array<string, User>  $users
     * @return array<string, Organization> The organizations, by their name
     */
    private function importOrganizations(array $users): array
    {
        $organizations = [];

        foreach ($this->mapping->where('organization', '!==', '') as $row) {
            $organization = $organizations[$row['organization']] ??= Organization::firstWhere('name', $row['organization'])
                ?? tap(Organization::create(['name' => $row['organization']]), fn () => $this->count('organizations created'));

            // Not a member: only their spots come in it
            if ($row['role'] === self::SKIP_USER) {
                continue;
            }

            $user = $users[$row['email']];

            if (! $user->belongsToOrganization($organization)) {
                $organization->users()->attach($user, ['role' => OrganizationRole::from($row['role'])]);
                $user->unsetRelation('organizations');
                $this->count('memberships created');
            }
        }

        return $organizations;
    }

    /**
     * The spots, with their UUID, which their QR codes hold, in the first organization of their user in the mapping.
     * Deleted ones too: their QR codes tell they are no longer in use.
     *
     * @param  array<string, User>  $users
     * @param  array<string, Organization>  $organizations
     */
    private function importSpots(array $users, array $organizations): void
    {
        $emails = $this->users->mapWithKeys(fn (object $user, string $email) => [$user->id => $email]);
        $planned = [];

        foreach ($this->spots as $old) {
            $email = $emails[$old->user_id];

            if ($this->isSkipped($email) && ! $this->keepsSpots($email)) {
                $this->count('spots skipped');

                continue;
            }

            if (Spot::withTrashed()->where('uuid', $old->id)->exists()) {
                $this->count('spots already there');

                continue;
            }

            $planned[] = [
                'old' => $old,
                'email' => $email,
                'organization' => $organizations[$this->organizationOf($email)],
                // Without the spaces around it, and an empty one as no name, as the forms save it: the spot is shown as unnamed
                'name' => blank($old->name) ? null : trim($old->name),
            ];
        }

        $names = $this->uniqueNames($planned);

        foreach ($planned as ['old' => $old, 'email' => $email, 'organization' => $organization]) {
            $spot = new Spot([
                'name' => $names[$old->id],
                'description' => $this->description($old),
                // To the 6 decimals stored, about 10 cm, from the double precision of the old ones. A single one is left out:
                // no map shows it, and the form refuses it
                'latitude' => $this->hasLocation($old) ? round($old->latitude, 6) : null,
                'longitude' => $this->hasLocation($old) ? round($old->longitude, 6) : null,
                'website' => $this->website($old),
                'redirect_to_website' => $old->direct_access_to_website_target,
                // Not public, the old spots were only left out of the map
                'is_hidden' => ! $old->public,
                'needs_setup' => ! $old->configured,
                'custom_data' => $this->customData($old),
            ]);

            $spot->uuid = $old->id;
            $spot->organization()->associate($organization);
            // Without a creator when their user is skipped: an orphan spot, managed by the members of its organization
            $spot->creator()->associate($users[$email] ?? null);
            // Printed already, as far as anyone knows: the first steps do not ask to download their QR codes
            $spot->qr_code_downloaded_at = $this->date($old->created_at);
            $spot->created_at = $this->date($old->created_at);
            $spot->updated_at = $this->date($old->updated_at);
            $spot->deleted_at = $old->deleted_at === null ? null : $this->date($old->deleted_at);
            $spot->timestamps = false;
            $spot->save();

            $this->count('spots created');
            $this->checkSpot($spot, $old);
        }
    }

    /**
     * The names of the spots to create, unique in their organization as the forms require: the ones in common, whatever
     * their case, get ` (1)`, ` (2)`… in the order the spots were created, deleted ones included, to be restored as they
     * are. A number already taken, by a spot of the organization or another name, is passed over. Their members rename
     * them to tell what they are.
     *
     * @param  list<array{old: object, email: string, organization: Organization, name: ?string}>  $planned
     * @return array<string, ?string> The names, by the UUID of the spots
     */
    private function uniqueNames(array $planned): array
    {
        $names = [];

        foreach (collect($planned)->groupBy(fn (array $spot) => $spot['organization']->getKey()) as $spots) {
            $organization = $spots->first()['organization'];

            // The names of the spots of the organization, deleted ones included, then the ones of the spots to create too
            $existing = Spot::withTrashed()->whereBelongsTo($organization)->whereNotNull('name')->pluck('name')
                ->map(fn (string $name) => mb_strtolower($name))
                ->all();
            $taken = array_fill_keys([...$existing, ...$spots->pluck('name')->filter()->map(fn (string $name) => mb_strtolower($name))], true);

            $groups = $spots->sortBy(fn (array $spot) => [$this->date($spot['old']->created_at)->getTimestampMs(), $spot['old']->id])
                ->groupBy(fn (array $spot) => $spot['name'] === null ? '' : mb_strtolower($spot['name']));

            foreach ($groups as $key => $group) {
                // Alone with its name, or unnamed: kept as it is
                if ($key === '' || ($group->count() === 1 && ! in_array($key, $existing, true))) {
                    $group->each(function (array $spot) use (&$names) {
                        $names[$spot['old']->id] = $spot['name'];
                    });

                    continue;
                }

                $number = 1;

                foreach ($group as $spot) {
                    while (isset($taken[mb_strtolower("{$spot['name']} ({$number})")])) {
                        $number++;
                    }

                    $names[$spot['old']->id] = "{$spot['name']} ({$number})";
                    $taken[mb_strtolower($names[$spot['old']->id])] = true;
                    $this->count('spots renamed');
                }

                $first = $group->first()['name'];
                $this->warnings[] = "{$group->count()} spots of {$organization->name} were named {$first}: they were numbered, up to {$names[$group->last()['old']->id]}.";
            }
        }

        return $names;
    }

    /**
     * The website of the old spot, with `https://` when it had no scheme, such as `www.example.com`: followed as it was,
     * from its QR code, it would lead to a page of this application.
     */
    private function website(object $old): ?string
    {
        $website = blank($old->website_target) ? null : trim($old->website_target);

        if ($website === null || preg_match('/^[a-z][a-z0-9+.-]*:\/\//i', $website)) {
            return $website;
        }

        $this->warnings[] = "The website of the spot {$old->id}, {$website}, had no scheme: https:// was added.";

        return "https://{$website}";
    }

    /**
     * The description of the old spot, in Markdown as the ones of this application: the ones written in HTML, through
     * the former API, are converted, as their HTML would be stripped, and they would be shown empty.
     */
    private function description(object $old): ?string
    {
        if (blank($old->description) || ! preg_match('/<\/?[a-z][a-z0-9]*\b[^>]*>/i', $old->description)) {
            return $old->description;
        }

        $this->count('descriptions converted from HTML');

        // `<br>` as a line break of Markdown, the tags without one dropped but not their text
        return trim((new HtmlConverter(['strip_tags' => true, 'remove_nodes' => 'script style']))->convert($old->description));
    }

    /**
     * The payload of the old spot as custom data: a JSON object, or nothing, as the custom data are.
     */
    private function customData(object $old): ?object
    {
        if ($old->payload === null || $old->payload instanceof \stdClass) {
            return $old->payload;
        }

        $this->warnings[] = "The payload of the spot {$old->id} is not a JSON object, and was left out: ".json_encode($old->payload);

        return null;
    }

    /**
     * Warn about what was changed for the forms to accept the spot once it is edited.
     */
    private function checkSpot(Spot $spot, object $old): void
    {
        $label = $spot->name === null ? "The spot {$spot->uuid}" : "The spot {$spot->name} ({$spot->uuid})";

        if ($spot->name === null && ! $spot->needs_setup && ! $spot->trashed()) {
            $this->warnings[] = "{$label} is set up, but has no name: it is shown as unnamed, and its form asks for one.";
        }

        if (! $this->hasLocation($old) && ($old->latitude !== null || $old->longitude !== null)) {
            $this->warnings[] = "{$label} had a ".($old->latitude !== null ? 'latitude without a longitude' : 'longitude without a latitude')
                .': it was imported without a location.';
        }
    }

    /**
     * Whether the old spot has a location: both coordinates, as the maps and the forms need.
     */
    private function hasLocation(object $old): bool
    {
        return $old->latitude !== null && $old->longitude !== null;
    }

    /**
     * The first organization of the user in the mapping, the one of their spots, if any.
     */
    private function organizationOf(string $email): ?string
    {
        return $this->mapping->where('email', $email)->firstWhere('organization', '!==', '')['organization'] ?? null;
    }

    /**
     * Whether the user is left out of the import, with the role `skip` or `skip-user` in the mapping.
     */
    private function isSkipped(string $email): bool
    {
        return $this->mapping->where('email', $email)->contains(fn (array $row) => in_array($row['role'], [self::SKIP, self::SKIP_USER], true));
    }

    /**
     * Whether the spots of a skipped user are imported anyway, with the role `skip-user` in the mapping.
     */
    private function keepsSpots(string $email): bool
    {
        return $this->mapping->where('email', $email)->contains('role', self::SKIP_USER);
    }

    /**
     * The email addresses that several old users have once lowercased.
     *
     * @return list<string>
     */
    private function duplicateEmails(): array
    {
        return array_keys(array_filter(array_count_values($this->emails), fn (int $count) => $count > 1));
    }

    /**
     * A date of the old application, with its time zone, in the one of this application.
     */
    private function date(string $value): Carbon
    {
        return Carbon::parse($value)->utc();
    }

    /**
     * Count one more of a kind, for the report.
     */
    private function count(string $kind): void
    {
        $this->counts[$kind] = ($this->counts[$kind] ?? 0) + 1;
    }

    /**
     * The rows of an exported table, with their JSON columns as objects.
     *
     * @return list<object>
     */
    private function readJson(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("The file {$path} is missing.");
        }

        $rows = json_decode((string) file_get_contents($path), flags: JSON_THROW_ON_ERROR);

        if (! is_array($rows)) {
            throw new RuntimeException("The file {$path} is not a JSON array of rows.");
        }

        return $rows;
    }

    /**
     * The rows of the mapping, after its header, with their line in the file.
     *
     * @return Collection<int, array{email: string, organization: string, role: string, line: int}>
     */
    private function readMapping(string $path): Collection
    {
        if (! is_file($path)) {
            throw new RuntimeException("The file {$path} is missing.");
        }

        // Without the blank lines and the comments, starting with `#`, to split it into parts; with their line in the file
        $lines = collect(file($path, FILE_IGNORE_NEW_LINES) ?: [])
            ->mapWithKeys(fn (string $line, int $index) => [$index + 1 => trim($line)])
            ->reject(fn (string $line) => $line === '' || str_starts_with($line, '#'))
            ->map(fn (string $line) => str_getcsv($line));

        if (array_map(trim(...), $lines->first() ?? []) !== ['email', 'organization', 'role']) {
            throw new RuntimeException("The mapping {$path} must start with the header `email,organization,role`.");
        }

        return $lines->skip(1)->map(fn (array $row, int $line) => [
            'email' => Str::lower(trim($row[0] ?? '')),
            'organization' => trim($row[1] ?? ''),
            'role' => Str::lower(trim($row[2] ?? '')),
            'line' => $line,
        ])->values();
    }
}
