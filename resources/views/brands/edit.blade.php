@extends('layouts.app')
@section('title', 'Edit Brand')

@section('content')
<div class="max-w-xl">
    <div class="mb-6">
        <a href="{{ route('brands.show', $brand) }}" class="text-sm font-medium hover:underline" style="color:var(--primary)">← Back to {{ $brand->name }}</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-6">Edit Brand</h2>

        <form method="POST" action="{{ route('brands.update', $brand) }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand Name</label>
                    <input type="text" name="name" value="{{ old('name', $brand->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $brand->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand Color</label>
                    <input type="color" name="color" value="{{ old('color', $brand->color ?? '#4F46E5') }}" class="h-10 w-20 border border-gray-300 rounded-lg cursor-pointer">
                </div>
                <div class="flex items-center gap-4">
                    <button type="submit" class="text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:opacity-90" style="background:var(--primary)">Save Changes</button>
                </div>
            </div>
        </form>

        <hr class="my-6 border-gray-200">

        <form method="POST" action="{{ route('brands.destroy', $brand) }}" onsubmit="return confirm('Delete this brand and all its data?')">
            @csrf @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700">Delete Brand</button>
        </form>
    </div>
</div>
@endsection
