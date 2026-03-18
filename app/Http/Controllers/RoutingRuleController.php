<?php

namespace App\Http\Controllers;

use App\Models\RoutingRule;
use Illuminate\Http\Request;

class RoutingRuleController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->workspace;
        $rules = RoutingRule::where('workspace_id', $workspace->id)
            ->with('targetUser:id,name')
            ->orderBy('priority')
            ->get();

        $teamMembers = $workspace->users()->get(['users.id', 'name']);
        $brands = $workspace->brands()->get(['id', 'name']);

        return view('routing.index', compact('rules', 'teamMembers', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:service_type,brand,budget,spam_score,country,fallback',
            'conditions' => 'nullable|array',
            'conditions.value' => 'nullable|string',
            'conditions.operator' => 'nullable|string',
            'target_user_id' => 'required|exists:users,id',
            'priority' => 'integer|min:0',
        ]);

        RoutingRule::create([
            'workspace_id' => $request->workspace->id,
            ...$validated,
        ]);

        return back()->with('success', 'Routing rule created!');
    }

    public function update(Request $request, RoutingRule $routingRule)
    {
        if ($routingRule->workspace_id !== $request->workspace->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:service_type,brand,budget,spam_score,country,fallback',
            'conditions' => 'nullable|array',
            'target_user_id' => 'required|exists:users,id',
            'priority' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $routingRule->update($validated);

        return back()->with('success', 'Routing rule updated!');
    }

    public function destroy(Request $request, RoutingRule $routingRule)
    {
        if ($routingRule->workspace_id !== $request->workspace->id) {
            abort(403);
        }

        $routingRule->delete();

        return back()->with('success', 'Routing rule deleted.');
    }
}
