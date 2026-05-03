<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\VaccinationRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VaccinationController extends Controller
{
    public function create(Pet $pet): View
    {
        return view('vet.vaccinations.create', compact('pet'));
    }

    public function store(Request $request, Pet $pet): RedirectResponse
    {
        $request->validate([
            'vaccine_name'      => ['required', 'string', 'max:200'],
            'manufacturer'      => ['nullable', 'string', 'max:200'],
            'batch_number'      => ['nullable', 'string', 'max:100'],
            'date_administered' => ['required', 'date', 'before_or_equal:today'],
            'next_due_date'     => ['nullable', 'date', 'after:today'],
            'is_booster'        => ['boolean'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $pet) {
            VaccinationRecord::create([
                'pet_id'            => $pet->id,
                'vaccine_name'      => $request->vaccine_name,
                'manufacturer'      => $request->manufacturer,
                'batch_number'      => $request->batch_number,
                'date_administered' => $request->date_administered,
                'next_due_date'     => $request->next_due_date,
                'notes'             => $request->notes,
                'administered_by'   => auth()->id(),
                'is_booster'        => $request->boolean('is_booster'),
            ]);

            if (!$pet->is_vaccinated) {
                $pet->update(['is_vaccinated' => true]);
            }
        });

        return redirect()->route('vet.pets.show', $pet)
            ->with('success', 'Vaccination record added.');
    }

    public function destroy(VaccinationRecord $vaccination): RedirectResponse
    {
        $petId = $vaccination->pet_id;
        $vaccination->delete();
        return redirect()->route('vet.pets.show', $petId)
            ->with('success', 'Vaccination record removed.');
    }
}