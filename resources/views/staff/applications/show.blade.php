@extends('layouts.app')
@section('title', 'Application — ' . $application->application_number)
@section('page-title', 'Application Detail')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('staff.applications.index') }}">Applications</a></li>
    <li class="breadcrumb-item active">{{ $application->application_number }}</li>
@endsection

@push('styles')
<style>
    /* ── Info label/value ── */
    .info-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); margin-bottom:.2rem; }
    .info-value { font-size:.875rem; font-weight:500; color:var(--text); }

    /* ── Section pill ── */
    .section-pill {
        display:inline-flex; align-items:center; gap:.4rem;
        font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em;
        padding:.22rem .7rem; border-radius:20px;
        background:var(--coral-subtle); color:var(--coral); margin-bottom:.5rem;
    }

    /* ── Pet thumb ── */
    .pet-thumb-lg {
        width: 90px; height: 90px; border-radius: 14px;
        object-fit: cover; border: 2px solid var(--border); flex-shrink: 0;
        transition: transform .2s;
    }
    .pet-thumb-lg:hover { transform: scale(1.05); }
    .pet-thumb-ph {
        width: 90px; height: 90px; border-radius: 14px;
        background: var(--coral-light); display: flex; align-items: center;
        justify-content: center; font-size: 2.5rem; flex-shrink: 0;
    }

    /* ── Action panel ── */
    .action-panel .btn { border-radius: 9px; font-size: .85rem; transition: transform .15s, box-shadow .15s; }
    .action-panel .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }

    /* ── Timeline ── */
    .timeline { position: relative; padding-left: 2rem; }
    .timeline::before {
        content: ''; position: absolute;
        left: .55rem; top: 4px; bottom: 4px;
        width: 2px; background: var(--border); border-radius: 2px;
    }
    .timeline-item { position: relative; margin-bottom: 1.4rem; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-dot {
        position: absolute; left: -1.53rem; top: .2rem;
        width: 14px; height: 14px; border-radius: 50%;
        border: 2.5px solid var(--white);
        transform: scale(0);
        transition: transform .4s cubic-bezier(.34,1.56,.64,1);
    }
    .timeline-dot.revealed { transform: scale(1); }
    .timeline-dot.dot-coral  { background: var(--coral); box-shadow: 0 0 0 2px var(--coral-light); }
    .timeline-dot.dot-gold   { background: var(--gold);  box-shadow: 0 0 0 2px var(--gold-light); }
    .timeline-dot.dot-sage   { background: var(--sage);  box-shadow: 0 0 0 2px var(--sage-light); }
    .timeline-dot.dot-danger { background: #C0392B;      box-shadow: 0 0 0 2px #FEF0EE; }
    .tl-content {
        opacity: 0; transform: translateX(-8px);
        transition: opacity .35s ease, transform .35s ease;
    }
    .timeline-item.revealed .tl-content { opacity: 1; transform: translateX(0); }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown   { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
    @keyframes slideLeft  { from { opacity:0; transform:translateX(-20px); } to { opacity:1; transform:translateX(0); } }
    @keyframes slideRight { from { opacity:0; transform:translateX(20px);  } to { opacity:1; transform:translateX(0); } }
    @keyframes badgePop {
        0%   { transform:scale(.5); opacity:0; }
        70%  { transform:scale(1.12); }
        100% { transform:scale(1);   opacity:1; }
    }

    .header-bar   { animation: fadeDown .4s ease both; }
    .status-badge { animation: badgePop .5s cubic-bezier(.34,1.56,.64,1) .3s both; opacity:0; }

    .card-pet       { opacity:0; animation: slideLeft  .45s ease .2s both; }
    .card-applicant { opacity:0; animation: slideLeft  .45s ease .33s both; }
    .card-actions   { opacity:0; animation: slideRight .45s ease .25s both; }
    .card-timeline  { opacity:0; animation: slideRight .45s ease .4s  both; }
</style>
@endpush

@section('content')

{{-- ── Header bar ── --}}
<div class="header-bar d-flex flex-wrap align-items-center gap-3 mb-4 p-3 rounded-3"
     style="background:var(--white); border:1px solid var(--border); box-shadow:var(--shadow-sm);">
    <a href="{{ route('staff.applications.index') }}" class="btn btn-sm btn-outline-secondary" style="flex-shrink:0;">
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
                'pending'      => 'warning', 'submitted'    => 'warning',
                'reviewing'    => 'info',    'under_review' => 'info',
                'interview'    => 'primary', 'approved'     => 'success',
                'rejected'     => 'danger',  'completed'    => 'success',
                'withdrawn'    => 'secondary','returned'    => 'secondary',
            ];
        @endphp
        <span class="badge bg-{{ $badgeMap[$application->status] ?? 'secondary' }} status-badge"
              style="font-size:.8rem; padding:.45em 1em;">
            {{ ucfirst(str_replace('_',' ',$application->status)) }}
        </span>
    </div>
</div>

<div class="row g-3">

    {{-- ══ LEFT ══ --}}
    <div class="col-lg-8 d-flex flex-column gap-3">

        {{-- Pet Info ── --}}
        <div class="card card-pet">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-heart-fill"></i> Pet</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Pet Information</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <div class="d-flex gap-3 align-items-center">
                    @if($application->pet?->primary_image)
                        <img src="{{ $application->pet->primary_image_url }}" class="pet-thumb-lg" alt="">
                    @else
                        <div class="pet-thumb-ph">🐾</div>
                    @endif
                    <div>
                        <div style="font-size:1.15rem; font-weight:800; color:var(--navy); margin-bottom:.25rem;">
                            {{ $application->pet?->name ?? 'Deleted Pet' }}
                        </div>
                        <div style="font-size:.8rem; color:var(--muted); margin-bottom:.5rem;">
                            {{ $application->pet?->petCategory?->name ?? '' }}
                            @if($application->pet?->breed) · {{ $application->pet->breed->name }} @endif
                        </div>
                        @if($application->pet)
                        <a href="{{ route('staff.pets.show', $application->pet) }}"
                           style="font-size:.75rem; color:var(--coral); text-decoration:none;">
                            <i class="bi bi-eye me-1"></i> View Pet Profile
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Applicant Info ── --}}
        <div class="card card-applicant">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-person-fill"></i> Applicant</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Applicant Information</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <dl class="row mb-0" style="font-size:.875rem; row-gap:.5rem;">
                    <dt class="col-md-4" style="font-weight:600; color:var(--navy-mid);">Full Name</dt>
                    <dd class="col-md-8 mb-0">{{ $application->applicant_full_name ?? '—' }}</dd>

                    <dt class="col-md-4" style="font-weight:600; color:var(--navy-mid);">Email</dt>
                    <dd class="col-md-8 mb-0">{{ $application->applicant_email ?? $application->adopter?->email ?? '—' }}</dd>

                    <dt class="col-md-4" style="font-weight:600; color:var(--navy-mid);">Phone</dt>
                    <dd class="col-md-8 mb-0">{{ $application->applicant_phone ?? $application->adopter?->phone ?? '—' }}</dd>

                    <dt class="col-md-4" style="font-weight:600; color:var(--navy-mid);">Address</dt>
                    <dd class="col-md-8 mb-0">{{ $application->applicant_address ?? $application->adopter?->address ?? '—' }}</dd>

                    <dt class="col-md-4" style="font-weight:600; color:var(--navy-mid);">Housing Type</dt>
                    <dd class="col-md-8 mb-0">{{ ucfirst(str_replace('_',' ',$application->housing_type ?? '—')) }}</dd>

                    <dt class="col-md-4" style="font-weight:600; color:var(--navy-mid);">Reason for Adopting</dt>
                    <dd class="col-md-8 mb-0">{{ $application->reason_for_adopting ?? '—' }}</dd>

                    <dt class="col-md-4" style="font-weight:600; color:var(--navy-mid);">Experience with Pets</dt>
                    <dd class="col-md-8 mb-0">{{ $application->experience_with_pets ?? '—' }}</dd>

                    <dt class="col-md-4" style="font-weight:600; color:var(--navy-mid);">Additional Notes</dt>
                    <dd class="col-md-8 mb-0">{{ $application->additional_notes ?? 'none' }}</dd>
                </dl>
            </div>
        </div>

    </div>

    {{-- ══ RIGHT ══ --}}
    <div class="col-lg-4 d-flex flex-column gap-3">

        {{-- Staff Actions ── --}}
        @if(in_array($application->status, ['pending','submitted','reviewing','under_review','interview']))
        <div class="card card-actions" style="border-top:3px solid var(--coral);">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:var(--gold-light);color:#7A5A1A;"><i class="bi bi-lightning-fill"></i> Actions</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Staff Actions</h6>
            </div>
            <div class="card-body action-panel d-flex flex-column gap-2" style="padding:1.1rem 1.25rem;">

                @if(in_array($application->status, ['pending','submitted']))
                <form action="{{ route('staff.applications.review', $application) }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-secondary w-100" style="border-color:var(--border);">
                        <i class="bi bi-eye me-1" style="color:var(--coral);"></i> Mark as Under Review
                    </button>
                </form>
                @endif

                @if(in_array($application->status, ['pending','submitted','reviewing','under_review']))
                <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#scheduleModal"
                        style="border-color:var(--border);">
                    <i class="bi bi-calendar-check me-1" style="color:var(--sage);"></i> Schedule Interview
                </button>
                @endif

                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="bi bi-check-circle me-1"></i> Approve Application
                </button>

                <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#rejectModal"
                        style="color:#C0392B; border-color:#F5C6C0;">
                    <i class="bi bi-x-circle me-1"></i> Reject Application
                </button>

            </div>
        </div>
        @else
        <div class="card card-actions">
            <div class="card-body text-center py-3" style="color:var(--muted); font-size:.83rem;">
                <i class="bi bi-check2-circle d-block mb-2" style="font-size:1.8rem; opacity:.3;"></i>
                No actions available for this status.
            </div>
        </div>
        @endif

        {{-- Timeline ── --}}
        <div class="card card-timeline">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:rgba(45,49,71,.07);color:var(--navy);"><i class="bi bi-clock-history"></i> History</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Timeline</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <div class="timeline" id="timeline">

                    <div class="timeline-item">
                        <div class="timeline-dot dot-coral"></div>
                        <div class="tl-content">
                            <div style="font-size:.83rem; font-weight:600; color:var(--navy);">Application Submitted</div>
                            <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                                {{ $application->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>

                    @if($application->reviewed_at)
                    <div class="timeline-item">
                        <div class="timeline-dot dot-gold"></div>
                        <div class="tl-content">
                            <div style="font-size:.83rem; font-weight:600; color:var(--navy);">Under Review</div>
                            <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                                {{ $application->reviewed_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($application->interview_at)
                    <div class="timeline-item">
                        <div class="timeline-dot dot-coral"></div>
                        <div class="tl-content">
                            <div style="font-size:.83rem; font-weight:600; color:var(--navy);">Interview Scheduled</div>
                            <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                                {{ \Carbon\Carbon::parse($application->interview_at)->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($application->approved_at)
                    <div class="timeline-item">
                        <div class="timeline-dot dot-sage"></div>
                        <div class="tl-content">
                            <div style="font-size:.83rem; font-weight:600; color:#2D5A3D;">Approved ✓</div>
                            <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                                {{ $application->approved_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($application->rejected_at)
                    <div class="timeline-item">
                        <div class="timeline-dot dot-danger"></div>
                        <div class="tl-content">
                            <div style="font-size:.83rem; font-weight:600; color:#C0392B;">Rejected</div>
                            <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                                {{ $application->rejected_at->format('M d, Y h:i A') }}
                            </div>
                            @if($application->rejection_reason)
                            <div style="font-size:.75rem; color:var(--muted); font-style:italic; margin-top:.3rem;
                                        padding:.45rem .75rem; background:var(--bg); border-radius:7px; border-left:3px solid #C0392B;">
                                {{ $application->rejection_reason }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($application->completed_at)
                    <div class="timeline-item">
                        <div class="timeline-dot dot-sage"></div>
                        <div class="tl-content">
                            <div style="font-size:.83rem; font-weight:600; color:#2D5A3D;">Adoption Completed 🎉</div>
                            <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                                {{ $application->completed_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── Schedule Interview Modal ── --}}
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius); border:none; box-shadow:var(--shadow-md);">
            <form action="{{ route('staff.applications.scheduleInterview', $application) }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--border); padding:1.25rem;">
                    <h5 class="modal-title fw-bold" style="color:var(--navy);">
                        <i class="bi bi-calendar-check me-2" style="color:var(--sage);"></i>Schedule Interview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.25rem;">
                    <div>
                        <label class="form-label">Interview Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="interview_at" class="form-control"
                               min="{{ now()->addHour()->format('Y-m-d\TH:i') }}" required>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border); padding:1rem 1.25rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm" style="background:var(--sage);color:#fff;border:none;">
                        <i class="bi bi-calendar-check me-1"></i> Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Approve Modal ── --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius); border:none; box-shadow:var(--shadow-md);">
            <form action="{{ route('staff.applications.review', $application) }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="approve">
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
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Any instructions for the adopter..."></textarea>
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

{{-- ── Reject Modal ── --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius); border:none; box-shadow:var(--shadow-md);">
            <form action="{{ route('staff.applications.review', $application) }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="reject">
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
                                  placeholder="Explain why this application is being rejected..." required></textarea>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Timeline dots pop → text slides in
    document.querySelectorAll('.timeline-item').forEach((item, i) => {
        setTimeout(() => {
            item.querySelector('.timeline-dot')?.classList.add('revealed');
            setTimeout(() => item.classList.add('revealed'), 100);
        }, 650 + (i * 200));
    });
});
</script>
@endpush