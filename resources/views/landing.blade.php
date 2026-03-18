<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FormPilot — Smart Form Backend as a Service</title>
    <meta name="description" content="Capture form submissions from any website into one centralized dashboard. AI-powered lead scoring, routing, and classification for agencies and multi-brand businesses.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 50%, #EC4899 100%); }
        .glass { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2); }
    </style>
</head>
<body class="bg-white antialiased">
    {{-- Nav --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-sm font-bold">FP</div>
                <span class="font-bold text-gray-900 text-lg">FormPilot</span>
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Login</a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">Get Started Free</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="gradient-bg pt-32 pb-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMSIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjEpIi8+PC9zdmc+')] opacity-50"></div>
        <div class="max-w-4xl mx-auto text-center px-6 relative">
            <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-1.5 mb-6">
                <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                <span class="text-white/80 text-xs font-medium">Now in Beta — Free for early adopters</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight mb-6">
                One dashboard for<br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-amber-200 to-pink-200">all your form leads</span>
            </h1>
            <p class="text-lg text-white/70 max-w-2xl mx-auto mb-10">
                Keep your existing forms. Add one JS snippet or use a direct endpoint. Capture all submissions in one place with AI-powered lead scoring and routing.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('register') }}" class="bg-white text-indigo-700 px-8 py-3.5 rounded-xl text-sm font-bold hover:bg-gray-50 transition-colors shadow-lg">Start Free — No Credit Card</a>
                <a href="#features" class="glass text-white px-8 py-3.5 rounded-xl text-sm font-medium hover:bg-white/20 transition-colors">How It Works ↓</a>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Everything you need to manage leads</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">Built for agencies, freelancers, and teams who manage multiple websites and need one central inbox.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                $features = [
                    ['🔌', 'Universal Form Capture', 'Works with HTML forms, Bootstrap, WordPress, or any form. No redesign needed.'],
                    ['📥', 'Central Inbox', 'All leads from all websites in one searchable, filterable dashboard.'],
                    ['🤖', 'AI Lead Scoring', 'Automatic lead temperature, service type detection, and spam filtering.'],
                    ['🏢', 'Multi-Brand', 'Organize sites by brand. Perfect for agencies with multiple clients.'],
                    ['📊', 'Smart Routing', 'Assign leads to reps based on service type, brand, budget, or location.'],
                    ['🏷️', 'White Label', 'Customize logo, colors, and branding. Make it yours.'],
                ];
                @endphp
                @foreach($features as $f)
                <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow">
                    <div class="text-3xl mb-3">{{ $f[0] }}</div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $f[1] }}</h3>
                    <p class="text-sm text-gray-500">{{ $f[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Three simple steps</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @php
                $steps = [
                    ['1', 'Add your websites', 'Create brands and add domains. Get an API key in seconds.'],
                    ['2', 'Connect your forms', 'Add one JS snippet to your pages or use the direct POST endpoint.'],
                    ['3', 'Manage leads', 'See all submissions in your inbox. AI scores and routes them automatically.'],
                ];
                @endphp
                @foreach($steps as $step)
                <div class="text-center">
                    <div class="h-12 w-12 rounded-full bg-indigo-600 text-white text-lg font-bold flex items-center justify-center mx-auto mb-4">{{ $step[0] }}</div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $step[1] }}</h3>
                    <p class="text-sm text-gray-500">{{ $step[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="gradient-bg py-20">
        <div class="max-w-4xl mx-auto text-center px-6">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to centralize your leads?</h2>
            <p class="text-white/70 mb-8">Start capturing form submissions in under 5 minutes.</p>
            <a href="{{ route('register') }}" class="bg-white text-indigo-700 px-8 py-3.5 rounded-xl text-sm font-bold hover:bg-gray-50 transition-colors shadow-lg inline-block">Get Started Free</a>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <div class="flex items-center justify-center gap-2 mb-4">
                <div class="h-6 w-6 rounded bg-indigo-600 flex items-center justify-center text-white text-xs font-bold">FP</div>
                <span class="text-white font-semibold">FormPilot</span>
            </div>
            <p class="text-sm">Smart Form Backend as a Service</p>
            <p class="text-xs mt-4">&copy; {{ date('Y') }} FormPilot. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
