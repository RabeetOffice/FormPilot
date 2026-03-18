@extends('layouts.app')
@section('title', 'White Label Settings')

@section('content')
<div class="max-w-xl">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-900">White Label Settings</h2>
        <p class="text-sm text-gray-500 mt-1">Customize the appearance of your workspace</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('whitelabel.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">App Name</label>
                    <input type="text" name="app_name" value="{{ $workspace->settings['app_name'] ?? 'FormPilot' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Your brand name">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="primary_color" value="{{ $workspace->settings['primary_color'] ?? '#4F46E5' }}" class="h-10 w-20 border border-gray-300 rounded-lg cursor-pointer">
                        <span class="text-sm text-gray-500">Used for buttons, links, and active states</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Sender Name</label>
                    <input type="text" name="email_sender_name" value="{{ $workspace->settings['email_sender_name'] ?? 'FormPilot' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Your Agency Name">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    @if($workspace->getLogo())
                        <img src="{{ $workspace->getLogo() }}" class="h-12 mb-2 rounded" alt="Current logo">
                    @endif
                    <input type="file" name="logo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                </div>

                <button type="submit" class="text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:opacity-90" style="background:var(--primary)">Save Settings</button>
            </div>
        </form>
    </div>

    {{-- Preview --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Preview</h3>
        <div class="bg-gray-50 rounded-lg p-6 flex items-center gap-4">
            @if($workspace->getLogo())
                <img src="{{ $workspace->getLogo() }}" class="h-10 w-10 rounded" alt="">
            @else
                <div class="h-10 w-10 rounded-lg flex items-center justify-center text-white text-sm font-bold" style="background: {{ $workspace->getPrimaryColor() }}">
                    {{ substr($workspace->getAppName(), 0, 2) }}
                </div>
            @endif
            <div>
                <p class="font-bold text-gray-900">{{ $workspace->getAppName() }}</p>
                <p class="text-xs text-gray-500">Email sender: {{ $workspace->getEmailSenderName() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
