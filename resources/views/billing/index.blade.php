@extends('layouts.app')
@section('title', 'Billing')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-900">Billing & Subscription</h2>
        <p class="text-sm text-gray-500 mt-1">Manage your plan and billing details</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
        <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Free Plan</h3>
        <p class="text-sm text-gray-500 mb-6">You're currently on the free plan. Upgrade to unlock more features.</p>

        <div class="bg-gray-50 rounded-lg p-5 text-left mb-6">
            <h4 class="text-sm font-semibold text-gray-900 mb-3">Current Usage</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Submissions this month</span><span class="font-medium text-gray-900">—</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Brands</span><span class="font-medium text-gray-900">—</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Team members</span><span class="font-medium text-gray-900">—</span></div>
            </div>
        </div>

        <p class="text-sm text-gray-400">Billing integration coming soon. Contact us for enterprise pricing.</p>
    </div>
</div>
@endsection
