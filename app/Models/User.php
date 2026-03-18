<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'current_workspace_id', 'phone', 'avatar', 'timezone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_user')
            ->withPivot('role', 'invited_at', 'accepted_at', 'invitation_token')
            ->withTimestamps();
    }

    public function currentWorkspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'current_workspace_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'assigned_to');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    // Get the user's role in a specific workspace
    public function roleIn(Workspace $workspace): ?string
    {
        $pivot = $this->workspaces()->where('workspace_id', $workspace->id)->first();
        return $pivot?->pivot?->role;
    }

    // Check if user has at least the given role level in workspace
    public function hasRoleIn(Workspace $workspace, string $minimumRole): bool
    {
        $roleHierarchy = ['owner' => 4, 'admin' => 3, 'sales_rep' => 2, 'viewer' => 1];
        $userRole = $this->roleIn($workspace);
        if (!$userRole) return false;
        return ($roleHierarchy[$userRole] ?? 0) >= ($roleHierarchy[$minimumRole] ?? 0);
    }
}
