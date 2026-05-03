<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdoptionApplication;
use App\Models\SystemNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $applications = AdoptionApplication::with(['pet', 'adopter'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->where('application_number', 'like', "%{$request->search}%")
                ->orWhere('applicant_full_name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(15);

        return view('admin.applications.index', compact('applications'));
    }

    public function show(AdoptionApplication $application): View
    {
        $application->load([
            'pet.category', 'pet.breed', 'pet.vaccinationRecords',
            'pet.medicalRecords.vet', 'adopter', 'reviewedBy', 'payments',
        ]);
        return view('admin.applications.show', compact('application'));
    }

    /**
     * Approve an adoption application — atomic transaction.
     */
    public function approve(Request $request, AdoptionApplication $application): RedirectResponse
    {
        $request->validate([
            'review_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_if(!in_array($application->status, ['submitted', 'under_review', 'interview_scheduled']), 400,
            'Application cannot be approved in its current state.');

        DB::transaction(function () use ($request, $application) {
            $oldStatus = $application->status;

            // Update application
            $application->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes'=> $request->review_notes,
            ]);

            // Update pet status
            $application->pet->update(['status' => 'adopted']);

            // Reject all other active applications for this pet
            AdoptionApplication::where('pet_id', $application->pet_id)
                ->where('id', '!=', $application->id)
                ->whereNotIn('status', ['rejected', 'withdrawn', 'completed'])
                ->each(function ($otherApp) {
                    $otherApp->update([
                        'status'           => 'rejected',
                        'reviewed_by'      => auth()->id(),
                        'reviewed_at'      => now(),
                        'rejection_reason' => 'Another applicant was selected for this pet.',
                    ]);

                    // Notify rejected adopters
                    SystemNotification::create([
                        'user_id' => $otherApp->adopter_id,
                        'title'   => 'Application Update',
                        'message' => "Unfortunately, your application for {$otherApp->pet->name} was not selected. Keep browsing — another pet may be perfect for you!",
                        'type'    => 'warning',
                        'link'    => route('adopter.applications.show', $otherApp),
                        'icon'    => 'bi-x-circle',
                    ]);
                });

            // Notify approved adopter
            SystemNotification::create([
                'user_id' => $application->adopter_id,
                'title'   => '🎉 Application Approved!',
                'message' => "Congratulations! Your adoption application for {$application->pet->name} has been approved. Please proceed with the payment and next steps.",
                'type'    => 'success',
                'link'    => route('adopter.applications.show', $application),
                'icon'    => 'bi-check-circle-fill',
            ]);

            // Log
            ActivityLog::log(
                'application_approved',
                "Application {$application->application_number} approved. Pet {$application->pet->name} marked as adopted.",
                $application,
                ['status' => $oldStatus],
                ['status' => 'approved']
            );
        });

        return redirect()->back()->with('success', 'Application approved successfully. The adopter has been notified.');
    }

    /**
     * Reject an adoption application — atomic transaction.
     */
    public function reject(Request $request, AdoptionApplication $application): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        abort_if(!in_array($application->status, ['submitted', 'under_review', 'interview_scheduled']), 400);

        DB::transaction(function () use ($request, $application) {
            $oldStatus = $application->status;

            $application->update([
                'status'           => 'rejected',
                'reviewed_by'      => auth()->id(),
                'reviewed_at'      => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Free pet if no other active applications
            $otherActiveApps = AdoptionApplication::where('pet_id', $application->pet_id)
                ->where('id', '!=', $application->id)
                ->whereNotIn('status', ['rejected', 'withdrawn', 'completed'])
                ->exists();

            if (!$otherActiveApps) {
                $application->pet->update(['status' => 'available']);
            }

            // Notify adopter
            SystemNotification::create([
                'user_id' => $application->adopter_id,
                'title'   => 'Application Status Update',
                'message' => "Your application for {$application->pet->name} was not approved at this time. Reason: {$request->rejection_reason}",
                'type'    => 'danger',
                'link'    => route('adopter.applications.show', $application),
                'icon'    => 'bi-x-circle',
            ]);

            ActivityLog::log(
                'application_rejected',
                "Application {$application->application_number} rejected.",
                $application,
                ['status' => $oldStatus],
                ['status' => 'rejected', 'reason' => $request->rejection_reason]
            );
        });

        return redirect()->back()->with('success', 'Application has been rejected. The adopter has been notified.');
    }

    public function review(AdoptionApplication $application)
{
    DB::transaction(function () use ($application) {
        $application->update([
            'status'      => 'under_review',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        SystemNotification::create([
            'user_id' => $application->adopter_id,
            'title'   => 'Application Under Review',
            'message' => "Your application {$application->application_number} is now being reviewed.",
            'type'    => 'info',
            'link'    => route('adopter.applications.show', $application),
        ]);
    });

    return back()->with('success', 'Application marked as under review.');
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

        // Notify the adopter
        SystemNotification::create([
            'user_id' => auth()->id(),
            'title'   => 'Pet Returned',
            'message' => "You have returned {$application->pet->name}. We hope to find them a new home soon.",
            'type'    => 'info',
            'link'    => route('adopter.applications.show', $application),
        ]);

        // Notify admins
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