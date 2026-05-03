<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePetRequest;
use App\Models\ActivityLog;
use App\Models\Breed;
use App\Models\Pet;
use App\Models\PetCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PetController extends Controller
{
    public function index(Request $request): View
    {
        $pets = Pet::with(['category', 'breed', 'addedBy'])
            ->when($request->search, fn ($q) => $q->search($request->search))
            ->when($request->category, fn ($q) => $q->where('pet_category_id', $request->category))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = PetCategory::where('is_active', true)->get();

        return view('staff.pets.index', compact('pets', 'categories'));
    }

    public function create(): View
    {
        $categories = PetCategory::where('is_active', true)->get();
        $breeds     = Breed::where('is_active', true)->get();
        return view('staff.pets.create', compact('categories', 'breeds'));
    }

    public function store(StorePetRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['added_by'] = auth()->id();

            // Handle primary image upload
            if ($request->hasFile('primary_image')) {
                $data['primary_image'] = $request->file('primary_image')
                    ->store('pets', 'public');
            }

            // Handle additional images
            if ($request->hasFile('images')) {
                $data['images'] = collect($request->file('images'))
                    ->map(fn ($img) => $img->store('pets', 'public'))
                    ->toArray();
            }

            // Cast checkboxes to bool
            $data['is_vaccinated']  = $request->boolean('is_vaccinated');
            $data['is_neutered']    = $request->boolean('is_neutered');
            $data['is_microchipped']= $request->boolean('is_microchipped');

            $pet = Pet::create($data);

            ActivityLog::log('pet_created', "New pet '{$pet->name}' added to the system.", $pet);
        });

        return redirect()->route('staff.pets.index')
            ->with('success', 'Pet added successfully and pending admin approval.');
    }

    public function show(Pet $pet): View
    {
        $pet->load([
            'category', 'breed', 'addedBy', 'vetApprovedBy',
            'medicalRecords.vet', 'vaccinationRecords.administeredBy',
            'adoptionApplications.adopter',
        ]);
        return view('staff.pets.show', compact('pet'));
    }

    public function edit(Pet $pet): View
    {
        $categories = PetCategory::where('is_active', true)->get();
        $breeds     = Breed::where('is_active', true)
            ->when($pet->pet_category_id, fn ($q) => $q->where('pet_category_id', $pet->pet_category_id))
            ->get();
        return view('staff.pets.edit', compact('pet', 'categories', 'breeds'));
    }

    public function update(StorePetRequest $request, Pet $pet): RedirectResponse
    {
        DB::transaction(function () use ($request, $pet) {
            $data = $request->validated();

            // Handle primary image upload
            if ($request->hasFile('primary_image')) {
                if ($pet->primary_image) {
                    Storage::disk('public')->delete($pet->primary_image);
                }
                $data['primary_image'] = $request->file('primary_image')
                    ->store('pets', 'public');
            }

            // Handle additional images
            if ($request->hasFile('images')) {
                if ($pet->images) {
                    foreach ($pet->images as $img) {
                        Storage::disk('public')->delete($img);
                    }
                }
                $data['images'] = collect($request->file('images'))
                    ->map(fn ($img) => $img->store('pets', 'public'))
                    ->toArray();
            }

            $data['is_vaccinated']  = $request->boolean('is_vaccinated');
            $data['is_neutered']    = $request->boolean('is_neutered');
            $data['is_microchipped']= $request->boolean('is_microchipped');

            $oldData = $pet->only(['name', 'status', 'adoption_fee']);
            $pet->update($data);

            ActivityLog::log('pet_updated', "Pet '{$pet->name}' details updated.", $pet, $oldData, $pet->fresh()->only(['name', 'status', 'adoption_fee']));
        });

        return redirect()->route('staff.pets.show', $pet)
            ->with('success', 'Pet updated successfully.');
    }

    public function destroy(Pet $pet): RedirectResponse
    {
        DB::transaction(function () use ($pet) {
            if ($pet->primary_image) {
                Storage::disk('public')->delete($pet->primary_image);
            }
            $pet->delete();
            ActivityLog::log('pet_deleted', "Pet '{$pet->name}' removed from system.", $pet);
        });

        return redirect()->route('staff.pets.index')
            ->with('success', 'Pet removed successfully.');
    }

    public function updateStatus(Request $request, Pet $pet): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:available,pending,under_treatment,not_available,quarantine'],
        ]);

        DB::transaction(function () use ($request, $pet) {
            $oldStatus = $pet->status;
            $pet->update(['status' => $request->status]);
            ActivityLog::log('pet_status_changed', "Pet '{$pet->name}' status changed from {$oldStatus} to {$request->status}.", $pet);
        });

        return redirect()->back()->with('success', 'Pet status updated.');
    }
}