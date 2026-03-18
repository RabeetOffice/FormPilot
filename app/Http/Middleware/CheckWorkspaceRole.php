<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckWorkspaceRole
{
    protected array $roleHierarchy = [
        'owner' => 4,
        'admin' => 3,
        'sales_rep' => 2,
        'viewer' => 1,
    ];

    public function handle(Request $request, Closure $next, string $minimumRole = 'viewer'): Response
    {
        $user = $request->user();
        $workspace = $request->workspace ?? session('current_workspace_id');

        if (!$user || !$workspace) {
            abort(403, 'Unauthorized');
        }

        $workspaceModel = is_object($workspace) ? $workspace : \App\Models\Workspace::find($workspace);

        if (!$workspaceModel || !$user->hasRoleIn($workspaceModel, $minimumRole)) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
