@extends('layouts.app')
@section('title', $brand->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('brands.index') }}" class="text-sm font-medium hover:underline" style="color:var(--primary)">← Back to Brands</a>
</div>

<div class="flex items-start justify-between mb-6">
    <div class="flex items-center gap-4">
        <div class="h-14 w-14 rounded-xl flex items-center justify-center text-white text-lg font-bold" style="background: {{ $brand->color ?? '#6B7280' }}">
            {{ strtoupper(substr($brand->name, 0, 2)) }}
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $brand->name }}</h2>
            <p class="text-sm text-gray-500">{{ $brand->description ?? 'No description' }}</p>
        </div>
    </div>
    @if($isAdmin ?? false)
    <a href="{{ route('brands.edit', $brand) }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Edit Brand</a>
    @endif
</div>

{{-- Domains --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Domains ({{ $brand->domains->count() }})</h3>
        <a href="{{ route('domains.create') }}" class="text-sm font-medium hover:underline" style="color:var(--primary)">+ Add Domain</a>
    </div>
    @forelse($brand->domains as $domain)
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-900">{{ $domain->domain }}</p>
            <p class="text-xs text-gray-400 font-mono mt-0.5">API Key: {{ Str::limit($domain->api_key, 20) }}...</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500">{{ $domain->submissions_count }} leads</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $domain->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $domain->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>
    @empty
    <div class="px-5 py-8 text-center text-sm text-gray-400">No domains yet</div>
    @endforelse
</div>

{{-- Recent Submissions --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900">Recent Submissions</h3>
    </div>
    <table class="w-full">
        <tbody class="divide-y divide-gray-100">
            @forelse($recentSubmissions as $sub)
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('submissions.show', $sub) }}'">
                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $sub->getDisplayName() }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $sub->email ?? '—' }}</td>
                <td class="px-4 py-3 text-sm text-gray-500">{{ $sub->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400">No submissions yet</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
