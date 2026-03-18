<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->workspace;

        $totalSubmissions = Submission::where('workspace_id', $workspace->id)->count();
        $submissionsToday = Submission::where('workspace_id', $workspace->id)
            ->whereDate('created_at', today())->count();
        $hotLeads = Submission::where('workspace_id', $workspace->id)
            ->whereHas('aiClassification', fn($q) => $q->where('lead_temperature', 'hot'))
            ->count();
        $spamBlocked = Submission::where('workspace_id', $workspace->id)
            ->where('is_spam', true)->count();

        $leadsByBrand = Submission::where('workspace_id', $workspace->id)
            ->where('is_spam', false)
            ->selectRaw('brand_id, count(*) as count')
            ->groupBy('brand_id')
            ->with('brand:id,name,color')
            ->get();

        $leadsByServiceType = Submission::where('workspace_id', $workspace->id)
            ->where('is_spam', false)
            ->whereHas('aiClassification')
            ->with('aiClassification:id,submission_id,service_type')
            ->get()
            ->groupBy(fn($s) => $s->aiClassification?->service_type ?? 'unknown')
            ->map->count();

        $leadsByRep = Submission::where('workspace_id', $workspace->id)
            ->where('is_spam', false)
            ->whereHas('currentAssignment')
            ->with('currentAssignment.assignee:id,name')
            ->get()
            ->groupBy(fn($s) => $s->currentAssignment?->assignee?->name ?? 'Unassigned')
            ->map->count();

        $recentSubmissions = Submission::where('workspace_id', $workspace->id)
            ->where('is_spam', false)
            ->with(['brand:id,name,color', 'domain:id,domain', 'aiClassification', 'currentAssignment.assignee:id,name'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index', compact(
            'totalSubmissions', 'submissionsToday', 'hotLeads', 'spamBlocked',
            'leadsByBrand', 'leadsByServiceType', 'leadsByRep', 'recentSubmissions'
        ));
    }
}
