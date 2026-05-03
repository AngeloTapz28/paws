@extends('layouts.app')
@section('title', 'Application — ' . $application->application_number)
@section('page-title', 'Application Detail')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.applications.index') }}">Applications</a></li>
    <li class="breadcrumb-item active">{{ $application->application_number }}</li>
@endsection

@push('styles')
<style>
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
        border: 2.5px solid var(--white); box-shadow: 0 0 0 2px var(--border);
    }
    .timeline-dot.dot-coral  { background: var(--coral); box-shadow: 0 0 0 2px var(--coral-light); }
    .timeline-dot.dot-sage   { background: var(--sage);  box-shadow: 0 0 0 2px var(--sage-light); }
    .timeline-dot.dot-gold   { background: var(--gold);  box-shadow: 0 0 0 2px var(--gold-light); }
    .timeline-dot.dot-navy   { background: var(--navy);  box-shadow: 0 0 0 2px rgba(45,49,71,.15); }
    .timeline-dot.dot-danger { background: #C0392B;      box-shadow: 0 0 0 2px #FEF0EE; }

    /* Info label pairs */
    .info-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin-bottom: .2rem; }
    .info-value { font-size: .875rem; font-weight: 500; color: var(--text); }

    /* Section header */
    .section-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--coral-subtle); color: var(--coral);
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; padding: .22rem .7rem; border-radius: 20px; margin-bottom: .5rem;
    }

    /* Action buttons in panel */
    .action-panel .btn { border-radius: 9px; font-size: .85rem; }
</style>
@endpush

@section('content')

{{-- Page header bar --}}
<div class="d-flex flex-wrap align-items-center gap-3 mb-4 p-3 rounded-3"
     style="background:var(--white); border:1px solid var(--border); box-shadow:var(--shadow-sm);">
    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary" style="flex-shrink:0;">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
    <div>
        <div class="fw-bold" style="font-size:1rem; color:var(--navy);">{{ $application->application_number }}</div>
        <div style="font-size:.75rem; color:var(--muted);">
            Submitted {{ $application->created_at->format('F d, Y \a\t h:i A') }}
        </div>
    </div>
    <div class="ms-auto">
        @php
            $badgeMap = [
                'pending'      => 'warning',
                'submitted'    => 'info',
                'reviewing'    => 'info',
                'under_review' => 'info',
                'interview'    => 'primary',
                'approved'     => 'success',
                'rejected'     => 'danger',
                'completed'    => 'success',
                'withdrawn'    => 'secondary',
                'cancelled'    => 'secondary',
            ];
        @endphp
        <span class="badge bg-{{ $badgeMap[$application->status] ?? 'secondary' }}"
              style="font-size:.8rem; padding:.45em 1em;">
            {{ ucfirst($application->status) }}
        </span>
    </div>
</div>

