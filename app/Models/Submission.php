<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    protected $fillable = [
        'workspace_id', 'brand_id', 'domain_id', 'form_source_id',
        'full_name', 'first_name', 'last_name', 'email', 'phone',
        'company', 'subject', 'message', 'budget',
        'page_url', 'source_url', 'referrer',
        'utm_source', 'utm_medium', 'utm_campaign',
        'raw_payload', 'normalized_payload',
        'ip_address', 'user_agent', 'country',
        'status', 'spam_score', 'is_spam', 'honeypot_triggered', 'notes',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'normalized_payload' => 'array',
        'spam_score' => 'decimal:2',
        'is_spam' => 'boolean',
        'honeypot_triggered' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function formSource(): BelongsTo
    {
        return $this->belongsTo(FormSource::class);
    }

    public function aiClassification(): HasOne
    {
        return $this->hasOne(AiClassification::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function currentAssignment(): HasOne
    {
        return $this->hasOne(Assignment::class)->where('status', 'active')->latestOfMany();
    }

    public function getDisplayName(): string
    {
        return $this->full_name ?? trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')) ?: 'Unknown';
    }

    public function getLeadTemperature(): ?string
    {
        return $this->aiClassification?->lead_temperature;
    }

    public function getServiceType(): ?string
    {
        return $this->aiClassification?->service_type;
    }
}
