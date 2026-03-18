<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhiteLabelController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->workspace;
        return view('whitelabel.index', compact('workspace'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:7',
            'email_sender_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $workspace = $request->workspace;
        $settings = $workspace->settings ?? [];

        $settings['app_name'] = $validated['app_name'] ?? $settings['app_name'] ?? config('app.name');
        $settings['primary_color'] = $validated['primary_color'] ?? $settings['primary_color'] ?? '#4F46E5';
        $settings['email_sender_name'] = $validated['email_sender_name'] ?? $settings['email_sender_name'] ?? config('app.name');

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $settings['logo'] = '/storage/' . $path;
        }

        $workspace->update(['settings' => $settings]);

        return back()->with('success', 'White-label settings updated!');
    }
}
