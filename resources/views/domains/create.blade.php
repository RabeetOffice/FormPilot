@extends('layouts.app')
@section('title', 'Add Domain')

@section('content')
<div class="max-w-xl">
    <div class="mb-6">
        <a href="{{ route('domains.index') }}" class="text-sm font-medium hover:underline" style="color:var(--primary)">← Back to Domains</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Add a New Domain</h2>

        <form method="POST" action="{{ route('domains.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                    <select name="brand_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select a brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('brand_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Domain</label>
                    <input type="text" name="domain" value="{{ old('domain') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. example.com">
                    <p class="text-xs text-gray-400 mt-1">Enter the domain without http:// or www.</p>
                    @error('domain') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:opacity-90" style="background:var(--primary)">Add Domain</button>
            </div>
        </form>
    </div>
</div>
@endsection
