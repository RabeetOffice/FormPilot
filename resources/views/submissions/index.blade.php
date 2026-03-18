@extends('layouts.app')
@section('title', 'Submissions Inbox')

@section('content')
{{-- Filters --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, phone..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Brand</label>
            <select name="brand_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Brands</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All</option>
                @foreach(['new', 'open', 'in_progress', 'closed', 'archived'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Temperature</label>
            <select name="lead_temperature" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All</option>
                <option value="hot" {{ request('lead_temperature') === 'hot' ? 'selected' : '' }}>🔥 Hot</option>
                <option value="warm" {{ request('lead_temperature') === 'warm' ? 'selected' : '' }}>🌤 Warm</option>
                <option value="cold" {{ request('lead_temperature') === 'cold' ? 'selected' : '' }}>❄️ Cold</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Spam</label>
            <select name="is_spam" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="" {{ request('is_spam') === null ? 'selected' : '' }}>Hide Spam</option>
                <option value="true" {{ request('is_spam') === 'true' ? 'selected' : '' }}>Spam Only</option>
            </select>
        </div>
        <button type="submit" class="text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90" style="background:var(--primary)">Filter</button>
        <a href="{{ route('submissions.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Reset</a>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Phone</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Brand</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Temp</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Rep</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($submissions as $sub)
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('submissions.show', $sub) }}'">
                <td class="px-4 py-3.5 text-sm font-medium text-gray-900">
                    {{ $sub->getDisplayName() }}
                    @if($sub->is_spam)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 ml-1">SPAM</span>
                    @endif
                </td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $sub->email ?? '—' }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $sub->phone ?? '—' }}</td>
                <td class="px-4 py-3.5">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full" style="background: {{ $sub->brand->color ?? '#6B7280' }}"></span>
                        <span class="text-sm text-gray-700">{{ $sub->brand->name ?? '—' }}</span>
                    </span>
                </td>
                <td class="px-4 py-3.5">
                    @if($sub->aiClassification)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $sub->aiClassification->lead_temperature === 'hot' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $sub->aiClassification->lead_temperature === 'warm' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $sub->aiClassification->lead_temperature === 'cold' ? 'bg-blue-100 text-blue-700' : '' }}">
                            {{ ucfirst($sub->aiClassification->lead_temperature) }}
                        </span>
                    @else
                        <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-4 py-3.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $sub->status === 'new' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $sub->status === 'open' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $sub->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $sub->status === 'closed' ? 'bg-gray-100 text-gray-500' : '' }}
                        {{ $sub->status === 'archived' ? 'bg-gray-100 text-gray-400' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $sub->status)) }}
                    </span>
                </td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $sub->currentAssignment?->assignee?->name ?? '—' }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-500">{{ $sub->created_at->format('M d, H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-sm text-gray-400">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    No submissions found. Try adjusting your filters.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($submissions->hasPages())
    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
        {{ $submissions->links() }}
    </div>
    @endif
</div>
@endsection
