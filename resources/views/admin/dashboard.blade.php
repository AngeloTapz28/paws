@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('page-actions')
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-bar-chart me-1"></i> View Reports
    </a>
@endsection

@push('styles')
<style>
    /* ── Stat cards ── */
    .stat-card-v2 {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.4rem 1.5rem;
        position: relative;
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
        box-shadow: var(--shadow-sm);
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
    .stat-card-v2 .sc-value {
        font-size: 2rem; font-weight: 800; line-height: 1; color: var(--navy); margin-bottom: .2rem;
    }
    .stat-card-v2 .sc-label {
        font-size: .75rem; font-weight: 600; text-transform: uppercase;
        letter-spacing: .06em; color: var(--muted);
    }
    .stat-card-v2 .sc-sub {
        font-size: .75rem; margin-top: .5rem; display: flex; align-items: center; gap: .3rem;
    }

    /* ── Section pill ── */
    .section-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--coral-subtle); color: var(--coral);
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; padding: .25rem .75rem; border-radius: 20px;
        margin-bottom: .6rem;
    }

    /* ── Status row ── */
    .status-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: .55rem .75rem; border-radius: 9px; margin-bottom: .35rem;
        transition: background .15s;
    }
    .status-row:hover { background: var(--coral-subtle); }
    .status-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }

    /* ── Pending pet item ── */
    .pending-pet-item {
        display: flex; align-items: center; gap: .75rem;
        padding: .65rem .75rem; border-radius: 10px; margin-bottom: .35rem;
        background: var(--bg); transition: background .15s, transform .15s;
    }
    .pending-pet-item:hover { background: var(--coral-subtle); transform: translateX(3px); }

    /* ── App row ── */
    .app-row td { padding: .8rem 1rem !important; }
    .app-number { font-weight: 700; color: var(--coral); font-size: .85rem; }
    .pet-thumb { width: 34px; height: 34px; border-radius: 8px; object-fit: cover; border: 2px solid var(--border); }

    /* ── Progress bar animation ── */
    .status-bar-segment {
        height: 7px;
        width: 0%;
        transition: width 1s cubic-bezier(.25,.46,.45,.94);
    }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    /* Welcome strip */
    .welcome-strip { animation: fadeUp .5s ease both; }

    /* Stat cards stagger */
    .stat-card-v2 { opacity: 0; }
    .stat-card-v2.animated { animation: fadeUp .5s ease both; }

    /* Main grid cards */
    .main-card-left  { opacity: 0; animation: fadeUp .5s ease .55s both; }
    .main-card-right { opacity: 0; animation: fadeUp .5s ease .65s both; }

    /* Table rows stagger in */
    .app-row { opacity: 0; }
    .app-row.animated { animation: slideInLeft .4s ease both; }

    /* Status rows */
    .status-row { opacity: 0; }
    .status-row.animated { animation: fadeUp .35s ease both; }
</style>
@endpush

@section('content')

{{-- ── Welcome strip ── --}}
@php
    $nameParts  = explode(' ', auth()->user()->name);
    $honorifics = ['system', 'dr.', 'dr', 'mr.', 'mr', 'ms.', 'ms', 'mrs.', 'mrs', 'prof.', 'prof'];
    $firstName  = collect($nameParts)
                    ->first(fn($p) => !in_array(strtolower($p), $honorifics))
                    ?? last($nameParts);
@endphp
<div class="welcome-strip d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 style="font-size:1.5rem; font-weight:800; color:var(--navy); margin:0;">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
            {{ $firstName }} 
        </h2>
        <p class="mb-0 mt-1" style="color:var(--muted); font-size:.85rem;">
            Here's what's happening with PAWS today — {{ now()->format('l, F j, Y') }}
        </p>
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2" data-delay="100">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--coral), #E8956A);"></div>
            <div class="sc-icon" style="background:var(--coral-light); color:var(--coral);">
                <i class="bi bi-heart-fill"></i>
            </div>
            <div class="sc-value" data-count="{{ $stats['total_pets'] }}">0</div>
            <div class="sc-label">Total Pets</div>
            <div class="sc-sub" style="color:var(--sage);">
                <i class="bi bi-check-circle-fill"></i> {{ $stats['available_pets'] }} available now
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2" data-delay="200">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--sage), #A8C8B3);"></div>
            <div class="sc-icon" style="background:var(--sage-light); color:var(--sage);">
                <i class="bi bi-house-heart-fill"></i>
            </div>
            <div class="sc-value" data-count="{{ $stats['total_adopted'] }}">0</div>
            <div class="sc-label">Pets Adopted</div>
            <div class="sc-sub" style="color:var(--muted);">
                <i class="bi bi-trophy-fill" style="color:var(--gold);"></i> All time total
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2" data-delay="300">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--gold), #EDD090);"></div>
            <div class="sc-icon" style="background:var(--gold-light); color:#B8892A;">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="sc-value" data-count="{{ $stats['pending_applications'] }}">0</div>
            <div class="sc-label">Pending Applications</div>
            <div class="sc-sub" style="color:#B8892A;">
                <i class="bi bi-clock-fill"></i> Awaiting your review
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2" data-delay="400">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--navy), var(--navy-light));"></div>
            <div class="sc-icon" style="background:rgba(45,49,71,.08); color:var(--navy);">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="sc-value" data-count="{{ $stats['total_users'] }}">0</div>
            <div class="sc-label">Registered Users</div>
            <div class="sc-sub" style="color:var(--muted);">
                <i class="bi bi-person-plus-fill" style="color:var(--coral);"></i> {{ $stats['new_users_month'] }} joined this month
            </div>
        </div>
    </div>

</div>

