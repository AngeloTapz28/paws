@extends('layouts.app')

@section('title', 'Staff Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('page-actions')
    <a href="{{ route('staff.pets.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Pet
    </a>
@endsection

@push('styles')
<style>
    /* ── Stat cards ── */
    .stat-card-v2 {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.4rem 1.5rem;
        position: relative; overflow: hidden;
        transition: transform .2s, box-shadow .2s; box-shadow: var(--shadow-sm);
    }
    .stat-card-v2:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
    .stat-card-v2 .sc-accent {
        position: absolute; top: 0; left: 0; right: 0;
        height: 3px; border-radius: var(--radius) var(--radius) 0 0;
    }
    .stat-card-v2 .sc-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; margin-bottom: .9rem;
    }
    .stat-card-v2 .sc-value { font-size: 2rem; font-weight: 800; line-height: 1; color: var(--navy); margin-bottom: .2rem; }
    .stat-card-v2 .sc-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }

    /* ── Section pill ── */
    .section-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--coral-subtle); color: var(--coral);
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; padding: .25rem .75rem; border-radius: 20px; margin-bottom: .5rem;
    }

    /* ── App row ── */
    .app-number { font-weight: 700; color: var(--coral); font-size: .85rem; }
    .pet-thumb  { width: 34px; height: 34px; border-radius: 8px; object-fit: cover; border: 2px solid var(--border); transition: transform .2s; }
    .app-row:hover .pet-thumb { transform: scale(1.08); }

    /* ── Quick action items ── */
    .qa-item {
        display: flex; align-items: center; gap: .85rem;
        padding: .85rem 1.1rem; border-radius: var(--radius-sm);
        border: 1px solid var(--border); background: var(--white);
        text-decoration: none; color: inherit;
        transition: background .15s, transform .15s, box-shadow .15s;
        margin-bottom: .5rem;
    }
    .qa-item:last-child { margin-bottom: 0; }
    .qa-item:hover { background: var(--coral-subtle); transform: translateX(4px); box-shadow: var(--shadow-sm); color: inherit; }
    .qa-icon {
        width: 40px; height: 40px; border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .qa-title { font-size: .875rem; font-weight: 700; color: var(--navy); margin-bottom: .1rem; }
    .qa-sub   { font-size: .73rem; color: var(--muted); }
    .qa-arrow { margin-left: auto; color: var(--muted); font-size: .8rem; transition: transform .15s; }
    .qa-item:hover .qa-arrow { transform: translateX(3px); color: var(--coral); }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-18px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(18px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Welcome strip */
    .welcome-strip { animation: fadeDown .45s ease both; }

    /* Stat cards stagger — JS */
    .stat-card-v2 { opacity: 0; }
    .stat-card-v2.animated { animation: fadeUp .45s ease both; }

    /* Main grid */
    .col-apps   { opacity: 0; animation: slideInLeft  .45s ease .5s both; }
    .col-quick  { opacity: 0; animation: slideInRight .45s ease .6s both; }

    /* App rows — JS */
    .app-row { opacity: 0; }
    .app-row.visible { animation: slideInLeft .38s ease both; }

    /* Quick action items — JS */
    .qa-item { opacity: 0; }
    .qa-item.visible { animation: slideInRight .38s ease both; }
</style>
@endpush

@section('content')

{{-- ── Welcome Strip ── --}}
@php
    $nameParts  = explode(' ', auth()->user()->name);
    $honorifics = ['dr.','dr','mr.','mr','ms.','ms','mrs.','mrs','prof.','prof'];
    $firstName  = collect($nameParts)->first(fn($p) => !in_array(strtolower($p), $honorifics)) ?? last($nameParts);
@endphp
<div class="welcome-strip d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 style="font-size:1.5rem; font-weight:800; color:var(--navy); margin:0;">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ $firstName }}
        </h2>
        <p class="mb-0 mt-1" style="color:var(--muted); font-size:.85rem;">
            {{ now()->format('l, F j, Y') }} — Here's your shelter overview
        </p>
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2" data-delay="0">
            <div class="sc-accent" style="background:linear-gradient(90deg,var(--coral),#E8956A);"></div>
            <div class="sc-icon" style="background:var(--coral-light); color:var(--coral);"><i class="bi bi-heart-fill"></i></div>
            <div class="sc-value" data-count="{{ $totalPets ?? $stats['total_pets'] ?? 0 }}">0</div>
            <div class="sc-label">Total Pets</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2" data-delay="100">
            <div class="sc-accent" style="background:linear-gradient(90deg,var(--sage),#A8C8B3);"></div>
            <div class="sc-icon" style="background:var(--sage-light); color:var(--sage);"><i class="bi bi-check-circle-fill"></i></div>
            <div class="sc-value" data-count="{{ $availablePets ?? $stats['available_pets'] ?? 0 }}">0</div>
            <div class="sc-label">Available Pets</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2" data-delay="200">
            <div class="sc-accent" style="background:linear-gradient(90deg,var(--gold),#EDD090);"></div>
            <div class="sc-icon" style="background:var(--gold-light); color:#B8892A;"><i class="bi bi-file-earmark-text-fill"></i></div>
            <div class="sc-value" data-count="{{ $pendingApplications ?? $stats['pending_applications'] ?? 0 }}">0</div>
            <div class="sc-label">Pending Applications</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2" data-delay="300">
            <div class="sc-accent" style="background:linear-gradient(90deg,var(--navy),var(--navy-light));"></div>
            <div class="sc-icon" style="background:rgba(45,49,71,.08); color:var(--navy);"><i class="bi bi-cash-stack"></i></div>
            <div class="sc-value" data-count="{{ $totalPayments ?? $stats['payments_today'] ?? 0 }}">0</div>
            <div class="sc-label">Payments Today</div>
        </div>
    </div>

</div>

{{-- ── Main Grid ── --}}
<div class="row g-3">

    {{-- Pending Applications ── --}}
    <div class="col-lg-8 col-apps">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between" style="padding:1rem 1.25rem;">
                <div>
                    <div class="section-pill" style="background:var(--coral-subtle); color:var(--coral);">
                        <i class="bi bi-clock"></i> Needs Review
                    </div>
                    <h6 class="mb-0 fw-bold" style="color:var(--navy);">Pending Applications</h6>
                </div>
                <a href="{{ route('staff.applications.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:.78rem;">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentApplications->isEmpty())
                    <div class="empty-state py-5">
                        <span class="empty-icon">📋</span>
                        <h5>No Pending Applications</h5>
                        <p>All caught up! No applications need review right now.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th style="padding-left:1.25rem;">App #</th>
                                    <th>Pet</th>
                                    <th>Applicant</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th style="text-align:right; padding-right:1.25rem;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentApplications as $i => $app)
                                <tr class="app-row" data-index="{{ $i }}">
                                    <td style="padding-left:1.25rem;">
                                        <span class="app-number">{{ $app->application_number }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $app->pet?->primary_image_url ?? asset('images/no-pet.png') }}" class="pet-thumb" alt="">
                                            <span style="font-weight:600; font-size:.855rem; color:var(--navy);">{{ $app->pet?->name ?? 'Deleted Pet' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size:.855rem; color:var(--text);">{{ $app->applicant_full_name }}</div>
                                        <div style="font-size:.75rem; color:var(--muted);">{{ $app->adopter?->email }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $app->status_badge }}">{{ $app->status_label }}</span>
                                    </td>
                                    <td style="font-size:.8rem; color:var(--muted);">
                                        {{ $app->submitted_at?->format('M d, Y') ?? $app->created_at->format('M d, Y') }}
                                    </td>
                                    <td style="text-align:right; padding-right:1.25rem;">
                                        <a href="{{ route('staff.applications.show', $app) }}"
                                           class="btn btn-sm btn-outline-primary" style="font-size:.75rem; padding:.25rem .65rem;">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick Actions ── --}}
    <div class="col-lg-4 col-quick d-flex flex-column gap-3">
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:var(--gold-light); color:#7A5A1A;">
                    <i class="bi bi-lightning-fill"></i> Quick Actions
                </div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">What would you like to do?</h6>
            </div>
            <div class="card-body" style="padding:1rem 1.1rem;" id="qaList">

                <a href="{{ route('staff.pets.create') }}" class="qa-item" data-qa="0">
                    <div class="qa-icon" style="background:var(--coral-light); color:var(--coral);">
                        <i class="bi bi-plus-circle-fill"></i>
                    </div>
                    <div>
                        <div class="qa-title">Add New Pet</div>
                        <div class="qa-sub">Register a new pet listing</div>
                    </div>
                    <i class="bi bi-chevron-right qa-arrow"></i>
                </a>

                <a href="{{ route('staff.applications.index') }}" class="qa-item" data-qa="1">
                    <div class="qa-icon" style="background:var(--gold-light); color:#B8892A;">
                        <i class="bi bi-file-earmark-check-fill"></i>
                    </div>
                    <div>
                        <div class="qa-title">Review Applications</div>
                        <div class="qa-sub">
                            {{ $pendingApplications ?? $stats['pending_applications'] ?? 0 }} pending review
                        </div>
                    </div>
                    <i class="bi bi-chevron-right qa-arrow"></i>
                </a>

                <a href="{{ route('staff.payments.create') }}" class="qa-item" data-qa="2">
                    <div class="qa-icon" style="background:var(--sage-light); color:var(--sage);">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <div class="qa-title">Record Payment</div>
                        <div class="qa-sub">Log a new payment</div>
                    </div>
                    <i class="bi bi-chevron-right qa-arrow"></i>
                </a>

                <a href="{{ route('staff.pets.index') }}" class="qa-item" data-qa="3">
                    <div class="qa-icon" style="background:rgba(45,49,71,.07); color:var(--navy);">
                        <i class="bi bi-grid-fill"></i>
                    </div>
                    <div>
                        <div class="qa-title">Manage Pets</div>
                        <div class="qa-sub">View & update pet listings</div>
                    </div>
                    <i class="bi bi-chevron-right qa-arrow"></i>
                </a>

            </div>
        </div>

        {{-- Recent Pets ── --}}
        @if(isset($recentPets) && $recentPets->count())
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-heart"></i> Recently Added</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Recent Pets</h6>
            </div>
            <div class="card-body" style="padding:.75rem 1.1rem;">
                @foreach($recentPets->take(4) as $pet)
                <a href="{{ route('staff.pets.show', $pet) }}"
                   class="d-flex align-items-center gap-2 py-2 text-decoration-none"
                   style="border-bottom:1px solid var(--border); transition:background .12s; border-radius:6px; padding-left:.4rem;"
                   onmouseover="this.style.background='var(--coral-subtle)'"
                   onmouseout="this.style.background=''">
                    @if($pet->primary_image)
                        <img src="{{ $pet->primary_image_url }}"
                             style="width:36px;height:36px;border-radius:8px;object-fit:cover;border:2px solid var(--border);" alt="">
                    @else
                        <div style="width:36px;height:36px;border-radius:8px;background:var(--coral-light);display:flex;align-items:center;justify-content:center;font-size:1.2rem;">🐾</div>
                    @endif
                    <div class="flex-grow-1 min-w-0">
                        <div style="font-size:.83rem;font-weight:600;color:var(--navy);">{{ $pet->name }}</div>
                        <div style="font-size:.72rem;color:var(--muted);">{{ $pet->category->name ?? '—' }}</div>
                    </div>
                    @if(!$pet->is_admin_approved)
                        <span style="font-size:.63rem;font-weight:700;padding:.2em .6em;border-radius:20px;background:var(--gold-light);color:#7A5A1A;">Pending</span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── 1. Stat cards stagger ──
    document.querySelectorAll('.stat-card-v2').forEach(card => {
        setTimeout(() => card.classList.add('animated'), parseInt(card.dataset.delay ?? 0));
    });

    // ── 2. Count-up ──
    function countUp(el) {
        const target = parseInt(el.dataset.count);
        if (isNaN(target) || target === 0) { el.textContent = '0'; return; }
        const dur = 900, step = 16, inc = target / (dur / step);
        let cur = 0;
        const t = setInterval(() => {
            cur += inc;
            if (cur >= target) { clearInterval(t); el.textContent = target; }
            else el.textContent = Math.floor(cur);
        }, step);
    }
    setTimeout(() => {
        document.querySelectorAll('.stat-card-v2 .sc-value[data-count]').forEach(countUp);
    }, 350);

    // ── 3. App rows stagger ──
    document.querySelectorAll('.app-row').forEach(row => {
        const delay = 600 + (parseInt(row.dataset.index) * 70);
        setTimeout(() => row.classList.add('visible'), delay);
    });

    // ── 4. Quick action items stagger ──
    document.querySelectorAll('.qa-item').forEach(item => {
        const delay = 700 + (parseInt(item.dataset.qa) * 90);
        setTimeout(() => item.classList.add('visible'), delay);
    });

});
</script>
@endpush