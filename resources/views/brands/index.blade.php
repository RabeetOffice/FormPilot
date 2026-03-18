@extends('layouts.app')
@section('title', 'Brands')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Your Brands</h2>
        <p class="text-sm text-gray-500 mt-1">Manage brands and their websites</p>
    </div>
    @if($isAdmin ?? false)
    <a href="{{ route('brands.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90" style="background:var(--primary)">+ New Brand</a>
    @endif
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($brands as $brand)
    <a href="{{ route('brands.show', $brand) }}" class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow block">
        <div class="flex items-center gap-3 mb-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center text-white text-sm font-bold" style="background: {{ $brand->color ?? '#6B7280' }}">
                {{ strtoupper(substr($brand->name, 0, 2)) }}
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900">{{ $brand->name }}</h3>
                <p class="text-xs text-gray-500">{{ $brand->slug }}</p>
            </div>
        </div>
        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $brand->description ?? 'No description' }}</p>
        <div class="flex items-center gap-4 text-xs text-gray-400">
            <span>{{ $brand->domains_count }} {{ Str::plural('domain', $brand->domains_count) }}</span>
            <span>{{ $brand->submissions_count }} {{ Str::plural('lead', $brand->submissions_count) }}</span>
        </div>
    </a>
    @empty
    <div class="col-span-3 text-center py-12">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        <p class="text-sm text-gray-400 mb-3">No brands yet</p>
        @if($isAdmin ?? false)
        <a href="{{ route('brands.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90" style="background:var(--primary)">Create Your First Brand</a>
        @endif
    </div>
    @endforelse
</div>

@if($brands->hasPages())
<div class="mt-6">{{ $brands->links() }}</div>
@endif
@endsection
