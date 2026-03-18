<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Domain extends Model
{
    protected $fillable = [
        'brand_id', 'domain', 'api_key', 'allowed_origins', 'is_active',
    ];

    protected $casts = [
        'allowed_origins' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($domain) {
            if (empty($domain->api_key)) {
                $domain->api_key = Str::random(64);
            }
        });
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function formSources(): HasMany
    {
        return $this->hasMany(FormSource::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
