<?php

namespace App\Http\Controllers\Adopter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\Pet;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = AdoptionApplication::with(['pet'])
            ->where('adopter_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('adopter.applications.index', compact('applications'));
    }

    public function create(Pet $pet)
    {
        $user = auth()->user();
        return view('adopter.applications.create', compact('pet', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pet_id'              => 'required|exists:pets,id',
            'reason_for_adopting' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $pet  = Pet::lockForUpdate()->findOrFail($request->pet_id);
            $user = auth()->user();

            $application = AdoptionApplication::create([
                'pet_id'               => $request->pet_id,
                'adopter_id'           => $user->id,
                'status'               => 'submitted',
                'applicant_full_name'  => $user->name,
                'applicant_email'      => $user->email,
                'applicant_phone'      => $user->phone ?? '',
                'applicant_address'    => $user->address ?? '',
                'housing_type'         => $request->housing_type ?? 'not specified',
                'has_yard'             => $request->has_yard ? true : false,
                'has_other_pets'       => $request->has_other_pets ? true : false,
                'other_pets_details'   => $request->other_pets_details,
                'has_children'         => $request->has_children ? true : false,
                'reason_for_adopting'  => $request->reason_for_adopting,
                'experience_with_pets' => $request->experience_with_pets,
                'occupation'           => $request->occupation,
                'working_hours'        => $request->working_hours,
                'emergency_contact'    => $request->emergency_contact,
                'additional_notes'     => $request->additional_notes,
                'submitted_at'         => now(),
            ]);

            $pet->update(['status' => 'pending']);

            SystemNotification::create([
                'user_id' => $user->id,
                'title'   => 'Application Submitted',
                'message' => "Your application for {$pet->name} has been submitted successfully.",
                'type'    => 'success',
                'link'    => route('adopter.applications.show', $application),
            ]);
        });

        return redirect()->route('adopter.applications.index')
            ->with('success', 'Application submitted successfully!');
    }

    public function show(AdoptionApplication $application)
    {
        abort_if($application->adopter_id !== auth()->id(), 403);
        $application->load(['pet', 'payments']);
        return view('adopter.applications.show', compact('application'));
    }

    public function withdraw(AdoptionApplication $application)
    {
        abort_if($application->adopter_id !== auth()->id(), 403);

        DB::transaction(function () use ($application) {
            $application->update(['status' => 'withdrawn']);
            $application->pet->update(['status' => 'available']);
        });

        return back()->with('success', 'Application withdrawn successfully.');
    }

    public function returnPet(Request $request, AdoptionApplication $application)
{
    abort_if($application->adopter_id !== auth()->id(), 403);
    abort_if($application->status !== 'completed', 403);

    $request->validate([
        'return_reason' => 'required|string|min:10|max:1000',
    ]);

    DB::transaction(function () use ($request, $application) {
        $application->update([
            'status'        => 'returned',
            'return_reason' => $request->return_reason,
            'returned_at'   => now(),
        ]);

        $application->pet->update(['status' => 'available']);

        SystemNotification::create([
            'user_id' => auth()->id(),
            'title'   => 'Pet Returned',
            'message' => "You have returned {$application->pet->name}. We hope to find them a new home soon.",
            'type'    => 'info',
            'link'    => route('adopter.applications.show', $application),
        ]);

        \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->each(function ($admin) use ($application) {
                SystemNotification::create([
                    'user_id' => $admin->id,
                    'title'   => 'Pet Returned',
                    'message' => "{$application->adopter->name} has returned {$application->pet->name}.",
                    'type'    => 'warning',
                    'link'    => route('admin.applications.show', $application),
                ]);
            });
    });

    return redirect()->route('adopter.applications.index')
        ->with('success', 'Pet returned successfully. Thank you for letting us know.');
}
}