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
    .stat-card-v2:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
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

    /* Section header pill */
    .section-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--coral-subtle); color: var(--coral);
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; padding: .25rem .75rem; border-radius: 20px;
        margin-bottom: .6rem;
    }

    /* Status row item */
    .status-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: .55rem .75rem; border-radius: 9px; margin-bottom: .35rem;
        transition: background .15s;
    }
    .status-row:hover { background: var(--coral-subtle); }
    .status-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }

    /* Pending pet item */
    .pending-pet-item {
        display: flex; align-items: center; gap: .75rem;
        padding: .65rem .75rem; border-radius: 10px; margin-bottom: .35rem;
        background: var(--bg); transition: background .15s;
    }
    .pending-pet-item:hover { background: var(--coral-subtle); }

    /* App row */
    .app-row td { padding: .8rem 1rem !important; }
    .app-number { font-weight: 700; color: var(--coral); font-size: .85rem; }
    .pet-thumb { width: 34px; height: 34px; border-radius: 8px; object-fit: cover; border: 2px solid var(--border); }
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
<div class="d-flex align-items-center justify-content-between mb-4">
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
        <div class="stat-card-v2">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--coral), #E8956A);"></div>
            <div class="sc-icon" style="background:var(--coral-light); color:var(--coral);">
                <i class="bi bi-heart-fill"></i>
            </div>
            <div class="sc-value">{{ $stats['total_pets'] }}</div>
            <div class="sc-label">Total Pets</div>
            <div class="sc-sub" style="color:var(--sage);">
                <i class="bi bi-check-circle-fill"></i> {{ $stats['available_pets'] }} available now
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--sage), #A8C8B3);"></div>
            <div class="sc-icon" style="background:var(--sage-light); color:var(--sage);">
                <i class="bi bi-house-heart-fill"></i>
            </div>
            <div class="sc-value">{{ $stats['total_adopted'] }}</div>
            <div class="sc-label">Pets Adopted</div>
            <div class="sc-sub" style="color:var(--muted);">
                <i class="bi bi-trophy-fill" style="color:var(--gold);"></i> All time total
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--gold), #EDD090);"></div>
            <div class="sc-icon" style="background:var(--gold-light); color:#B8892A;">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="sc-value">{{ $stats['pending_applications'] }}</div>
            <div class="sc-label">Pending Applications</div>
            <div class="sc-sub" style="color:#B8892A;">
                <i class="bi bi-clock-fill"></i> Awaiting your review
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--navy), var(--navy-light));"></div>
            <div class="sc-icon" style="background:rgba(45,49,71,.08); color:var(--navy);">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="sc-value">{{ $stats['total_users'] }}</div>
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
    <div class="col-lg-8">
        <div class="card h-100" style="border-radius: var(--radius);">
            <div class="card-header d-flex align-items-center justify-content-between" style="padding: 1rem 1.25rem;">
                <div>
                    <div class="section-pill"><i class="bi bi-file-earmark-text"></i> Applications</div>
                    <h6 class="mb-0 fw-700" style="color:var(--navy); font-weight:700;">Recent Applications</h6>
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
                                @foreach($recentApplications as $app)
                                <tr class="app-row">
                                    <td><span class="app-number">{{ $app->application_number }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $app->pet?->primary_image_url ?? asset('images/no-pet.png') }}" class="pet-thumb" alt="">
                                                <span class="fw-600" style="font-weight:600; font-size:.85rem;">{{ $app->pet?->name ?? 'Deleted Pet' }}</span>
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
    <div class="col-lg-4 d-flex flex-column gap-3">

        {{-- Pet Status Overview --}}
        <div class="card">
            <div class="card-header" style="padding: 1rem 1.25rem;">
                <div class="section-pill"><i class="bi bi-pie-chart"></i> Overview</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">Pet Status Breakdown</h6>
            </div>
            <div class="card-body" style="padding: .9rem 1.25rem;">
                @foreach([
                    ['label' => 'Available',       'count' => $stats['available_pets'],   'color' => '#8FAF9A'],
                    ['label' => 'Pending',         'count' => $stats['pending_pets'],     'color' => '#E6C27A'],
                    ['label' => 'Adopted',         'count' => $stats['total_adopted'],    'color' => '#D97757'],
                    ['label' => 'Under Treatment', 'count' => $stats['treatment_pets'],   'color' => '#2D3147'],
                ] as $item)
                <div class="status-row">
                    <div class="d-flex align-items-center gap-2">
                        <div class="status-dot" style="background: {{ $item['color'] }};"></div>
                        <span style="font-size:.83rem; color:var(--text);">{{ $item['label'] }}</span>
                    </div>
                    <span style="font-weight:700; font-size:.875rem; color:var(--navy);">{{ $item['count'] }}</span>
                </div>
                @endforeach

                {{-- Simple visual bar --}}
                @php $total = max(1, $stats['total_pets']); @endphp
                <div class="mt-3 d-flex rounded-pill overflow-hidden" style="height:7px; gap:2px;">
                    <div style="width:{{ ($stats['available_pets']/$total)*100 }}%; background:#8FAF9A;"></div>
                    <div style="width:{{ ($stats['pending_pets']/$total)*100 }}%; background:#E6C27A;"></div>
                    <div style="width:{{ ($stats['total_adopted']/$total)*100 }}%; background:#D97757;"></div>
                    <div style="flex:1; background:#2D3147;"></div>
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