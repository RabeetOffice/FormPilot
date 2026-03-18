@extends('layouts.app')
@section('title', 'Team Members')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Team Members</h2>
        <p class="text-sm text-gray-500 mt-1">Manage workspace users and roles</p>
    </div>
</div>

{{-- Invite form --}}
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-3">Invite Team Member</h3>
    <form method="POST" action="{{ route('team.invite') }}" class="flex flex-wrap gap-3 items-end">
        @csrf
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
            <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="team@example.com">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
            <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="admin">Admin</option>
                <option value="sales_rep">Sales Rep</option>
                <option value="viewer">Viewer</option>
            </select>
        </div>
        <button type="submit" class="text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90" style="background:var(--primary)">Invite</button>
    </form>
</div>

{{-- Members table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Joined</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($members as $member)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3.5">
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-semibold text-white" style="background:var(--primary)">{{ substr($member->name, 0, 1) }}</div>
                        <span class="text-sm font-medium text-gray-900">{{ $member->name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $member->email }}</td>
                <td class="px-4 py-3.5">
                    @if($member->pivot->role !== 'owner' && (($userRole ?? '') === 'owner' || ($userRole ?? '') === 'admin'))
                    <form method="POST" action="{{ route('team.updateRole', $member) }}" class="inline">
                        @csrf @method('PATCH')
                        <select name="role" onchange="this.form.submit()" class="border border-gray-300 rounded px-2 py-1 text-xs">
                            @foreach(['admin', 'sales_rep', 'viewer'] as $role)
                                <option value="{{ $role }}" {{ $member->pivot->role === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                            @endforeach
                        </select>
                    </form>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">{{ ucfirst(str_replace('_', ' ', $member->pivot->role)) }}</span>
                    @endif
                </td>
                <td class="px-4 py-3.5 text-sm text-gray-500">{{ $member->pivot->accepted_at ? \Carbon\Carbon::parse($member->pivot->accepted_at)->format('M d, Y') : 'Pending' }}</td>
                <td class="px-4 py-3.5">
                    @if($member->pivot->role !== 'owner' && $member->id !== Auth::id())
                    <form method="POST" action="{{ route('team.remove', $member) }}" onsubmit="return confirm('Remove this team member?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Remove</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
