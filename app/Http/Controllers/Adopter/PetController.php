<?php

namespace App\Http\Controllers\Adopter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\Pet;
use App\Models\PetCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PetController extends Controller
{
    public function index(Request $request): View
    {
        $pets = Pet::with(['category', 'breed'])
            ->available()
            ->when($request->search, fn ($q) => $q->search($request->search))
            ->when($request->category, fn ($q) => $q->where('pet_category_id', $request->category))
            ->when($request->size, fn ($q) => $q->where('size', $request->size))
            ->when($request->gender, fn ($q) => $q->where('gender', $request->gender))
            ->when($request->vaccinated, fn ($q) => $q->where('is_vaccinated', true))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = PetCategory::where('is_active', true)->get();

        return view('adopter.pets.index', compact('pets', 'categories'));
    }

    public function show(Pet $pet): View
    {
        abort_if($pet->status !== 'available' || !$pet->is_admin_approved, 404);

        $pet->load(['category', 'breed', 'vaccinationRecords']);

        $existingApplication = AdoptionApplication::where('pet_id', $pet->id)
            ->where('adopter_id', auth()->id())
            ->whereNotIn('status', ['rejected', 'withdrawn'])
            ->first();

        return view('adopter.pets.show', compact('pet', 'existingApplication'));
    }
}