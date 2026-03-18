@extends('layouts.app')
@section('title', 'Form Sources')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Form Sources</h2>
        <p class="text-sm text-gray-500 mt-1">Track how forms are connected to your domains</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Domain</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Brand</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Submissions</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($formSources as $fs)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3.5 text-sm font-medium text-gray-900">{{ $fs->name }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $fs->domain->domain }}</td>
                <td class="px-4 py-3.5">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full" style="background: {{ $fs->domain->brand->color ?? '#6B7280' }}"></span>
                        <span class="text-sm text-gray-700">{{ $fs->domain->brand->name }}</span>
                    </span>
                </td>
                <td class="px-4 py-3.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ ucfirst(str_replace('_', ' ', $fs->type)) }}</span>
                </td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $fs->submissions_count }}</td>
                <td class="px-4 py-3.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $fs->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $fs->is_active ? 'Active' : 'Inactive' }}</span>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">No form sources yet. They are automatically created when you add a domain.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($formSources->hasPages())
    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">{{ $formSources->links() }}</div>
    @endif
</div>
@endsection
