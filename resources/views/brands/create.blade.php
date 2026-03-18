@extends('layouts.app')
@section('title', 'Create Brand')

@section('content')
<div class="max-w-xl">
    <div class="mb-6">
        <a href="{{ route('brands.index') }}" class="text-sm font-medium hover:underline" style="color:var(--primary)">← Back to Brands</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Create a New Brand</h2>

        <form method="POST" action="{{ route('brands.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Acme Corp">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Brief description of this brand...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand Color</label>
                    <input type="color" name="color" value="{{ old('color', '#4F46E5') }}" class="h-10 w-20 border border-gray-300 rounded-lg cursor-pointer">
                </div>

                <button type="submit" class="text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:opacity-90" style="background:var(--primary)">Create Brand</button>
            </div>
        </form>
    </div>
</div>
@endsection
