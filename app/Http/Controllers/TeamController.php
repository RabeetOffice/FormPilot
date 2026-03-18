<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->workspace;
        $members = $workspace->users()->withPivot('role', 'accepted_at', 'invited_at')->get();

        return view('team.index', compact('members', 'workspace'));
    }

    public function invite(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin,sales_rep,viewer',
        ]);

        $workspace = $request->workspace;

        // Check if already a member
        if ($workspace->users()->where('email', $validated['email'])->exists()) {
            return back()->with('error', 'This user is already a member of this workspace.');
        }

        // Check if user exists
        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            $workspace->users()->attach($user->id, [
                'role' => $validated['role'],
                'invited_at' => now(),
                'accepted_at' => now(), // Auto-accept for existing users in MVP
                'invitation_token' => Str::random(32),
            ]);
        } else {
            // Create a placeholder — user will be linked on registration
            // For MVP, we just show a message
            return back()->with('error', 'User not found. They need to register first.');
        }

        return back()->with('success', 'Team member invited successfully!');
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:owner,admin,sales_rep,viewer',
        ]);

        $workspace = $request->workspace;

        $workspace->users()->updateExistingPivot($user->id, [
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'Role updated successfully!');
    }

    public function remove(Request $request, User $user)
    {
        $workspace = $request->workspace;

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot remove yourself from the workspace.');
        }

        $workspace->users()->detach($user->id);

        return back()->with('success', 'Team member removed.');
    }
}
