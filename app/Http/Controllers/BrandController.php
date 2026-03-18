<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->workspace;
        $brands = Brand::where('workspace_id', $workspace->id)
            ->withCount(['domains', 'submissions'])
            ->latest()
            ->paginate(12);

        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
        ]);

        Brand::create([
            'workspace_id' => $request->workspace->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? '#4F46E5',
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand created successfully!');
    }

    public function show(Request $request, Brand $brand)
    {
        $this->authorizeWorkspace($request, $brand);

        $brand->load(['domains' => function ($q) {
            $q->withCount('submissions');
        }]);

        $recentSubmissions = $brand->submissions()
            ->with(['domain:id,domain', 'aiClassification'])
            ->latest()
            ->take(10)
            ->get();

        return view('brands.show', compact('brand', 'recentSubmissions'));
    }

    public function edit(Request $request, Brand $brand)
    {
        $this->authorizeWorkspace($request, $brand);
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $this->authorizeWorkspace($request, $brand);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        $brand->update($validated);

        return redirect()->route('brands.show', $brand)->with('success', 'Brand updated!');
    }

    public function destroy(Request $request, Brand $brand)
    {
        $this->authorizeWorkspace($request, $brand);
        $brand->delete();

        return redirect()->route('brands.index')->with('success', 'Brand deleted.');
    }

    protected function authorizeWorkspace(Request $request, Brand $brand): void
    {
        if ($brand->workspace_id !== $request->workspace->id) {
            abort(403);
        }
    }
}
