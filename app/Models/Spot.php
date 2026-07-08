<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Spot extends Model
{
    /** @use HasFactory<\Database\Factories\SpotFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'description',
        'latitude', 'longitude',
        'website_target', 'direct_access_to_website_target',
        'configured', 'public', 'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'direct_access_to_website_target' => 'boolean',
            'configured' => 'boolean',
            'public' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
  }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
