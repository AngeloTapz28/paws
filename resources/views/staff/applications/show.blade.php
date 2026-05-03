@extends('layouts.app')

@section('title', 'Application — ' . $application->application_number)
@section('page-title', 'Application Detail')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('staff.applications.index') }}" class="btn btn-light btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
    <h5 class="mb-0 fw-bold">{{ $application->application_number }}</h5>
    <span class="ms-auto badge bg-info-subtle text-info fs-6 px-3 py-2">
        {{ ucfirst($application->status) }}
    </span>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Pet Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-heart me-2 text-danger"></i>Pet Information
                </h6>
            </div>
            <div class="card-body">
                <h5 class="fw-bold">{{ $application->pet->name ?? '—' }}</h5>
                <div class="text-muted">{{ $application->pet->petCategory->name ?? '' }}</div>
            </div>
        </div>

        {{-- Applicant Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-person me-2 text-primary"></i>Applicant Information
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-md-4 text-muted">Full Name</dt>
                    <dd class="col-md-8">{{ $application->applicant_full_name ?? '—' }}</dd>
                    <dt class="col-md-4 text-muted">Email</dt>
                    <dd class="col-md-8">{{ $application->applicant_email ?? '—' }}</dd>
                    <dt class="col-md-4 text-muted">Phone</dt>
                    <dd class="col-md-8">{{ $application->applicant_phone ?? '—' }}</dd>
                    <dt class="col-md-4 text-muted">Address</dt>
                    <dd class="col-md-8">{{ $application->applicant_address ?? '—' }}</dd>
                    <dt class="col-md-4 text-muted">Housing Type</dt>
                    <dd class="col-md-8">{{ $application->housing_type ?? '—' }}</dd>
                    <dt class="col-md-4 text-muted">Reason for Adopting</dt>
                    <dd class="col-md-8">{{ $application->reason_for_adopting ?? '—' }}</dd>
                    <dt class="col-md-4 text-muted">Experience with Pets</dt>
                    <dd class="col-md-8">{{ $application->experience_with_pets ?? '—' }}</dd>
                    <dt class="col-md-4 text-muted">Additional Notes</dt>
                    <dd class="col-md-8">{{ $application->additional_notes ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-clock-history me-2 text-muted"></i>Timeline
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <div class="small fw-semibold">Application Submitted</div>
                        <div class="text-muted small">
                            {{ $application->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    @if($application->reviewed_at)
                    <div>
                        <div class="small fw-semibold">Under Review</div>
                        <div class="text-muted small">
                            {{ $application->reviewed_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection