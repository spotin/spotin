<?php

namespace App\Models;

use App\Enums\OrganizationRole;
use Database\Factories\OrganizationInvitationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['email', 'role', 'token', 'invited_by', 'expires_at'])]
#[Hidden(['token'])]
class OrganizationInvitation extends Model
{
    /**
     * The session key holding the token of the invitation a visitor is following.
     */
    public const SESSION_KEY = 'invitation-token';

    /** @use HasFactory<OrganizationInvitationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => OrganizationRole::class,
            'expires_at' => 'datetime',
        ];
    }

    /**
     * The invited email address, always stored in lowercase.
     *
     * @return Attribute<string, string>
     */
    protected function email(): Attribute
    {
        return Attribute::set(fn (string $value) => Str::lower($value));
    }

    /**
     * Hash a plain token the way it is stored in the database.
     */
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Generate a new plain token to send by email.
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Find the active invitation matching a plain token, if any.
     */
    public static function findActiveByToken(string $token): ?static
    {
        return static::active()->where('token', static::hashToken($token))->first();
    }

    /**
     * Find the active invitation a visitor started following, if any.
     */
    public static function findInSession(): ?static
    {
        $token = session(self::SESSION_KEY);

        return is_string($token) ? static::findActiveByToken($token) : null;
    }

    /**
     * Scope the query to the invitations that have not expired.
     *
     * @param  Builder<static>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('expires_at', '>', now());
    }

    /**
     * Determine whether the invitation has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Determine whether the invitation was sent to the user's verified email address.
     */
    public function isFor(User $user): bool
    {
        return $user->hasVerifiedEmail() && Str::lower($user->email) === Str::lower($this->email);
    }

    /**
     * The organization the invitation is for.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * The user that sent the invitation.
     *
     * @return BelongsTo<User, $this>
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
