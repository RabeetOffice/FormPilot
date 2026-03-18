@extends('layouts.app')
@section('title', 'Routing Rules')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Routing Rules</h2>
        <p class="text-sm text-gray-500 mt-1">Auto-assign leads to team members based on conditions</p>
    </div>
</div>

@if($isAdmin ?? false)
{{-- Add Rule Form --}}
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-3">Add Routing Rule</h3>
    <form method="POST" action="{{ route('routing-rules.store') }}" class="space-y-3">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Rule Name</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Web Design to John">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="service_type">Service Type</option>
                    <option value="brand">Brand</option>
                    <option value="budget">Budget Threshold</option>
                    <option value="spam_score">Spam Score</option>
                    <option value="country">Country</option>
                    <option value="fallback">Fallback</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Value</label>
                <input type="text" name="conditions[value]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. web_design, 5000, US">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Assign To</label>
                <select name="target_user_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($teamMembers as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Priority</label>
                <input type="number" name="priority" value="0" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        <button type="submit" class="text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90" style="background:var(--primary)">Add Rule</button>
    </form>
</div>
@endif

{{-- Rules Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Priority</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Condition</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Assign To</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                @if($isAdmin ?? false)
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rules as $rule)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3.5 text-sm font-mono text-gray-500">#{{ $rule->priority }}</td>
                <td class="px-4 py-3.5 text-sm font-medium text-gray-900">{{ $rule->name }}</td>
                <td class="px-4 py-3.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">{{ ucfirst(str_replace('_', ' ', $rule->type)) }}</span>
                </td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $rule->conditions['value'] ?? ($rule->type === 'fallback' ? 'Default' : '—') }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-900">{{ $rule->targetUser->name }}</td>
                <td class="px-4 py-3.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $rule->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $rule->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                @if($isAdmin ?? false)
                <td class="px-4 py-3.5">
                    <form method="POST" action="{{ route('routing-rules.destroy', $rule) }}" onsubmit="return confirm('Delete this rule?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                    </form>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">No routing rules yet. Add your first rule above.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
