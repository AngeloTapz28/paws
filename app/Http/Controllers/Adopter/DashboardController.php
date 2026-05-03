<?php

namespace App\Http\Controllers\Adopter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\Pet;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $applications = AdoptionApplication::with(['pet.category'])
            ->where('adopter_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_applications' => AdoptionApplication::where('adopter_id', $user->id)->count(),
            'pending'            => AdoptionApplication::where('adopter_id', $user->id)
                                        ->whereIn('status', ['submitted', 'under_review'])->count(),
            'approved'           => AdoptionApplication::where('adopter_id', $user->id)
                                        ->where('status', 'approved')->count(),
            'completed'          => AdoptionApplication::where('adopter_id', $user->id)
                                        ->where('status', 'completed')->count(),
        ];

        $featuredPets = Pet::with('category')
            ->available()
            ->latest()
            ->take(6)
            ->get();

        return view('adopter.dashboard', compact('applications', 'stats', 'featuredPets', 'user'));
    }
}