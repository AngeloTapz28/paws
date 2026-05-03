<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $applications = AdoptionApplication::with(['pet', 'adopter'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) =>
                $q->where('application_number', 'like', "%{$request->search}%")
            )
            ->latest()
            ->paginate(15);

        return view('staff.applications.index', compact('applications'));
    }

    public function show(AdoptionApplication $application)
    {
        $application->load(['pet', 'adopter', 'payments']);
        return view('staff.applications.show', compact('application'));
    }
}