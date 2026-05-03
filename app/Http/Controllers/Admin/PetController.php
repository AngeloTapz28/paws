<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pet;
use App\Models\SystemNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PetController extends Controller
{
    public function index(Request $request): View
    {
        $pets = Pet::with(['category', 'breed', 'addedBy'])
            ->when($request->approval, fn ($q) =>
                $request->approval === 'pending'
                    ? $q->where('is_admin_approved', false)
                    : $q->where('is_admin_approved', true))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return view('admin.pets.index', compact('pets'));
    }

    public function approve(Pet $pet): RedirectResponse
    {
        DB::transaction(function () use ($pet) {
            $pet->update([
                'is_admin_approved' => true,
                'listed_at'         => now(),
            ]);

            // Notify the staff member who added it
            SystemNotification::create([
                'user_id' => $pet->added_by,
                'title'   => '✅ Pet Listing Approved',
                'message' => "Your listing for {$pet->name} has been approved and is now public.",
                'type'    => 'success',
                'icon'    => 'bi-check-circle',
            ]);

            ActivityLog::log('pet_approved', "Pet '{$pet->name}' listing approved.", $pet);
        });

        return redirect()->back()->with('success', "Pet '{$pet->name}' approved and published.");
    }

    public function reject(Request $request, Pet $pet): RedirectResponse
    {
        $request->validate(['reason' => ['required', 'string', 'max:500']]);

        DB::transaction(function () use ($request, $pet) {
            $pet->update(['status' => 'not_available']);

            SystemNotification::create([
                'user_id' => $pet->added_by,
                'title'   => 'Pet Listing Rejected',
                'message' => "The listing for {$pet->name} was not approved. Reason: {$request->reason}",
                'type'    => 'danger',
                'icon'    => 'bi-x-circle',
            ]);

            ActivityLog::log('pet_rejected', "Pet '{$pet->name}' listing rejected.", $pet);
        });

        return redirect()->back()->with('success', "Pet listing rejected.");
    }
}