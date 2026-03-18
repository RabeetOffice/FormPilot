<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DomainController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->workspace;
        $domains = Domain::whereHas('brand', fn($q) => $q->where('workspace_id', $workspace->id))
            ->with(['brand:id,name,color'])
            ->withCount('submissions')
            ->latest()
            ->paginate(15);

        return view('domains.index', compact('domains'));
    }

    public function create(Request $request)
    {
        $brands = Brand::where('workspace_id', $request->workspace->id)->get();
        return view('domains.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'domain' => 'required|string|max:255',
        ]);

        // Ensure brand belongs to workspace
        $brand = Brand::where('id', $validated['brand_id'])
            ->where('workspace_id', $request->workspace->id)
            ->firstOrFail();

        $domain = Domain::create([
            'brand_id' => $brand->id,
            'domain' => $validated['domain'],
            'api_key' => Str::random(64),
            'allowed_origins' => ['https://' . $validated['domain'], 'http://' . $validated['domain']],
        ]);

        // Auto-create a default form source
        $domain->formSources()->create([
            'name' => 'Default',
            'type' => 'direct_post',
            'is_active' => true,
        ]);

        return redirect()->route('domains.index')->with('success', 'Domain added successfully!');
    }

    public function regenerateKey(Request $request, Domain $domain)
    {
        $this->authorizeWorkspace($request, $domain);

        $domain->update(['api_key' => Str::random(64)]);

        return back()->with('success', 'API key regenerated!');
    }

    public function destroy(Request $request, Domain $domain)
    {
        $this->authorizeWorkspace($request, $domain);
        $domain->delete();

        return redirect()->route('domains.index')->with('success', 'Domain deleted.');
    }

    protected function authorizeWorkspace(Request $request, Domain $domain): void
    {
        if ($domain->brand->workspace_id !== $request->workspace->id) {
            abort(403);
        }
    }
}