<div class="row g-3">

    {{-- LEFT COLUMN --}}
    <div class="col-lg-8 d-flex flex-column gap-3">

        {{-- Pet Info --}}
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-heart-fill"></i> Pet</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Pet Information</h6>
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
                        <h5 class="fw-bold mb-1" style="color:var(--navy);">{{ $application->pet?->name ?? 'Deleted Pet' }}</h5>
                        <div style="color:var(--muted);font-size:.83rem;margin-bottom:.75rem;">
                            {{ $application->pet?->petCategory?->name ?? '' }}
                            @if($application->pet?->breed) &middot; {{ $application->pet?->breed?->name }} @endif
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span style="font-size:.73rem;font-weight:600;padding:.25em .75em;border-radius:20px;background:var(--coral-subtle);color:var(--coral);">
                                {{ ucfirst($application->pet?->gender ?? '—') }}
                            </span>
                            <span style="font-size:.73rem;font-weight:600;padding:.25em .75em;border-radius:20px;background:var(--gold-light);color:#7A5A1A;">
                                Age: {{ $application->pet?->age_label ?? '—' }}
                            </span>
                            <span style="font-size:.73rem;font-weight:600;padding:.25em .75em;border-radius:20px;background:var(--sage-light);color:#2D5A3D;">
                                ₱{{ number_format($application->pet?->adoption_fee, 2) }} fee
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Adopter Info --}}
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-person-fill"></i> Adopter</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Adopter Information</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $application->applicant_full_name ?? $application->adopter->full_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $application->applicant_email ?? $application->adopter->email ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ $application->applicant_phone ?? $application->adopter->phone ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ $application->applicant_address ?? $application->adopter->address ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Application Answers --}}
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-card-list"></i> Answers</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Application Answers</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-label">Housing Type</div>
                        <div class="info-value">{{ $application->housing_type ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Has Other Pets?</div>
                        <div class="info-value">{{ $application->has_other_pets ? 'Yes' : 'No' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="info-label">Reason for Adopting</div>
                        <div class="info-value">{{ $application->reason_for_adopting ?? '—' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="info-label">Experience with Pets</div>
                        <div class="info-value">{{ $application->experience_with_pets ?? '—' }}</div>
                    </div>
                    @if($application->additional_notes)
                    <div class="col-12">
                        <div class="info-label">Additional Notes</div>
                        <div class="info-value">{{ $application->additional_notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Payments --}}
        @if($application->payments->count())
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:var(--sage-light);color:#2D5A3D;"><i class="bi bi-cash-stack"></i> Payments</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Payment Records</h6>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="padding-left:1.25rem;">Reference</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($application->payments as $pay)
                        <tr>
                            <td style="padding-left:1.25rem; font-size:.85rem; font-weight:600; color:var(--navy);">
                                {{ $pay->reference_number ?? '—' }}
                            </td>
                            <td style="font-size:.875rem; font-weight:700; color:var(--coral);">
                                ₱{{ number_format($pay->amount, 2) }}
                            </td>
                            <td style="font-size:.83rem; color:var(--muted);">{{ ucfirst($pay->payment_method ?? '—') }}</td>
                            <td style="font-size:.8rem; color:var(--muted);">{{ $pay->created_at->format('M d, Y') }}</td>
                            <td>
                                <span style="font-size:.7rem; font-weight:700; padding:.28em .75em; border-radius:20px;
                                    background: {{ $pay->status === 'completed' ? 'var(--sage-light)' : 'var(--gold-light)' }};
                                    color: {{ $pay->status === 'completed' ? '#2D5A3D' : '#7A5A1A' }};">
                                    {{ ucfirst($pay->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-lg-4 d-flex flex-column gap-3">

        {{-- Action Panel --}}
        @if(in_array($application->status, ['pending','submitted','reviewing','under_review','interview']))
        <div class="card" style="border-top: 3px solid var(--coral);">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:var(--gold-light);color:#7A5A1A;"><i class="bi bi-lightning-fill"></i> Actions</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Review Actions</h6>
            </div>
            <div class="card-body action-panel d-flex flex-column gap-2" style="padding:1.1rem 1.25rem;">

                @if(in_array($application->status, ['pending', 'submitted']))
                <form action="{{ route('admin.applications.review', $application) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline-secondary w-100" style="border-color:var(--border);">
                        <i class="bi bi-eye me-1" style="color:var(--coral);"></i> Mark as Under Review
                    </button>
                </form>
                @endif

                @if(in_array($application->status, ['pending','submitted','reviewing','under_review','interview']))
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="bi bi-check-circle me-1"></i> Approve Application
                </button>
                @endif

                <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#rejectModal"
                        style="color:#C0392B; border-color:#F5C6C0;">
                    <i class="bi bi-x-circle me-1"></i> Reject Application
                </button>
            </div>
        </div>
        @endif

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
                        @if($application->rejection_reason)
                        <div style="font-size:.75rem; color:var(--muted); font-style:italic; margin-top:.3rem; padding:.5rem .75rem; background:var(--bg); border-radius:7px; border-left:3px solid #C0392B;">
                            {{ $application->rejection_reason }}
                        </div>
                        @endif
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
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius); border:none; box-shadow:var(--shadow-md);">
            <form action="{{ route('admin.applications.approve', $application) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header" style="border-bottom:1px solid var(--border); padding:1.25rem;">
                    <h5 class="modal-title fw-bold" style="color:var(--navy);">
                        <i class="bi bi-check-circle-fill me-2" style="color:var(--sage);"></i>Approve Application
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.25rem;">
                    <p style="font-size:.875rem; color:var(--muted); margin-bottom:1rem;">
                        Approving will notify the adopter and mark the pet as reserved.
                    </p>
                    <div>
                        <label class="form-label">Notes <span style="color:var(--muted);">(optional)</span></label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Any special instructions for the adopter…"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border); padding:1rem 1.25rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check-circle me-1"></i> Confirm Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius); border:none; box-shadow:var(--shadow-md);">
            <form action="{{ route('admin.applications.reject', $application) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header" style="border-bottom:1px solid var(--border); padding:1.25rem;">
                    <h5 class="modal-title fw-bold" style="color:#C0392B;">
                        <i class="bi bi-x-circle-fill me-2"></i>Reject Application
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.25rem;">
                    <div>
                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3"
                                  placeholder="Explain why this application is being rejected…" required></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border); padding:1rem 1.25rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm" style="background:#C0392B;color:#fff;border:none;">
                        <i class="bi bi-x-circle me-1"></i> Reject Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection