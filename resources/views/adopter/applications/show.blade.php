@extends('layouts.app')
@section('title', 'Application — ' . $application->application_number)
@section('page-title', 'Application Detail')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('adopter.applications.index') }}">My Applications</a></li>
    <li class="breadcrumb-item active">{{ $application->application_number }}</li>
@endsection

@push('styles')
<style>
    .info-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin-bottom: .2rem; }
    .info-value { font-size: .875rem; font-weight: 500; color: var(--text); }

    .section-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--coral-subtle); color: var(--coral);
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; padding: .22rem .7rem; border-radius: 20px; margin-bottom: .5rem;
    }

    /* Timeline */
    .timeline { position: relative; padding-left: 2rem; }
    .timeline::before {
        content: ''; position: absolute; left: .55rem; top: 4px; bottom: 4px;
        width: 2px; background: var(--border); border-radius: 2px;
    }
    .timeline-item { position: relative; margin-bottom: 1.4rem; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-dot {
        position: absolute; left: -1.53rem; top: .2rem;
        width: 14px; height: 14px; border-radius: 50%;
        border: 2.5px solid var(--white);
    }
    .dot-coral  { background: var(--coral);  box-shadow: 0 0 0 2px var(--coral-light); }
    .dot-sage   { background: var(--sage);   box-shadow: 0 0 0 2px var(--sage-light); }
    .dot-gold   { background: var(--gold);   box-shadow: 0 0 0 2px var(--gold-light); }
    .dot-danger { background: #C0392B;       box-shadow: 0 0 0 2px #FEF0EE; }
    .dot-navy   { background: var(--navy);   box-shadow: 0 0 0 2px rgba(45,49,71,.15); }
</style>
@endpush

@section('content')

{{-- Header bar --}}
<div class="d-flex flex-wrap align-items-center gap-3 mb-4 p-3 rounded-3"
     style="background:var(--white); border:1px solid var(--border); box-shadow:var(--shadow-sm);">
    <a href="{{ route('adopter.applications.index') }}" class="btn btn-sm btn-outline-secondary" style="flex-shrink:0;">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
    <div>
        <div class="fw-bold" style="font-size:1rem; color:var(--navy);">{{ $application->application_number }}</div>
        <div style="font-size:.75rem; color:var(--muted);">
            Submitted {{ $application->created_at->format('F d, Y \a\t h:i A') }}
        </div>
    </div>
    <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
        @php
            $badgeMap = [
                'pending'      => 'warning',
                'submitted'    => 'warning',
                'under_review' => 'info',
                'interview'    => 'primary',
                'approved'     => 'success',
                'rejected'     => 'danger',
                'completed'    => 'success',
                'withdrawn'    => 'secondary',
                'returned'     => 'secondary',
            ];
        @endphp
        <span class="badge bg-{{ $badgeMap[$application->status] ?? 'secondary' }}"
              style="font-size:.8rem; padding:.45em 1em;">
            {{ ucfirst(str_replace('_', ' ', $application->status)) }}
        </span>

        {{-- Withdraw button --}}
        @if(in_array($application->status, ['pending','submitted']))
        <form action="{{ route('adopter.applications.withdraw', $application) }}" method="POST"
              onsubmit="return confirm('Are you sure you want to withdraw this application?')">
            @csrf
            <button class="btn btn-sm btn-outline-secondary" style="color:#C0392B; border-color:#F5C6C0; font-size:.78rem;">
                <i class="bi bi-x-circle me-1"></i> Withdraw
            </button>
        </form>
        @endif

        {{-- Return Pet button --}}
        @if($application->status === 'completed')
        <button type="button" class="btn btn-sm btn-outline-danger"
                data-bs-toggle="modal" data-bs-target="#returnModal"
                style="font-size:.78rem;">
            <i class="bi bi-arrow-return-left me-1"></i> Return Pet
        </button>
        @endif
    </div>
</div>

<div class="row g-3">
    {{-- LEFT --}}
    <div class="col-lg-8 d-flex flex-column gap-3">

        {{-- Pet Info --}}
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-heart-fill"></i> Pet</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Pet You Applied For</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <div class="d-flex gap-4">
                    @if($application->pet?->primary_image)
                        <img src="{{ Storage::url($application->pet?->primary_image) }}"
                             style="width:110px;height:110px;border-radius:var(--radius);object-fit:cover;
                                    border:2px solid var(--border);flex-shrink:0;" alt="">
                    @else
                        <div style="width:110px;height:110px;border-radius:var(--radius);
                                    background:var(--coral-light);display:flex;align-items:center;
                                    justify-content:center;font-size:3rem;flex-shrink:0;">🐾</div>
                    @endif
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1" style="color:var(--navy);">
                            {{ $application->pet?->name ?? 'Deleted Pet' }}
                        </h5>
                        <p style="color:var(--muted); font-size:.83rem; margin-bottom:.75rem;">
                            {{ $application->pet?->petCategory?->name ?? '' }}
                            @if($application->pet?->breed) - {{ $application->pet?->breed?->name }} @endif
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span style="font-size:.73rem;font-weight:600;padding:.25em .75em;border-radius:20px;background:var(--coral-subtle);color:var(--coral);">
                                {{ ucfirst($application->pet?->gender ?? '—') }}
                            </span>
                            <span style="font-size:.73rem;font-weight:600;padding:.25em .75em;border-radius:20px;background:var(--gold-light);color:#7A5A1A;">
                                {{ $application->pet?->age_label ?? '—' }}
                            </span>
                            @if($application->pet?->adoption_fee)
                            <span style="font-size:.73rem;font-weight:600;padding:.25em .75em;border-radius:20px;background:var(--sage-light);color:#2D5A3D;">
                                ₱{{ number_format($application->pet?->adoption_fee, 2) }} fee
                            </span>
                            @endif
                        </div>
                        @if($application->pet)
                        <div class="mt-3">
                            <a href="{{ route('adopter.pets.show', $application->pet) }}"
                               class="btn btn-sm btn-outline-secondary" style="font-size:.78rem;">
                                <i class="bi bi-eye me-1"></i> View Pet Profile
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Your Info --}}
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-person-fill"></i> Your Info</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Your Information</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $application->applicant_full_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $application->applicant_email ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ $application->applicant_phone ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ $application->applicant_address ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Application Answers --}}
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-card-list"></i> Answers</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Your Application Answers</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-label">Housing Type</div>
                        <div class="info-value">{{ ucfirst($application->housing_type ?? '—') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Has Other Pets?</div>
                        <div class="info-value">{{ $application->has_other_pets ? 'Yes' : 'No' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="info-label">Reason for Adopting</div>
                        <div class="info-value">{{ $application->reason_for_adopting ?? '—' }}</div>
                    </div>
                    @if($application->experience_with_pets)
                    <div class="col-12">
                        <div class="info-label">Experience with Pets</div>
                        <div class="info-value">{{ $application->experience_with_pets }}</div>
                    </div>
                    @endif
                    @if($application->additional_notes)
                    <div class="col-12">
                        <div class="info-label">Additional Notes</div>
                        <div class="info-value">{{ $application->additional_notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Return Reason (if returned) --}}
        @if($application->status === 'returned' && $application->return_reason)
        <div class="card" style="border-left: 3px solid #C0392B;">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:#FEF0EE;color:#8B2516;">
                    <i class="bi bi-arrow-return-left"></i> Return Info
                </div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Pet Return Details</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <div class="info-label">Reason for Return</div>
                <div class="info-value">{{ $application->return_reason }}</div>
                @if($application->returned_at)
                <div class="info-label mt-3">Returned On</div>
                <div class="info-value">{{ $application->returned_at->format('F d, Y h:i A') }}</div>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT --}}
    <div class="col-lg-4 d-flex flex-column gap-3">

        {{-- Status Card --}}
        <div class="card" style="border-top: 3px solid var(--coral);">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:var(--gold-light);color:#7A5A1A;"><i class="bi bi-info-circle"></i> Status</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Application Status</h6>
            </div>
            <div class="card-body" style="padding:1.1rem 1.25rem;">
                @php
                    $statusInfo = [
                        'pending'      => ['icon' => 'bi-clock',              'color' => '#7A5A1A',        'bg' => 'var(--gold-light)',   'msg' => 'Your application is waiting to be reviewed.'],
                        'submitted'    => ['icon' => 'bi-send',               'color' => '#7A5A1A',        'bg' => 'var(--gold-light)',   'msg' => 'Your application has been submitted successfully.'],
                        'under_review' => ['icon' => 'bi-eye',                'color' => 'var(--coral-dark)', 'bg' => 'var(--coral-subtle)', 'msg' => 'Our team is currently reviewing your application.'],
                        'interview'    => ['icon' => 'bi-calendar-check',     'color' => 'var(--coral-dark)', 'bg' => 'var(--coral-subtle)', 'msg' => 'You have been scheduled for an interview.'],
                        'approved'     => ['icon' => 'bi-check-circle-fill',  'color' => '#2D5A3D',        'bg' => 'var(--sage-light)',   'msg' => 'Congratulations! Your application has been approved.'],
                        'completed'    => ['icon' => 'bi-house-heart-fill',   'color' => '#2D5A3D',        'bg' => 'var(--sage-light)',   'msg' => 'Adoption completed! Welcome to your new family member. 🎉'],
                        'rejected'     => ['icon' => 'bi-x-circle-fill',      'color' => '#8B2516',        'bg' => '#FEF0EE',            'msg' => 'Unfortunately, your application was not approved this time.'],
                        'withdrawn'    => ['icon' => 'bi-dash-circle',        'color' => '#6B7280',        'bg' => '#F3F4F6',            'msg' => 'You have withdrawn this application.'],
                        'returned'     => ['icon' => 'bi-arrow-return-left',  'color' => '#6B7280',        'bg' => '#F3F4F6',            'msg' => 'You have returned this pet. Thank you for letting us know.'],
                    ];
                    $info = $statusInfo[$application->status] ?? ['icon' => 'bi-question-circle', 'color' => 'var(--muted)', 'bg' => 'var(--bg)', 'msg' => ''];
                @endphp
                <div class="d-flex gap-3 align-items-start p-3 rounded-3" style="background:{{ $info['bg'] }};">
                    <i class="bi {{ $info['icon'] }}" style="font-size:1.4rem; color:{{ $info['color'] }}; flex-shrink:0; margin-top:.1rem;"></i>
                    <div>
                        <div class="fw-bold" style="font-size:.85rem; color:{{ $info['color'] }};">
                            {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                        </div>
                        <div style="font-size:.78rem; color:var(--muted); margin-top:.2rem; line-height:1.5;">
                            {{ $info['msg'] }}
                        </div>
                        @if($application->rejection_reason)
                        <div style="font-size:.78rem; font-style:italic; margin-top:.5rem; color:#8B2516;">
                            "{{ $application->rejection_reason }}"
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:rgba(45,49,71,.07);color:var(--navy);"><i class="bi bi-clock-history"></i> History</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Application Timeline</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot dot-coral"></div>
                        <div style="font-size:.83rem; font-weight:600; color:var(--navy);">Application Submitted</div>
                        <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                            {{ $application->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>

                    @if($application->reviewed_at)
                    <div class="timeline-item">
                        <div class="timeline-dot dot-gold"></div>
                        <div style="font-size:.83rem; font-weight:600; color:var(--navy);">Under Review</div>
                        <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                            {{ $application->reviewed_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    @endif

                    @if($application->approved_at)
                    <div class="timeline-item">
                        <div class="timeline-dot dot-sage"></div>
                        <div style="font-size:.83rem; font-weight:600; color:#2D5A3D;">Approved ✓</div>
                        <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                            {{ $application->approved_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    @endif

                    @if($application->rejected_at)
                    <div class="timeline-item">
                        <div class="timeline-dot dot-danger"></div>
                        <div style="font-size:.83rem; font-weight:600; color:#C0392B;">Rejected</div>
                        <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                            {{ $application->rejected_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    @endif

                    @if($application->completed_at)
                    <div class="timeline-item">
                        <div class="timeline-dot dot-sage"></div>
                        <div style="font-size:.83rem; font-weight:600; color:#2D5A3D;">Adoption Completed 🎉</div>
                        <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                            {{ $application->completed_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    @endif

                    @if($application->returned_at)
                    <div class="timeline-item">
                        <div class="timeline-dot dot-danger"></div>
                        <div style="font-size:.83rem; font-weight:600; color:#C0392B;">Pet Returned</div>
                        <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                            {{ $application->returned_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Return Pet Modal --}}
@if($application->status === 'completed')
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius); border:1px solid var(--border);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color:var(--navy);">
                    <i class="bi bi-arrow-return-left me-2" style="color:#C0392B;"></i>
                    Return Pet
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('adopter.applications.return', $application) }}" method="POST">
                @csrf
                <div class="modal-body pt-2">
                    <p style="font-size:.875rem; color:var(--muted); margin-bottom:1rem;">
                        We're sorry to hear you need to return
                        <strong style="color:var(--navy);">{{ $application->pet?->name ?? 'this pet' }}</strong>.
                        Please let us know the reason so we can help find them a new home.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">
                            Reason for Returning <span class="text-danger">*</span>
                        </label>
                        <textarea name="return_reason" class="form-control" rows="4"
                                  placeholder="Please explain why you are returning the pet..."
                                  required minlength="10"></textarea>
                    </div>
                    <div class="alert alert-warning mb-0" style="font-size:.8rem; border-radius:10px;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        This action cannot be undone. The pet will be listed as
                        <strong>available for adoption</strong> again.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4">
                        <i class="bi bi-arrow-return-left me-1"></i> Confirm Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection