@extends('layouts.app')

@section('title', 'Add Medical Record')
@section('page-title', 'Add Medical Record')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-clipboard-pulse me-2 text-primary"></i>New Medical Record
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('vet.pets.medical-records.store', $pet) }}" method="POST">
                    @csrf

                    <div class="row g-4">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Examination Date <span class="text-danger">*</span></label>
                            <input type="date" name="examination_date"
                                   class="form-control @error('examination_date') is-invalid @enderror"
                                   value="{{ old('examination_date', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}" required>
                            @error('examination_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Health Status <span class="text-danger">*</span></label>
                            <select name="health_status" class="form-select @error('health_status') is-invalid @enderror" required>
                                <option value="">Select Status</option>
                                <option value="excellent" @selected(old('health_status') === 'excellent')>Excellent</option>
                                <option value="good" @selected(old('health_status') === 'good')>Good</option>
                                <option value="fair" @selected(old('health_status') === 'fair')>Fair</option>
                                <option value="poor" @selected(old('health_status') === 'poor')>Poor</option>
                                <option value="critical" @selected(old('health_status') === 'critical')>Critical</option>
                            </select>
                            @error('health_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fit for Adoption?</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="fit_for_adoption"
                                       value="1" {{ old('fit_for_adoption') ? 'checked' : '' }}>
                                <label class="form-check-label">Yes, fit for adoption</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Diagnosis</label>
                            <textarea name="diagnosis" class="form-control" rows="2"
                                placeholder="Enter diagnosis...">{{ old('diagnosis') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Treatment</label>
                            <textarea name="treatment" class="form-control" rows="2"
                                placeholder="Enter treatment plan...">{{ old('treatment') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Next Checkup Date</label>
                            <input type="date" name="next_checkup_date" class="form-control"
                                value="{{ old('next_checkup_date') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"
                                placeholder="Additional notes...">{{ old('notes') }}</textarea>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ url()->previous() }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Save Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection