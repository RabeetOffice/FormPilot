@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-bold text-gray-900">Notifications</h2>
    <form method="POST" action="{{ route('notifications.markAllRead') }}">
        @csrf
        <button type="submit" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">Mark All Read</button>
    </form>
</div>

<div class="space-y-2">
    @forelse($notifications as $notification)
    <form method="POST" action="{{ route('notifications.markAsRead', $notification) }}">
        @csrf
        <button type="submit" class="w-full text-left bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow {{ $notification->isRead() ? 'opacity-60' : '' }}">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 h-2 w-2 rounded-full flex-shrink-0 {{ $notification->isRead() ? 'bg-gray-300' : '' }}" @if(!$notification->isRead()) style="background:var(--primary)" @endif></div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $notification->body }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                    {{ $notification->type === 'new_submission' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $notification->type === 'assignment' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $notification->type === 'spam_alert' ? 'bg-red-100 text-red-700' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                </span>
            </div>
        </button>
    </form>
    @empty
    <div class="text-center py-12">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <p class="text-sm text-gray-400">No notifications yet</p>
    </div>
    @endforelse
</div>

@if($notifications->hasPages())
<div class="mt-6">{{ $notifications->links() }}</div>
@endif
@endsection