{{-- ── Main Grid ── --}}
<div class="row g-3">

    {{-- Recent Applications Table --}}
    <div class="col-lg-8 main-card-left">
        <div class="card h-100" style="border-radius: var(--radius);">
            <div class="card-header d-flex align-items-center justify-content-between" style="padding: 1rem 1.25rem;">
                <div>
                    <div class="section-pill"><i class="bi bi-file-earmark-text"></i> Applications</div>
                    <h6 class="mb-0" style="color:var(--navy); font-weight:700;">Recent Applications</h6>
                </div>
                <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:.78rem;">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentApplications->isEmpty())
                    <div class="empty-state py-5">
                        <span class="empty-icon">📋</span>
                        <h5>No Applications Yet</h5>
                        <p>Adoption applications will appear here.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>App #</th>
                                    <th>Pet</th>
                                    <th>Applicant</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentApplications as $i => $app)
                                <tr class="app-row" data-delay="{{ $i * 80 }}">
                                    <td><span class="app-number">{{ $app->application_number }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $app->pet?->primary_image_url ?? asset('images/no-pet.png') }}" class="pet-thumb" alt="">
                                            <span style="font-weight:600; font-size:.85rem;">{{ $app->pet?->name ?? 'Deleted Pet' }}</span>
                                        </div>
                                    </td>
                                    <td style="font-size:.85rem;">{{ $app->applicant_full_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $app->status_badge }}">{{ $app->status_label }}</span>
                                    </td>
                                    <td style="color:var(--muted); font-size:.8rem;">{{ $app->submitted_at?->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.applications.show', $app) }}"
                                           class="btn btn-sm btn-outline-primary" style="font-size:.75rem; padding:.25rem .65rem;">
                                            View
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

    {{-- Right Column --}}
    <div class="col-lg-4 main-card-right d-flex flex-column gap-3">

        {{-- Pet Status Overview --}}
        <div class="card">
            <div class="card-header" style="padding: 1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-pie-chart"></i> Overview</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Pet Status Breakdown</h6>
            </div>
            <div class="card-body" style="padding: .9rem 1.25rem;">
                @php $total = max(1, $stats['total_pets']); @endphp
                @foreach([
                    ['label' => 'Available',       'count' => $stats['available_pets'],   'color' => '#8FAF9A', 'pct' => round(($stats['available_pets']/$total)*100)],
                    ['label' => 'Pending',         'count' => $stats['pending_pets'],     'color' => '#E6C27A', 'pct' => round(($stats['pending_pets']/$total)*100)],
                    ['label' => 'Adopted',         'count' => $stats['total_adopted'],    'color' => '#D97757', 'pct' => round(($stats['total_adopted']/$total)*100)],
                    ['label' => 'Under Treatment', 'count' => $stats['treatment_pets'],   'color' => '#2D3147', 'pct' => 0],
                ] as $idx => $item)
                <div class="status-row" data-delay="{{ $idx * 80 }}">
                    <div class="d-flex align-items-center gap-2">
                        <div class="status-dot" style="background: {{ $item['color'] }};"></div>
                        <span style="font-size:.83rem; color:var(--text);">{{ $item['label'] }}</span>
                    </div>
                    <span style="font-weight:700; font-size:.875rem; color:var(--navy);">{{ $item['count'] }}</span>
                </div>
                @endforeach

                {{-- Animated visual bar --}}
                <div class="mt-3 d-flex rounded-pill overflow-hidden" style="height:7px; gap:2px;" id="statusBar">
                    <div class="status-bar-segment" data-width="{{ round(($stats['available_pets']/$total)*100) }}" style="background:#8FAF9A; border-radius:20px 0 0 20px;"></div>
                    <div class="status-bar-segment" data-width="{{ round(($stats['pending_pets']/$total)*100) }}"   style="background:#E6C27A;"></div>
                    <div class="status-bar-segment" data-width="{{ round(($stats['total_adopted']/$total)*100) }}"  style="background:#D97757;"></div>
                    <div class="status-bar-segment" style="flex:1; background:#2D3147; border-radius:0 20px 20px 0; transition: opacity 1s ease;"></div>
                </div>
            </div>
        </div>

        {{-- Pending Approvals --}}
        <div class="card flex-grow-1">
            <div class="card-header" style="padding: 1rem 1.25rem;">
                <div class="section-pill" style="background:var(--gold-light); color:#B8892A;"><i class="bi bi-clock"></i> Action Needed</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Pending Pet Approvals</h6>
            </div>
            <div class="card-body" style="padding: .9rem 1.25rem;">
                @if($pendingPets->isEmpty())
                    <div class="text-center py-3" style="color:var(--muted); font-size:.82rem;">
                        <i class="bi bi-check2-circle d-block mb-2" style="font-size:1.8rem; opacity:.3;"></i>
                        All pets are approved!
                    </div>
                @else
                    @foreach($pendingPets as $pet)
                    <div class="pending-pet-item">
                        <img src="{{ $pet->primary_image_url }}"
                             style="width:38px;height:38px;border-radius:9px;object-fit:cover;border:2px solid var(--border);" alt="">
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold" style="font-size:.82rem; color:var(--navy);">{{ $pet->name }}</div>
                            <div style="font-size:.72rem; color:var(--muted);">{{ $pet->category->name ?? '—' }}</div>
                        </div>
                        <form action="{{ route('admin.pets.approve', $pet) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-primary" style="font-size:.72rem; padding:.2rem .6rem; border-radius:6px;">
                                Approve
                            </button>
                        </form>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── 1. Stat cards stagger in ──
    document.querySelectorAll('.stat-card-v2').forEach(card => {
        const delay = parseInt(card.dataset.delay ?? 0);
        setTimeout(() => card.classList.add('animated'), delay);
    });

    // ── 2. Count-up for stat values ──
    function countUp(el) {
        const target = parseInt(el.dataset.count, 10);
        if (isNaN(target) || target === 0) { el.textContent = '0'; return; }
        const duration = 1000;
        const step     = 16;
        const increment = target / (duration / step);
        let current = 0;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                clearInterval(timer);
                el.textContent = target;
            } else {
                el.textContent = Math.floor(current);
            }
        }, step);
    }

    // Start count-up after cards animate in
    setTimeout(() => {
        document.querySelectorAll('.stat-card-v2 .sc-value[data-count]').forEach(countUp);
    }, 500);

    // ── 3. Table rows stagger in ──
    document.querySelectorAll('.app-row').forEach(row => {
        const delay = 600 + parseInt(row.dataset.delay ?? 0);
        setTimeout(() => row.classList.add('animated'), delay);
    });

    // ── 4. Status rows stagger in ──
    document.querySelectorAll('.status-row').forEach(row => {
        const delay = 700 + parseInt(row.dataset.delay ?? 0);
        setTimeout(() => row.classList.add('animated'), delay);
    });

    // ── 5. Animated status bar ──
    setTimeout(() => {
        document.querySelectorAll('.status-bar-segment[data-width]').forEach(seg => {
            seg.style.width = seg.dataset.width + '%';
        });
    }, 800);

});
</script>
@endpush