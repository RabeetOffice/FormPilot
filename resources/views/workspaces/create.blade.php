<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Workspace — FormPilot</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 antialiased">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="h-12 w-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white text-lg font-bold mx-auto mb-4">FP</div>
                <h1 class="text-2xl font-bold text-gray-900">Welcome to FormPilot</h1>
                <p class="text-sm text-gray-500 mt-2">Create your first workspace to get started</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <form method="POST" action="{{ route('workspaces.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Workspace Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. My Agency">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                            <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Brief description...">{{ old('description') }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">Create Workspace</button>
                    </div>
                </form>
            </div>

            <div class="text-center mt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-400 hover:text-gray-600">Logout</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
