<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\Payment;
use App\Models\Pet;
use Illuminate\View\View;

class DashboardController extends Controller
{
   public function index(): View
{
    $totalPets            = Pet::count();
    $availablePets        = Pet::where('status', 'available')->count();
    $pendingApplications  = AdoptionApplication::whereIn('status', ['submitted', 'under_review'])->count();
    $totalPayments        = Payment::where('status', 'completed')->count();

    $recentApplications   = AdoptionApplication::with(['pet', 'adopter'])
                                ->whereIn('status', ['submitted', 'under_review'])
                                ->latest()
                                ->take(8)
                                ->get();

    return view('staff.dashboard', compact(
        'totalPets', 'availablePets',
        'pendingApplications', 'totalPayments',
        'recentApplications'
    ));
}
}