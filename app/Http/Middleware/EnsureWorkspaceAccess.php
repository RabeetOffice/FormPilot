<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Get current workspace from session or user's default
        $workspaceId = session('current_workspace_id') ?? $user->current_workspace_id;

        if (!$workspaceId) {
            // No workspace — redirect to onboarding
            return redirect()->route('workspaces.create');
        }

        // Verify user belongs to this workspace
        $workspace = $user->workspaces()->find($workspaceId);

        if (!$workspace) {
            // Try user's first workspace
            $workspace = $user->workspaces()->first();
            if (!$workspace) {
                return redirect()->route('workspaces.create');
            }
            $workspaceId = $workspace->id;
        }

        // Set workspace context
        session(['current_workspace_id' => $workspaceId]);
        $request->merge(['workspace' => $workspace]);

        // Determine user role and share with views
        $userRole = $user->roleIn($workspace);
        $roleHierarchy = ['owner' => 4, 'admin' => 3, 'sales_rep' => 2, 'viewer' => 1];
        $userRoleLevel = $roleHierarchy[$userRole] ?? 0;

        $request->attributes->set('userRole', $userRole);

        view()->share('currentWorkspace', $workspace);
        view()->share('userRole', $userRole);
        view()->share('isOwner', $userRoleLevel >= 4);
        view()->share('isAdmin', $userRoleLevel >= 3);
        view()->share('canManageSubmissions', $userRoleLevel >= 2); // sales_rep+

        return $next($request);
    }
}
