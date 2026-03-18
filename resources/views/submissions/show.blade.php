@extends('layouts.app')
@section('title', 'Submission Detail')

@section('content')
<div class="mb-4">
    <a href="{{ route('submissions.index') }}" class="text-sm font-medium hover:underline" style="color:var(--primary)">← Back to Inbox</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main content --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Contact info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $submission->getDisplayName() }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Submitted {{ $submission->created_at->format('M d, Y \a\t H:i') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($submission->aiClassification)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            {{ $submission->aiClassification->lead_temperature === 'hot' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $submission->aiClassification->lead_temperature === 'warm' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $submission->aiClassification->lead_temperature === 'cold' ? 'bg-blue-100 text-blue-700' : '' }}">
                            {{ ucfirst($submission->aiClassification->lead_temperature) }} Lead
                        </span>
                    @endif
                    @if($submission->is_spam)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">SPAM</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div><span class="text-xs font-medium text-gray-400 uppercase">Email</span><p class="text-sm text-gray-900 mt-0.5">{{ $submission->email ?? '—' }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">Phone</span><p class="text-sm text-gray-900 mt-0.5">{{ $submission->phone ?? '—' }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">Company</span><p class="text-sm text-gray-900 mt-0.5">{{ $submission->company ?? '—' }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">Budget</span><p class="text-sm text-gray-900 mt-0.5">{{ $submission->budget ?? '—' }}</p></div>
            </div>

            @if($submission->subject)
            <div class="mt-4">
                <span class="text-xs font-medium text-gray-400 uppercase">Subject</span>
                <p class="text-sm text-gray-900 mt-0.5">{{ $submission->subject }}</p>
            </div>
            @endif

            @if($submission->message)
            <div class="mt-4">
                <span class="text-xs font-medium text-gray-400 uppercase">Message</span>
                <p class="text-sm text-gray-700 mt-1 whitespace-pre-wrap bg-gray-50 rounded-lg p-3">{{ $submission->message }}</p>
            </div>
            @endif
        </div>

        {{-- AI Classification --}}
        @if($submission->aiClassification)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">AI Classification</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div><span class="text-xs font-medium text-gray-400 uppercase">Service Type</span><p class="text-sm text-gray-900 mt-0.5 capitalize">{{ str_replace('_', ' ', $submission->aiClassification->service_type ?? '—') }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">Urgency</span><p class="text-sm text-gray-900 mt-0.5 capitalize">{{ $submission->aiClassification->urgency ?? '—' }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">Sentiment</span><p class="text-sm text-gray-900 mt-0.5 capitalize">{{ $submission->aiClassification->sentiment ?? '—' }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">Spam Probability</span><p class="text-sm text-gray-900 mt-0.5">{{ number_format($submission->aiClassification->spam_probability * 100, 1) }}%</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">Model</span><p class="text-sm text-gray-900 mt-0.5">{{ $submission->aiClassification->model_used }}</p></div>
            </div>
            @if($submission->aiClassification->summary)
            <div class="mt-4">
                <span class="text-xs font-medium text-gray-400 uppercase">Summary</span>
                <p class="text-sm text-gray-700 mt-1">{{ $submission->aiClassification->summary }}</p>
            </div>
            @endif
        </div>
        @endif

        {{-- Tracking Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Tracking & Source</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-xs font-medium text-gray-400 uppercase">Brand</span><p class="text-gray-900 mt-0.5">{{ $submission->brand->name ?? '—' }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">Domain</span><p class="text-gray-900 mt-0.5">{{ $submission->domain->domain ?? '—' }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">Page URL</span><p class="text-gray-900 mt-0.5 truncate">{{ $submission->page_url ?? '—' }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">Referrer</span><p class="text-gray-900 mt-0.5 truncate">{{ $submission->referrer ?? '—' }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">UTM Source</span><p class="text-gray-900 mt-0.5">{{ $submission->utm_source ?? '—' }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">UTM Medium</span><p class="text-gray-900 mt-0.5">{{ $submission->utm_medium ?? '—' }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">IP Address</span><p class="text-gray-900 mt-0.5">{{ $submission->ip_address ?? '—' }}</p></div>
                <div><span class="text-xs font-medium text-gray-400 uppercase">Spam Score</span><p class="text-gray-900 mt-0.5">{{ $submission->spam_score }}</p></div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Internal Notes</h3>
            @if($canManageSubmissions ?? false)
            <form method="POST" action="{{ route('submissions.updateNotes', $submission) }}">
                @csrf @method('PATCH')
                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Add notes...">{{ $submission->notes }}</textarea>
                <div class="mt-2"><button type="submit" class="text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90" style="background:var(--primary)">Save Notes</button></div>
            </form>
            @else
            <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $submission->notes ?: 'No notes' }}</p>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Status --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Status</h3>
            @if($canManageSubmissions ?? false)
            <form method="POST" action="{{ route('submissions.updateStatus', $submission) }}">
                @csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach(['new', 'open', 'in_progress', 'closed', 'archived'] as $s)
                        <option value="{{ $s }}" {{ $submission->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </form>
            @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ ucfirst(str_replace('_', ' ', $submission->status)) }}</span>
            @endif
        </div>

        {{-- Assignment --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Assignment</h3>
            @if($submission->currentAssignment)
                <p class="text-sm text-gray-700 mb-2">Currently assigned to: <strong>{{ $submission->currentAssignment->assignee->name }}</strong></p>
            @else
                <p class="text-sm text-gray-400 mb-2">Not assigned</p>
            @endif
            @if($canManageSubmissions ?? false)
            <form method="POST" action="{{ route('submissions.reassign', $submission) }}">
                @csrf
                <select name="assigned_to" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select team member</option>
                    @foreach($teamMembers as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Reassign</button>
            </form>
            @endif
        </div>

        {{-- Assignment History --}}
        @if($submission->assignments->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Assignment History</h3>
            <div class="space-y-3">
                @foreach($submission->assignments->sortByDesc('created_at') as $assignment)
                <div class="border-l-2 pl-3 {{ $assignment->status === 'active' ? 'border-green-500' : 'border-gray-300' }}">
                    <p class="text-sm text-gray-900 font-medium">{{ $assignment->assignee->name }}</p>
                    <p class="text-xs text-gray-500">{{ $assignment->reason }}</p>
                    <p class="text-xs text-gray-400">{{ $assignment->assigned_at->diffForHumans() }} · {{ ucfirst($assignment->status) }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
