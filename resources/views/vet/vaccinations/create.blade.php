@extends('layouts.app')
@section('title', 'Add Vaccination — ' . $pet->name)
@section('page-title', 'Add Vaccination Record')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('vet.pets.index') }}">Pets</a></li>
    <li class="breadcrumb-item"><a href="{{ route('vet.pets.show', $pet) }}">{{ $pet->name }}</a></li>
    <li class="breadcrumb-item active">Add Vaccination</li>
@endsection

@section('content')

<div class="row g-4">

    {{-- Sidebar --}}
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <img src="{{ $pet->primary_image_url }}"
                 class="rounded-3 mb-3" style="width:100%;height:160px;object-fit:cover;">
            <h6 class="fw-bold mb-1">{{ $pet->name }}</h6>
            <div class="text-muted small mb-2">
                {{ $pet->category->name ?? '—' }}
            </div>
            <span class="badge bg-{{ $pet->is_vaccinated ? 'success' : 'warning text-dark' }}">
                {{ $pet->is_vaccinated ? '✅ Vaccinated' : '⚠️ Not Yet Vaccinated' }}
            </span>
        </div>
    </div>

    {{-- Form --}}
    <div class="col-lg-9">
        <form method="POST" action="{{ route('vet.pets.vaccinations.store', $pet) }}">
            @csrf
            <input type="hidden" name="pet_id" value="{{ $pet->id }}">

            <div class="card border-0 shadow-sm">
                <div class="card-header fw-semibold">
                    <i class="bi bi-shield-plus me-2 text-success"></i>Vaccination Details
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vaccine Name <span class="text-danger">*</span></label>
                            <input type="text" name="vaccine_name"
                                   class="form-control @error('vaccine_name') is-invalid @enderror"
                                   value="{{ old('vaccine_name') }}"
                                   placeholder="e.g. Rabies, DHPP, Bordetella…" required>
                            @error('vaccine_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vaccine Batch / Lot #</label>
                            <input type="text" name="batch_number"
                                   class="form-control @error('batch_number') is-invalid @enderror"
                                   value="{{ old('batch_number') }}"
                                   placeholder="Optional lot number">
                            @error('batch_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date Administered <span class="text-danger">*</span></label>
                            <input type="date" name="date_administered"
                                   class="form-control @error('administered_date') is-invalid @enderror"
                                   value="{{ old('administered_date', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}" required>
                            @error('administered_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Next Due Date</label>
                            <input type="date" name="next_due_date"
                                   class="form-control @error('next_due_date') is-invalid @enderror"
                                   value="{{ old('next_due_date') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            <div class="form-text">Leave blank if no booster is required.</div>
                            @error('next_due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Manufacturer</label>
                            <input type="text" name="manufacturer"
                                   class="form-control @error('manufacturer') is-invalid @enderror"
                                   value="{{ old('manufacturer') }}"
                                   placeholder="e.g. Zoetis, Merck…">
                            @error('manufacturer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dosage / Route</label>
                            <input type="text" name="dosage"
                                   class="form-control @error('dosage') is-invalid @enderror"
                                   value="{{ old('dosage') }}"
                                   placeholder="e.g. 1mL subcutaneous">
                            @error('dosage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" rows="3"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Any observations or additional notes…">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="mark_pet_vaccinated"
                                       id="markVaccinated" value="1" @checked(old('mark_pet_vaccinated', true))>
                                <label class="form-check-label fw-medium" for="markVaccinated">
                                    Mark pet as vaccinated after saving
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('vet.pets.show', $pet) }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-shield-check me-1"></i>Save Vaccination
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection