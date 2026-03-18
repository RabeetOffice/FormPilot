<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->workspace;

        $query = Submission::where('workspace_id', $workspace->id)
            ->with(['brand:id,name,color', 'domain:id,domain', 'aiClassification', 'currentAssignment.assignee:id,name']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_spam')) {
            $query->where('is_spam', $request->is_spam === 'true');
        } else {
            $query->where('is_spam', false); // Default: hide spam
        }

        if ($request->filled('lead_temperature')) {
            $query->whereHas('aiClassification', fn($q) =>
                $q->where('lead_temperature', $request->lead_temperature)
            );
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $submissions = $query->latest()->paginate(20)->withQueryString();

        $brands = $workspace->brands()->get(['id', 'name']);

        return view('submissions.index', compact('submissions', 'brands'));
    }

    public function show(Request $request, Submission $submission)
    {
        if ($submission->workspace_id !== $request->workspace->id) {
            abort(403);
        }

        $submission->load([
            'brand:id,name,color',
            'domain:id,domain',
            'aiClassification',
            'assignments.assignee:id,name',
            'assignments.assigner:id,name',
            'formSource:id,name,type',
        ]);

        $teamMembers = $request->workspace->users()->get(['users.id', 'name']);

        return view('submissions.show', compact('submission', 'teamMembers'));
    }

    public function updateStatus(Request $request, Submission $submission)
    {
        if ($submission->workspace_id !== $request->workspace->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:new,open,in_progress,closed,archived',
        ]);

        $submission->update($validated);

        return back()->with('success', 'Status updated.');
    }

    public function updateNotes(Request $request, Submission $submission)
    {
        if ($submission->workspace_id !== $request->workspace->id) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:5000',
        ]);

        $submission->update($validated);

        return back()->with('success', 'Notes updated.');
    }

    public function reassign(Request $request, Submission $submission)
    {
        if ($submission->workspace_id !== $request->workspace->id) {
            abort(403);
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        // Mark old assignments as reassigned
        Assignment::where('submission_id', $submission->id)
            ->where('status', 'active')
            ->update(['status' => 'reassigned']);

        Assignment::create([
            'submission_id' => $submission->id,
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => $request->user()->id,
            'reason' => 'Manual reassignment',
            'status' => 'active',
            'assigned_at' => now(),
        ]);

        return back()->with('success', 'Submission reassigned.');
    }
}
