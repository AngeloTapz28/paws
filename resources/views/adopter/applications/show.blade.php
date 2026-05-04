@extends('layouts.app')
@section('title', 'Application — ' . $application->application_number)
@section('page-title', 'Application Detail')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('adopter.applications.index') }}">My Applications</a></li>
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

    /* ── Pet image ── */
    .pet-thumb-lg {
        width: 90px; height: 90px; border-radius: 14px;
        object-fit: cover; border: 2px solid var(--border);
        flex-shrink: 0; transition: transform .2s;
    }
    .pet-thumb-lg:hover { transform: scale(1.05); }
    .pet-thumb-ph {
        width: 90px; height: 90px; border-radius: 14px;
        background: var(--coral-light); display: flex; align-items: center;
        justify-content: center; font-size: 2.5rem; flex-shrink: 0;
    }

    /* ── Tags ── */
    .pet-tag {
        font-size: .7rem; font-weight: 600; padding: .25em .7em;
        border-radius: 20px; display: inline-block;
    }
    .tag-gender { background: var(--coral-subtle); color: var(--coral); }
    .tag-fee    { background: var(--sage-light);   color: #2D5A3D; }

    /* ── Status card ── */
    .status-info-box {
        border-radius: var(--radius-sm); padding: 1rem 1.1rem;
        display: flex; gap: .75rem; align-items: flex-start;
    }
    .sib-icon {
        width: 36px; height: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .sib-title { font-size: .875rem; font-weight: 700; margin-bottom: .2rem; }
    .sib-msg   { font-size: .8rem; line-height: 1.5; }

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
    .timeline-dot.dot-muted  { background: var(--muted); box-shadow: 0 0 0 2px #F3F4F6; }
    .tl-content {
        opacity: 0; transform: translateX(-8px);
        transition: opacity .35s ease, transform .35s ease;
    }
    .timeline-item.revealed .tl-content { opacity: 1; transform: translateX(0); }

    /* ── Withdraw / Return buttons ── */
    .btn-withdraw {
        font-size: .78rem; color: #8B2516; border: 1.5px solid #F5C6C0;
        background: #FEF0EE; border-radius: 20px; padding: .35rem .9rem;
        transition: all .15s; cursor: pointer;
    }
    .btn-withdraw:hover { background: #C0392B; color: #fff; border-color: #C0392B; }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown   { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
    @keyframes slideLeft  { from { opacity:0; transform:translateX(-20px); } to { opacity:1; transform:translateX(0); } }
    @keyframes slideRight { from { opacity:0; transform:translateX(20px);  } to { opacity:1; transform:translateX(0); } }
    @keyframes fadeUp     { from { opacity:0; transform:translateY(14px);  } to { opacity:1; transform:translateY(0); } }
    @keyframes badgePop {
        0%   { transform:scale(.5); opacity:0; }
        70%  { transform:scale(1.12); }
        100% { transform:scale(1);   opacity:1; }
    }

    .header-bar   { animation: fadeDown .4s ease both; }
    .status-badge { animation: badgePop .5s cubic-bezier(.34,1.56,.64,1) .3s both; opacity:0; }

    .card-pet     { opacity:0; animation: slideLeft  .45s ease .2s both; }
    .card-info    { opacity:0; animation: slideLeft  .45s ease .32s both; }
    .card-answers { opacity:0; animation: slideLeft  .45s ease .44s both; }
    .card-return  { opacity:0; animation: slideLeft  .45s ease .56s both; }

    .card-status   { opacity:0; animation: slideRight .45s ease .25s both; }
    .card-timeline { opacity:0; animation: slideRight .45s ease .4s both; }

    .timeline-item .tl-content { opacity:0; }
</style>
@endpush

@section('content')

{{-- ── Header bar ── --}}
<div class="header-bar d-flex align-items-center gap-3 mb-4 p-3 rounded-3"
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
        <span class="badge bg-{{ $badgeMap[$application->status] ?? 'secondary' }} status-badge"
              style="font-size:.8rem; padding:.45em 1em;">
            {{ ucfirst(str_replace('_', ' ', $application->status)) }}
        </span>

        {{-- Withdraw button ── --}}
        @if(in_array($application->status, ['pending','submitted']))
        <form action="{{ route('adopter.applications.withdraw', $application) }}" method="POST"
              onsubmit="return confirm('Withdraw this application?')">
            @csrf
            <button type="submit" class="btn-withdraw">
                <i class="bi bi-x-circle me-1"></i> Withdraw
            </button>
        </form>
        @endif

        {{-- Return pet button ── --}}
        @if($application->status === 'completed')
        <button class="btn-withdraw" data-bs-toggle="modal" data-bs-target="#returnModal"
                style="border-color:var(--border); background:var(--bg); color:var(--muted);">
            <i class="bi bi-arrow-return-left me-1"></i> Return Pet
        </button>
        @endif
    </div>
</div>

<div class="row g-3">

    {{-- ══ LEFT COLUMN ══ --}}
    <div class="col-lg-8 d-flex flex-column gap-3">

        {{-- Pet You Applied For ── --}}
        <div class="card card-pet">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-heart-fill"></i> Pet</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Pet You Applied For</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <div class="d-flex gap-3 align-items-center">
                    @if($application->pet?->primary_image)
                        <img src="{{ $application->pet->primary_image_url }}" class="pet-thumb-lg" alt="">
                    @else
                        <div class="pet-thumb-ph">🐾</div>
                    @endif
                    <div>
                        <div style="font-size:1.1rem; font-weight:800; color:var(--navy); margin-bottom:.2rem;">
                            {{ $application->pet?->name ?? 'Deleted Pet' }}
                        </div>
                        <div style="font-size:.8rem; color:var(--muted); margin-bottom:.6rem;">
                            {{ $application->pet?->petCategory?->name ?? '' }}
                            @if($application->pet?->breed) · {{ $application->pet->breed->name }} @endif
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($application->pet?->gender)
                            <span class="pet-tag tag-gender">{{ ucfirst($application->pet->gender) }}</span>
                            @endif
                            <span class="pet-tag" style="background:rgba(45,49,71,.07); color:var(--navy);">—</span>
                            @if($application->pet?->adoption_fee)
                            <span class="pet-tag tag-fee">₱{{ number_format($application->pet->adoption_fee, 2) }} fee</span>
                            @endif
                        </div>
                        @if($application->pet)
                        <a href="{{ route('adopter.pets.show', $application->pet) }}"
                           style="font-size:.75rem; color:var(--coral); text-decoration:none; display:inline-flex; align-items:center; gap:.3rem; margin-top:.5rem;">
                            View Pet Profile <i class="bi bi-arrow-right" style="font-size:.7rem;"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Your Information ── --}}
        <div class="card card-info">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-person-fill"></i> Your Info</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Your Information</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $application->applicant_full_name ?? auth()->user()->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $application->applicant_email ?? auth()->user()->email }}</div>
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

        {{-- Application Answers ── --}}
        <div class="card card-answers">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-card-list"></i> Answers</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Your Application Answers</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-label">Housing Type</div>
                        <div class="info-value">{{ ucfirst(str_replace('_', ' ', $application->housing_type ?? '—')) }}</div>
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

        {{-- Return info ── --}}
        @if($application->status === 'returned' && $application->return_reason)
        <div class="card card-return" style="border-left: 3px solid #C0392B;">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:#FEF0EE;color:#8B2516;"><i class="bi bi-arrow-return-left"></i> Return Info</div>
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

    {{-- ══ RIGHT COLUMN ══ --}}
    <div class="col-lg-4 d-flex flex-column gap-3">

        {{-- Application Status ── --}}
        <div class="card card-status" style="border-top: 3px solid var(--coral);">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:var(--gold-light);color:#7A5A1A;"><i class="bi bi-info-circle"></i> Status</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Application Status</h6>
            </div>
            <div class="card-body" style="padding:1.1rem 1.25rem;">
                @php
                    $statusInfo = [
                        'pending'      => ['icon'=>'bi-clock',             'color'=>'#7A5A1A',    'bg'=>'var(--gold-light)',   'msg'=>'Your application is waiting to be reviewed.'],
                        'submitted'    => ['icon'=>'bi-send',              'color'=>'#7A5A1A',    'bg'=>'var(--gold-light)',   'msg'=>'Your application has been submitted successfully.'],
                        'under_review' => ['icon'=>'bi-eye',               'color'=>'var(--coral-dark)', 'bg'=>'var(--coral-subtle)', 'msg'=>'Our team is currently reviewing your application.'],
                        'interview'    => ['icon'=>'bi-calendar-check',    'color'=>'var(--coral-dark)', 'bg'=>'var(--coral-subtle)', 'msg'=>'You have been scheduled for an interview.'],
                        'approved'     => ['icon'=>'bi-check-circle-fill', 'color'=>'#2D5A3D',    'bg'=>'var(--sage-light)',   'msg'=>'Congratulations! Your application has been approved.'],
                        'completed'    => ['icon'=>'bi-house-heart-fill',  'color'=>'#2D5A3D',    'bg'=>'var(--sage-light)',   'msg'=>'Adoption completed! Welcome your new family member. 🎉'],
                        'rejected'     => ['icon'=>'bi-x-circle-fill',     'color'=>'#8B2516',    'bg'=>'#FEF0EE',            'msg'=>'Unfortunately, your application was not approved this time.'],
                        'withdrawn'    => ['icon'=>'bi-dash-circle',       'color'=>'#6B7280',    'bg'=>'#F3F4F6',            'msg'=>'You have withdrawn this application.'],
                        'returned'     => ['icon'=>'bi-arrow-return-left', 'color'=>'#6B7280',    'bg'=>'#F3F4F6',            'msg'=>'You have returned this pet. Thank you for letting us know.'],
                    ];
                    $info = $statusInfo[$application->status] ?? ['icon'=>'bi-info-circle','color'=>'var(--muted)','bg'=>'var(--bg)','msg'=>'Status unknown.'];
                @endphp
                <div class="status-info-box" style="background:{{ $info['bg'] }}; color:{{ $info['color'] }};">
                    <div class="sib-icon" style="background:rgba(255,255,255,.5);">
                        <i class="bi {{ $info['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="sib-title">{{ ucfirst(str_replace('_',' ',$application->status)) }}</div>
                        <div class="sib-msg">{{ $info['msg'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Application Timeline ── --}}
        <div class="card card-timeline">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:rgba(45,49,71,.07);color:var(--navy);"><i class="bi bi-clock-history"></i> History</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Application Timeline</h6>
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
                            <div style="font-size:.83rem; font-weight:600; color:#C0392B;">Not Approved</div>
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

                    @if($application->status === 'returned')
                    <div class="timeline-item">
                        <div class="timeline-dot dot-muted"></div>
                        <div class="tl-content">
                            <div style="font-size:.83rem; font-weight:600; color:var(--muted);">Pet Returned</div>
                            <div style="font-size:.75rem; color:var(--muted); margin-top:.1rem;">
                                {{ $application->returned_at?->format('M d, Y h:i A') ?? 'Date not recorded' }}
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>

{{-- Return Modal ── --}}
@if($application->status === 'completed')
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--radius); border:none; box-shadow:var(--shadow-md);">
            <form action="{{ route('adopter.applications.return', $application) }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--border); padding:1.25rem;">
                    <h5 class="modal-title fw-bold" style="color:var(--navy);">
                        <i class="bi bi-arrow-return-left me-2" style="color:var(--coral);"></i>Return Pet
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.25rem;">
                    <p style="font-size:.875rem; color:var(--muted); margin-bottom:1rem;">
                        We're sorry to hear that. Please let us know why you're returning {{ $application->pet?->name }}.
                    </p>
                    <div>
                        <label class="form-label">Reason for Return <span class="text-danger">*</span></label>
                        <textarea name="return_reason" class="form-control" rows="3"
                                  placeholder="Please explain your reason..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border); padding:1rem 1.25rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm" style="background:#C0392B;color:#fff;border:none;">
                        <i class="bi bi-arrow-return-left me-1"></i> Confirm Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Stagger timeline: dot pops → text slides in
    document.querySelectorAll('.timeline-item').forEach((item, i) => {
        setTimeout(() => {
            item.querySelector('.timeline-dot')?.classList.add('revealed');
            setTimeout(() => item.classList.add('revealed'), 100);
        }, 650 + (i * 200));
    });
});
</script>
@endpush