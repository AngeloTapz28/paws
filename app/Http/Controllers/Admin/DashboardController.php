<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\Pet;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_pets'           => Pet::count(),
            'available_pets'       => Pet::where('status', 'available')->count(),
            'pending_pets'         => Pet::where('status', 'pending')->count(),
            'total_adopted'        => Pet::where('status', 'adopted')->count(),
            'treatment_pets'       => Pet::where('status', 'under_treatment')->count(),
            'pending_applications' => AdoptionApplication::whereIn('status', ['submitted', 'under_review'])->count(),
            'total_users'          => User::count(),
            'new_users_month'      => User::whereMonth('created_at', now()->month)->count(),
        ];

        $recentApplications = AdoptionApplication::with(['pet', 'adopter'])
            ->latest()
            ->take(8)
            ->get();

        $pendingPets = Pet::with('category')
            ->where('is_admin_approved', false)
            ->where('is_vet_approved', true)
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentApplications', 'pendingPets'));
    }
}