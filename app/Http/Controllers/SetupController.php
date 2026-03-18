<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\FormSource;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function index(Request $request)
    {
        $workspace = $request->workspace;
        $domains = Domain::whereHas('brand', fn($q) => $q->where('workspace_id', $workspace->id))
            ->with('brand:id,name')
            ->get();

        return view('setup.index', compact('domains'));
    }
}
