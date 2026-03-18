@extends('layouts.app')
@section('title', 'Setup Instructions')

@section('content')
<div class="mb-6">
    <h2 class="text-lg font-bold text-gray-900">Setup Instructions</h2>
    <p class="text-sm text-gray-500 mt-1">Connect your forms to FormPilot</p>
</div>

@if($domains->count() === 0)
<div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg text-sm mb-6">
    You need to <a href="{{ route('domains.create') }}" class="font-semibold underline">add a domain</a> first before you can start capturing forms.
</div>
@else
{{-- Domain selector --}}
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Select Domain</label>
    <select id="domainSelector" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full max-w-md focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="updateSnippets()">
        @foreach($domains as $domain)
            <option value="{{ $domain->api_key }}" data-domain="{{ $domain->domain }}" data-brand="{{ $domain->brand->name }}">{{ $domain->domain }} ({{ $domain->brand->name }})</option>
        @endforeach
    </select>
</div>

{{-- Method 1: JS Snippet --}}
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <div class="flex items-center gap-2 mb-3">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Recommended</span>
        <h3 class="text-sm font-semibold text-gray-900">Method 1: Universal JavaScript Snippet</h3>
    </div>
    <p class="text-sm text-gray-500 mb-4">Add this one line to your webpage. It automatically intercepts all form submissions and sends them to FormPilot.</p>
    <div class="relative">
        <pre id="jsSnippet" class="bg-gray-900 text-green-400 rounded-lg p-4 text-sm font-mono overflow-x-auto"></pre>
        <button onclick="copySnippet('jsSnippet')" class="absolute top-2 right-2 bg-gray-700 text-white px-3 py-1 rounded text-xs hover:bg-gray-600">Copy</button>
    </div>
</div>

{{-- Method 2: Direct POST --}}
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-3">Method 2: Direct POST Endpoint</h3>
    <p class="text-sm text-gray-500 mb-4">Send form data directly to this endpoint via a standard HTML form or AJAX.</p>
    <div class="relative">
        <pre id="directEndpoint" class="bg-gray-900 text-green-400 rounded-lg p-4 text-sm font-mono overflow-x-auto"></pre>
        <button onclick="copySnippet('directEndpoint')" class="absolute top-2 right-2 bg-gray-700 text-white px-3 py-1 rounded text-xs hover:bg-gray-600">Copy</button>
    </div>

    <h4 class="text-sm font-medium text-gray-700 mt-6 mb-3">Example HTML Form</h4>
    <div class="relative">
        <pre id="htmlFormExample" class="bg-gray-900 text-green-400 rounded-lg p-4 text-sm font-mono overflow-x-auto whitespace-pre-wrap"></pre>
        <button onclick="copySnippet('htmlFormExample')" class="absolute top-2 right-2 bg-gray-700 text-white px-3 py-1 rounded text-xs hover:bg-gray-600">Copy</button>
    </div>
</div>

{{-- Method 3: Webhook --}}
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-3">Method 3: Webhook (WordPress / Zapier / etc.)</h3>
    <p class="text-sm text-gray-500 mb-4">Use this URL as a webhook endpoint in WordPress Contact Form 7, Gravity Forms, Zapier, or any service that supports webhooks.</p>
    <div class="relative">
        <pre id="webhookEndpoint" class="bg-gray-900 text-green-400 rounded-lg p-4 text-sm font-mono overflow-x-auto"></pre>
        <button onclick="copySnippet('webhookEndpoint')" class="absolute top-2 right-2 bg-gray-700 text-white px-3 py-1 rounded text-xs hover:bg-gray-600">Copy</button>
    </div>

    <h4 class="text-sm font-medium text-gray-700 mt-6 mb-3">Example Webhook Payload (JSON)</h4>
    <pre class="bg-gray-900 text-green-400 rounded-lg p-4 text-sm font-mono overflow-x-auto">{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "message": "I need a new website",
  "budget": "$5000"
}</pre>
</div>

{{-- Hidden Fields Guide --}}
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-3">Hidden Fields (Optional)</h3>
    <p class="text-sm text-gray-500 mb-4">Add these hidden fields to your forms for enhanced tracking and spam protection.</p>
    <pre class="bg-gray-900 text-green-400 rounded-lg p-4 text-sm font-mono overflow-x-auto whitespace-pre-wrap">&lt;!-- Honeypot field (spam protection) --&gt;
&lt;input type="text" name="_fp_hp" style="display:none" tabindex="-1" autocomplete="off"&gt;

&lt;!-- UTM tracking (auto-filled by JS snippet) --&gt;
&lt;input type="hidden" name="utm_source" value=""&gt;
&lt;input type="hidden" name="utm_medium" value=""&gt;
&lt;input type="hidden" name="utm_campaign" value=""&gt;
&lt;input type="hidden" name="page_url" value=""&gt;</pre>
</div>

{{-- Test Submission --}}
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-3">Test Submission</h3>
    <p class="text-sm text-gray-500 mb-4">Send a test submission to verify your setup is working.</p>
    <button onclick="sendTestSubmission()" id="testBtn" class="text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:opacity-90" style="background:var(--primary)">Send Test Submission</button>
    <div id="testResult" class="mt-3 text-sm hidden"></div>
</div>
@endif

<script>
function getSelectedKey() {
    return document.getElementById('domainSelector')?.value || '';
}

function updateSnippets() {
    const key = getSelectedKey();
    const base = '{{ url('/') }}';

    document.getElementById('jsSnippet').textContent =
        `<script src="${base}/js/formpilot.js" data-fp-key="${key}"><\/script>`;

    document.getElementById('directEndpoint').textContent =
        `POST ${base}/api/f/${key}`;

    document.getElementById('webhookEndpoint').textContent =
        `POST ${base}/api/webhook/${key}`;

    document.getElementById('htmlFormExample').textContent =
`<form action="${base}/api/f/${key}" method="POST">
  <input type="text" name="name" placeholder="Your Name" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="tel" name="phone" placeholder="Phone">
  <textarea name="message" placeholder="Message"></textarea>
  <input type="text" name="_fp_hp" style="display:none" tabindex="-1">
  <button type="submit">Submit</button>
</form>`;
}

function copySnippet(id) {
    const text = document.getElementById(id).textContent;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById(id).parentElement.querySelector('button');
        btn.textContent = 'Copied!';
        setTimeout(() => btn.textContent = 'Copy', 2000);
    });
}

function sendTestSubmission() {
    const key = getSelectedKey();
    const base = '{{ url('/') }}';
    const btn = document.getElementById('testBtn');
    const result = document.getElementById('testResult');

    btn.textContent = 'Sending...';
    btn.disabled = true;

    fetch(`${base}/api/f/${key}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
            name: 'Test User',
            email: 'test@formpilot.app',
            phone: '+1234567890',
            message: 'This is a test submission from the setup page.',
            subject: 'Test Submission',
            budget: '$1000',
        })
    })
    .then(r => r.json())
    .then(data => {
        result.classList.remove('hidden');
        if (data.success) {
            result.innerHTML = '<span class="text-green-600">✓ Test submission received! Check your inbox.</span>';
        } else {
            result.innerHTML = `<span class="text-red-600">✗ Error: ${data.error || 'Unknown error'}</span>`;
        }
    })
    .catch(err => {
        result.classList.remove('hidden');
        result.innerHTML = `<span class="text-red-600">✗ Connection error: ${err.message}</span>`;
    })
    .finally(() => {
        btn.textContent = 'Send Test Submission';
        btn.disabled = false;
    });
}

// Initialize on load
document.addEventListener('DOMContentLoaded', updateSnippets);
</script>
@endsection
