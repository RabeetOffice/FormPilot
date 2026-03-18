@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Submissions</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalSubmissions) }}</p>
            </div>
            <div class="h-12 w-12 rounded-xl flex items-center justify-center" style="background: color-mix(in srgb, var(--primary) 15%, white)">
                <svg class="w-6 h-6" style="color:var(--primary)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Today</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($submissionsToday) }}</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Hot Leads</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ number_format($hotLeads) }}</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Spam Blocked</p>
                <p class="text-3xl font-bold text-gray-400 mt-1">{{ number_format($spamBlocked) }}</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Charts row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Leads by Brand --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Leads by Brand</h3>
        @forelse($leadsByBrand as $item)
            <div class="flex items-center justify-between py-2">
                <div class="flex items-center gap-2">
                    <div class="h-3 w-3 rounded-full" style="background: {{ $item->brand->color ?? '#6B7280' }}"></div>
                    <span class="text-sm text-gray-700">{{ $item->brand->name ?? 'Unknown' }}</span>
                </div>
                <span class="text-sm font-semibold text-gray-900">{{ $item->count }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-400">No data yet</p>
        @endforelse
    </div>

    {{-- Leads by Service Type --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Leads by Service Type</h3>
        @forelse($leadsByServiceType as $type => $count)
            <div class="flex items-center justify-between py-2">
                <span class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $type) }}</span>
                <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-400">No data yet</p>
        @endforelse
    </div>

    {{-- Assigned by Rep --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Assigned by Rep</h3>
        @forelse($leadsByRep as $rep => $count)
            <div class="flex items-center justify-between py-2">
                <span class="text-sm text-gray-700">{{ $rep }}</span>
                <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-400">No assignments yet</p>
        @endforelse
    </div>
</div>

{{-- Recent Submissions --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Recent Submissions</h3>
        <a href="{{ route('submissions.index') }}" class="text-sm font-medium hover:underline" style="color:var(--primary)">View all →</a>
    </div>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Brand</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Temperature</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Rep</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($recentSubmissions as $sub)
            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('submissions.show', $sub) }}'">
                <td class="px-4 py-3.5 text-sm font-medium text-gray-900">{{ $sub->getDisplayName() }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $sub->email ?? '—' }}</td>
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
                        <span class="text-xs text-gray-400">Pending</span>
                    @endif
                </td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $sub->currentAssignment?->assignee?->name ?? '—' }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-500">{{ $sub->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">No submissions yet. Set up your first form integration!</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
