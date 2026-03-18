<?php

namespace App\Http\Controllers;

use App\Models\FormSource;
use App\Models\Domain;
use Illuminate\Http\Request;

class FormSourceController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->workspace;
        $formSources = FormSource::whereHas('domain.brand', fn($q) => $q->where('workspace_id', $workspace->id))
            ->with(['domain.brand:id,name,color', 'domain:id,domain,brand_id'])
            ->withCount('submissions')
            ->latest()
            ->paginate(15);

        return view('formsources.index', compact('formSources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_id' => 'required|exists:domains,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:js_snippet,direct_post,webhook',
        ]);

        // Verify domain belongs to workspace
        $domain = Domain::whereHas('brand', fn($q) => $q->where('workspace_id', $request->workspace->id))
            ->findOrFail($validated['domain_id']);

        FormSource::create($validated);

        return back()->with('success', 'Form source created!');
    }

    public function destroy(Request $request, FormSource $formSource)
    {
        if ($formSource->domain->brand->workspace_id !== $request->workspace->id) {
            abort(403);
        }

        $formSource->delete();
        return back()->with('success', 'Form source deleted.');
    }
}
