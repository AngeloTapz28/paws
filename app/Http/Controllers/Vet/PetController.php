<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function index(Request $request)
    {
        $pets = Pet::with(['category', 'breed', 'medicalRecords'])
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
            )
            ->when($request->status, fn($q) =>
                $q->where('status', $request->status)
            )
            ->whereIn('status', ['available', 'under_treatment', 'pending'])
            ->latest()
            ->paginate(12);

        return view('vet.pets.index', compact('pets'));
    }

    public function show(Pet $pet)
    {
        $pet->load([
            'category', 'breed',
            'medicalRecords.vet',
            'vaccinationRecords.administeredBy',
            'adoptionApplications',
        ]);

        return view('vet.pets.show', compact('pet'));
    }
}