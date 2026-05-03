<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalRecordRequest;
use App\Models\ActivityLog;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MedicalRecordController extends Controller
{
    public function index(Pet $pet): View
    {
        $records = $pet->medicalRecords()->with('vet')->latest('examination_date')->get();
        return view('vet.medical-records.index', compact('pet', 'records'));
    }

    public function create(Pet $pet): View
    {
        return view('vet.medical-records.create', compact('pet'));
    }

    public function store(StoreMedicalRecordRequest $request, Pet $pet): RedirectResponse
    {
        DB::transaction(function () use ($request, $pet) {
            $data = $request->validated();
            $data['pet_id'] = $pet->id;
            $data['vet_id'] = auth()->id();

            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment')
                    ->store('medical-records', 'public');
            }

            $record = MedicalRecord::create($data);

            // Update pet vaccination status if applicable
            if ($data['fit_for_adoption'] ?? false) {
                $pet->update(['is_vet_approved' => true, 'vet_approved_by' => auth()->id()]);
            }

            // If NOT fit, notify admin/staff
            if (!($data['fit_for_adoption'] ?? true)) {
                User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'staff']))->get()
                    ->each(function ($staff) use ($pet) {
                        SystemNotification::create([
                            'user_id' => $staff->id,
                            'title'   => '⚠️ Pet Health Alert',
                            'message' => "{$pet->name} has been marked as NOT fit for adoption. Please review.",
                            'type'    => 'warning',
                            'link'    => route('vet.pets.show', $pet),
                            'icon'    => 'bi-heart-pulse',
                        ]);
                    });
            }

            ActivityLog::log('medical_record_added', "Medical record added for {$pet->name}.", $record);
        });

        return redirect()->route('vet.pets.show', $pet)
            ->with('success', 'Medical record saved successfully.');
    }

    public function show(MedicalRecord $medicalRecord): View
    {
        $medicalRecord->load(['pet', 'vet']);
        return view('vet.medical-records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord): View
    {
        $medicalRecord->load('pet');
        return view('vet.medical-records.edit', compact('medicalRecord'));
    }

    public function update(StoreMedicalRecordRequest $request, MedicalRecord $medicalRecord): RedirectResponse
    {
        DB::transaction(function () use ($request, $medicalRecord) {
            $data = $request->validated();

            if ($request->hasFile('attachment')) {
                if ($medicalRecord->attachment) {
                    Storage::disk('public')->delete($medicalRecord->attachment);
                }
                $data['attachment'] = $request->file('attachment')->store('medical-records', 'public');
            }

            $medicalRecord->update($data);
            ActivityLog::log('medical_record_updated', "Medical record updated for {$medicalRecord->pet->name}.", $medicalRecord);
        });

        return redirect()->route('vet.pets.show', $medicalRecord->pet)
            ->with('success', 'Medical record updated.');
    }
}