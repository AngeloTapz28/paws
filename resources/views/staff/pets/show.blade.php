@extends('layouts.app')

@section('title', $pet->name)
@section('page-title', 'Pet Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('staff.pets.index') }}">All Pets</a></li>
    <li class="breadcrumb-item active">{{ $pet->name }}</li>
@endsection

@push('styles')
<style>
    /* ── Info label pairs ── */
    .info-label {
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; color: var(--muted); margin-bottom: .15rem;
    }
    .info-value { font-size: .875rem; font-weight: 500; color: var(--text); }

    /* ── Info row in dl ── */
    .info-row {
        display: flex; padding: .6rem 0;
        border-bottom: 1px solid var(--border);
        opacity: 0; /* JS reveals */
    }
    .info-row:last-child { border-bottom: none; }
    .info-row dt { width: 38%; font-size: .8rem; font-weight: 600; color: var(--muted); }
    .info-row dd { flex: 1; font-size: .875rem; color: var(--text); margin: 0; font-weight: 500; }
    .info-row.visible { animation: rowFadeIn .35s ease both; }

    /* ── Pet image ── */
    .pet-photo {
        width: 100%; height: 220px; object-fit: cover;
        border-radius: var(--radius-sm);
        transition: transform .35s ease, box-shadow .35s ease;
        cursor: zoom-in;
    }
    .pet-photo:hover { transform: scale(1.03); box-shadow: var(--shadow-md); }

    /* ── Vaccination/neuter badges ── */
    .health-badge {
        font-size: .72rem; font-weight: 600; padding: .3em .8em;
        border-radius: 20px; display: inline-flex; align-items: center; gap: .3rem;
    }
    .badge-vaccinated { background: var(--sage-light); color: #2D5A3D; }
    .badge-neutered   { background: rgba(111,66,193,.1); color: #6f42c1; }

    /* ── Status badge (top right) ── */
    .status-badge-top {
        font-size: .8rem; font-weight: 700; padding: .45em 1.1em;
        border-radius: 20px; letter-spacing: .04em;
    }
    .s-available  { background: var(--sage-light);  color: #2D5A3D; }
    .s-adopted    { background: var(--gold-light);  color: #7A5A1A; }
    .s-pending    { background: rgba(45,49,71,.08); color: var(--navy); }
    .s-treatment  { background: #FEF0EE;            color: #8B2516; }

    /* ── Section pill ── */
    .section-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--coral-subtle); color: var(--coral);
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; padding: .22rem .7rem; border-radius: 20px;
        margin-bottom: .5rem;
    }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-22px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(22px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes rowFadeIn {
        from { opacity: 0; transform: translateX(-10px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes badgePop {
        0%   { transform: scale(.5); opacity: 0; }
        70%  { transform: scale(1.12); }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes imageReveal {
        from { opacity: 0; transform: scale(.96); }
        to   { opacity: 1; transform: scale(1); }
    }

    /* Header bar */
    .header-bar { animation: fadeDown .4s ease both; }

    /* Status badge pops */
    .status-badge-top { animation: badgePop .5s cubic-bezier(.34,1.56,.64,1) .3s both; opacity: 0; }

    /* Left card slides in */
    .card-left  { opacity: 0; animation: slideInLeft  .45s ease .15s both; }

    /* Right card slides in */
    .card-right { opacity: 0; animation: slideInRight .45s ease .25s both; }

    /* Pet photo reveals */
    .pet-photo { animation: imageReveal .5s ease .2s both; opacity: 0; }

    /* Pet name/badges fade up */
    .pet-identity { opacity: 0; animation: fadeDown .4s ease .4s both; }
</style>
@endpush

@section('content')

{{-- ── Header bar ── --}}
<div class="header-bar d-flex align-items-center gap-3 mb-4">
    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary" style="flex-shrink:0;">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
    <h5 class="mb-0 fw-bold" style="color:var(--navy);">{{ $pet->name }}</h5>
    <div class="ms-auto">
        @php
            $statusClass = match($pet->status) {
                'available'       => 's-available',
                'adopted'         => 's-adopted',
                'under_treatment' => 's-treatment',
                default           => 's-pending',
            };
        @endphp
        <span class="status-badge-top {{ $statusClass }}">
            {{ ucfirst(str_replace('_', ' ', $pet->status)) }}
        </span>
    </div>
</div>

<div class="row g-4">

    {{-- ── LEFT: Image + identity ── --}}
    <div class="col-lg-4">
        <div class="card card-left">
            <div class="card-body text-center p-4">

                {{-- Photo ── --}}
                @if($pet->primary_image)
                    <img src="{{ Storage::url($pet->primary_image) }}"
                         class="pet-photo mb-3" alt="{{ $pet->name }}">
                @else
                    <div class="mb-3" style="height:220px; border-radius:var(--radius-sm);
                                background:var(--coral-light); display:flex; align-items:center;
                                justify-content:center; font-size:4rem;
                                animation: imageReveal .5s ease .2s both; opacity:0;">
                        🐾
                    </div>
                @endif

                {{-- Name & breed ── --}}
                <div class="pet-identity">
                    <h5 class="fw-bold mb-1" style="color:var(--navy);">{{ $pet->name }}</h5>
                    <div style="font-size:.83rem; color:var(--muted); margin-bottom:.75rem;">
                        {{ $pet->petCategory->name ?? '' }}
                        @if($pet->breed) · {{ $pet->breed->name }} @endif
                    </div>

                    {{-- Health badges ── --}}
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        @if($pet->is_vaccinated)
                        <span class="health-badge badge-vaccinated">
                            <i class="bi bi-shield-check"></i> Vaccinated
                        </span>
                        @endif
                        @if($pet->is_neutered)
                        <span class="health-badge badge-neutered">
                            <i class="bi bi-scissors"></i> Neutered
                        </span>
                        @endif
                        @if($pet->is_microchipped)
                        <span class="health-badge" style="background:rgba(45,49,71,.08); color:var(--navy);">
                            <i class="bi bi-cpu"></i> Microchipped
                        </span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── RIGHT: Pet information ── --}}
    <div class="col-lg-8">
        <div class="card card-right">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-info-circle"></i> Details</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Pet Information</h6>
            </div>
            <div class="card-body" style="padding:1.25rem;">
                <dl class="mb-0" id="infoList">
                    <div class="info-row">
                        <dt>Name</dt>
                        <dd>{{ $pet->name }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Category</dt>
                        <dd>{{ $pet->petCategory->name ?? '—' }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Breed</dt>
                        <dd>{{ $pet->breed->name ?? '—' }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Gender</dt>
                        <dd>{{ ucfirst($pet->gender ?? '—') }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Age</dt>
                        {{-- $pet->age accessor already returns formatted string e.g. "2 yrs" ── --}}
                        <dd>{{ $pet->age ?? '—' }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Date of Birth</dt>
                        <dd>{{ $pet->date_of_birth?->format('M d, Y') ?? '—' }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Size</dt>
                        <dd>{{ ucfirst($pet->size ?? '—') }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Color</dt>
                        <dd>{{ $pet->color ?? '—' }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Weight</dt>
                        <dd>{{ $pet->weight ? $pet->weight . ' kg' : '—' }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Adoption Fee</dt>
                        <dd style="font-weight:700; color:var(--coral);">
                            {{ $pet->adoption_fee_display }}
                        </dd>
                    </div>
                    <div class="info-row">
                        <dt>Status</dt>
                        <dd>
                            <span class="status-badge-top {{ $statusClass }}" style="font-size:.72rem; padding:.25em .75em; animation:none; opacity:1;">
                                {{ ucfirst(str_replace('_', ' ', $pet->status)) }}
                            </span>
                        </dd>
                    </div>
                    <div class="info-row">
                        <dt>Approval</dt>
                        <dd>
                            @if($pet->is_admin_approved)
                                <span style="font-size:.75rem; font-weight:600; color:#2D5A3D;">
                                    <i class="bi bi-check-circle-fill me-1"></i>Approved
                                </span>
                            @else
                                <span style="font-size:.75rem; font-weight:600; color:#7A5A1A;">
                                    <i class="bi bi-clock-fill me-1"></i>Pending Approval
                                </span>
                            @endif
                        </dd>
                    </div>
                    @if($pet->description)
                    <div class="info-row">
                        <dt>Description</dt>
                        <dd style="line-height:1.6;">{{ $pet->description }}</dd>
                    </div>
                    @endif
                    @if($pet->microchip_number)
                    <div class="info-row">
                        <dt>Microchip #</dt>
                        <dd style="font-family: monospace;">{{ $pet->microchip_number }}</dd>
                    </div>
                    @endif
                    <div class="info-row">
                        <dt>Added By</dt>
                        <dd>{{ $pet->addedBy->name ?? '—' }}</dd>
                    </div>
                    <div class="info-row">
                        <dt>Added On</dt>
                        <dd>{{ $pet->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- ── Admin approve/reject actions ── --}}
        @if(!$pet->is_admin_approved && auth()->user()->isAdmin())
        <div class="card mt-3" style="border-top:3px solid var(--coral); opacity:0; animation: slideInRight .4s ease .5s both;">
            <div class="card-body d-flex gap-2 align-items-center" style="padding:.9rem 1.25rem;">
                <i class="bi bi-lightning-fill" style="color:var(--coral); font-size:1rem;"></i>
                <span style="font-size:.85rem; font-weight:600; color:var(--navy); flex:1;">
                    This pet is awaiting your approval
                </span>
                <form action="{{ route('admin.pets.approve', $pet) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Approve
                    </button>
                </form>
                <form action="{{ route('admin.pets.reject', $pet) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Reject this pet listing?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" style="color:#C0392B; border-color:#F5C6C0;">
                        <i class="bi bi-x-circle me-1"></i> Reject
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Stagger each info row sliding in from left
    document.querySelectorAll('#infoList .info-row').forEach((row, i) => {
        setTimeout(() => row.classList.add('visible'), 400 + (i * 55));
    });
});
</script>
@endpush