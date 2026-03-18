<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Workspace extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($workspace) {
            if (empty($workspace->slug)) {
                $workspace->slug = Str::slug($workspace->name);
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workspace_user')
            ->withPivot('role', 'invited_at', 'accepted_at', 'invitation_token')
            ->withTimestamps();
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function routingRules(): HasMany
    {
        return $this->hasMany(RoutingRule::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // White-label helpers
    public function getAppName(): string
    {
        return $this->settings['app_name'] ?? config('app.name');
    }

    public function getLogo(): ?string
    {
        return $this->settings['logo'] ?? null;
    }

    public function getPrimaryColor(): string
    {
        return $this->settings['primary_color'] ?? '#4F46E5';
    }

    public function getEmailSenderName(): string
    {
        return $this->settings['email_sender_name'] ?? config('app.name');
    }
}
