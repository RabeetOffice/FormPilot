<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    public function create()
    {
        return view('workspaces.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $workspace = Workspace::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'settings' => [
                'app_name' => $validated['name'],
                'primary_color' => '#4F46E5',
            ],
        ]);

        // Attach user as owner
        $request->user()->workspaces()->attach($workspace->id, [
            'role' => 'owner',
            'accepted_at' => now(),
        ]);

        // Set as current workspace
        $request->user()->update(['current_workspace_id' => $workspace->id]);
        session(['current_workspace_id' => $workspace->id]);

        return redirect()->route('dashboard')->with('success', 'Workspace created successfully!');
    }

    public function switchWorkspace(Request $request, Workspace $workspace)
    {
        $user = $request->user();

        if (!$user->workspaces()->where('workspace_id', $workspace->id)->exists()) {
            abort(403);
        }

        $user->update(['current_workspace_id' => $workspace->id]);
        session(['current_workspace_id' => $workspace->id]);

        return redirect()->route('dashboard')->with('success', "Switched to {$workspace->name}");
    }
}
