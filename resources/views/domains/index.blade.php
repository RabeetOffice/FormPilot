@extends('layouts.app')
@section('title', 'Domains')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Domains</h2>
        <p class="text-sm text-gray-500 mt-1">Manage domains and API keys</p>
    </div>
    @if($isAdmin ?? false)
    <a href="{{ route('domains.create') }}" class="text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90" style="background:var(--primary)">+ Add Domain</a>
    @endif
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Domain</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Brand</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">API Key</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Leads</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                @if($isAdmin ?? false)
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($domains as $domain)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3.5 text-sm font-medium text-gray-900">{{ $domain->domain }}</td>
                <td class="px-4 py-3.5">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full" style="background: {{ $domain->brand->color ?? '#6B7280' }}"></span>
                        <span class="text-sm text-gray-700">{{ $domain->brand->name }}</span>
                    </span>
                </td>
                <td class="px-4 py-3.5">
                    <button type="button" onclick="copyToClipboard('{{ $domain->api_key }}', this)" class="group flex items-center gap-2 text-left hover:bg-gray-50 p-1 rounded transition-colors" title="Click to copy full API key">
                        <code class="text-xs bg-gray-100 px-2 py-1 rounded font-mono text-gray-600 group-hover:bg-gray-200 transition-colors">{{ Str::limit($domain->api_key, 24) }}</code>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </td>
                <td class="px-4 py-3.5 text-sm text-gray-600">{{ $domain->submissions_count }}</td>
                <td class="px-4 py-3.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $domain->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $domain->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                @if($isAdmin ?? false)
                <td class="px-4 py-3.5">
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('domains.regenerateKey', $domain) }}">
                            @csrf
                            <button type="submit" class="text-xs text-gray-500 hover:text-gray-700" onclick="return confirm('Regenerate API key? Existing integrations will stop working.')">Regen Key</button>
                        </form>
                        <form method="POST" action="{{ route('domains.destroy', $domain) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700" onclick="return confirm('Delete this domain?')">Delete</button>
                        </form>
                    </div>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">No domains yet. Add your first domain to start capturing leads.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($domains->hasPages())
    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">{{ $domains->links() }}</div>
    @endif
</div>

<script>
function copyToClipboard(text, buttonElement) {
    navigator.clipboard.writeText(text).then(() => {
        // Find the SVG icon inside the button
        const icon = buttonElement.querySelector('svg');
        const originalPath = icon.innerHTML;
        
        // Change to checkmark temporarily
        icon.classList.remove('text-gray-400', 'group-hover:text-indigo-600');
        icon.classList.add('text-green-500');
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
        
        // Change back after 2 seconds
        setTimeout(() => {
            icon.classList.remove('text-green-500');
            icon.classList.add('text-gray-400', 'group-hover:text-indigo-600');
            icon.innerHTML = originalPath;
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy text: ', err);
        alert('Failed to copy API key.');
    });
}
</script>
@endsection
