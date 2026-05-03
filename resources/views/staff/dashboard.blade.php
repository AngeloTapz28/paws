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
    .stat-card-v2 {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.4rem 1.5rem;
        position: relative; overflow: hidden;
        transition: transform .2s, box-shadow .2s; box-shadow: var(--shadow-sm);
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
    .stat-card-v2 .sc-value { font-size: 2rem; font-weight: 800; color: var(--navy); line-height: 1; margin-bottom: .2rem; }
    .stat-card-v2 .sc-label { font-size: .73rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }

    .section-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--coral-subtle); color: var(--coral);
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; padding: .22rem .7rem; border-radius: 20px; margin-bottom: .5rem;
    }

    .app-row td { padding: .8rem 1rem !important; }
    .app-number { font-weight: 700; color: var(--coral); font-size: .85rem; }
    .pet-thumb  { width: 36px; height: 36px; border-radius: 8px; object-fit: cover; border: 2px solid var(--border); }

    .action-btn {
        width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--border);
        background: var(--white); display: inline-flex; align-items: center;
        justify-content: center; font-size: .85rem; color: var(--muted);
        text-decoration: none; transition: all .15s;
    }
    .action-btn:hover { background: var(--coral-light); color: var(--coral); border-color: transparent; }

    /* Recent pets mini grid */
    .pet-mini { border-radius: 10px; overflow: hidden; border: 1px solid var(--border); background: var(--white); }
    .pet-mini img { width: 100%; height: 80px; object-fit: cover; }
    .pet-mini .pm-ph { width: 100%; height: 80px; background: var(--coral-light); display: flex; align-items: center; justify-content: center; font-size: 2rem; }
    .pet-mini .pm-body { padding: .5rem .6rem; }
    .pet-mini .pm-name { font-size: .78rem; font-weight: 700; color: var(--navy); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .pet-mini .pm-badge { font-size: .6rem; font-weight: 700; padding: .2em .55em; border-radius: 20px; }
</style>
@endpush

@section('content')

@php
    $nameParts  = explode(' ', auth()->user()->name);
    $honorifics = ['system','dr.','dr','mr.','mr','ms.','ms','mrs.','mrs'];
    $firstName  = collect($nameParts)->first(fn($p) => !in_array(strtolower($p), $honorifics)) ?? last($nameParts);
@endphp

{{-- Greeting --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 style="font-size:1.45rem; font-weight:800; color:var(--navy); margin:0;">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ $firstName }} 
        </h2>
        <p class="mb-0 mt-1" style="color:var(--muted); font-size:.85rem;">
            {{ now()->format('l, F j, Y') }} — Here's your shelter overview
        </p>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--coral), #E8956A);"></div>
            <div class="sc-icon" style="background:var(--coral-light); color:var(--coral);"><i class="bi bi-heart-fill"></i></div>
            <div class="sc-value">{{ $totalPets }}</div>
            <div class="sc-label">Total Pets</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--sage), #A8C8B3);"></div>
            <div class="sc-icon" style="background:var(--sage-light); color:var(--sage);"><i class="bi bi-check-circle-fill"></i></div>
            <div class="sc-value">{{ $availablePets }}</div>
            <div class="sc-label">Available Pets</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--gold), #EDD090);"></div>
            <div class="sc-icon" style="background:var(--gold-light); color:#B8892A;"><i class="bi bi-file-earmark-text-fill"></i></div>
            <div class="sc-value">{{ $pendingApplications }}</div>
            <div class="sc-label">Pending Applications</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-v2">
            <div class="sc-accent" style="background: linear-gradient(90deg, var(--navy), var(--navy-light));"></div>
            <div class="sc-icon" style="background:rgba(45,49,71,.08); color:var(--navy);"><i class="bi bi-cash-stack"></i></div>
            <div class="sc-value">{{ $totalPayments }}</div>
            <div class="sc-label">Payments Today</div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- Pending Applications --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between" style="padding:1rem 1.25rem;">
                <div>
                    <div class="section-pill"><i class="bi bi-clock"></i> Needs Review</div>
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
                                @foreach($recentApplications as $app)
                                <tr class="app-row">
                                    <td style="padding-left:1.25rem;">
                                        <span class="app-number">{{ $app->application_number }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $app->pet->primary_image_url }}" class="pet-thumb" alt="">
                                            <span style="font-weight:600; font-size:.855rem; color:var(--navy);">{{ $app->pet->name }}</span>
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
                                        <a href="{{ route('staff.applications.show', $app) }}" class="action-btn" title="View">
                                            <i class="bi bi-eye"></i>
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

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header" style="padding:1rem 1.25rem;">
                <div class="section-pill" style="background:var(--sage-light);color:#2D5A3D;"><i class="bi bi-lightning-fill"></i> Quick Actions</div>
                <h6 class="mb-0 fw-bold" style="color:var(--navy);">What would you like to do?</h6>
            </div>
            <div class="card-body d-flex flex-column gap-2" style="padding:1rem;">
                <a href="{{ route('staff.pets.create') }}"
                   class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none"
                   style="background:var(--coral-subtle); border:1px solid var(--coral-light); transition:all .15s;"
                   onmouseover="this.style.background='var(--coral-light)'" onmouseout="this.style.background='var(--coral-subtle)'">
                    <div style="width:38px;height:38px;border-radius:10px;background:var(--coral);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:700;color:var(--navy);">Add New Pet</div>
                        <div style="font-size:.75rem;color:var(--muted);">Register a new pet listing</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:var(--coral);font-size:.8rem;"></i>
                </a>

                <a href="{{ route('staff.applications.index') }}"
                   class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none"
                   style="background:var(--gold-light); border:1px solid #EDD8A0; transition:all .15s;"
                   onmouseover="this.style.background='#F0DFA0'" onmouseout="this.style.background='var(--gold-light)'">
                    <div style="width:38px;height:38px;border-radius:10px;background:var(--gold);color:var(--navy);display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:700;color:var(--navy);">Review Applications</div>
                        <div style="font-size:.75rem;color:var(--muted);">
                            {{ $pendingApplications }} pending review
                        </div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:#B8892A;font-size:.8rem;"></i>
                </a>

                <a href="{{ route('staff.payments.create') }}"
                   class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none"
                   style="background:var(--sage-light); border:1px solid #C0D9C8; transition:all .15s;"
                   onmouseover="this.style.background='#C8E0D0'" onmouseout="this.style.background='var(--sage-light)'">
                    <div style="width:38px;height:38px;border-radius:10px;background:var(--sage);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:700;color:var(--navy);">Record Payment</div>
                        <div style="font-size:.75rem;color:var(--muted);">Log a new payment</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:var(--sage);font-size:.8rem;"></i>
                </a>

                <a href="{{ route('staff.pets.index') }}"
                   class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none"
                   style="background:rgba(45,49,71,.05); border:1px solid var(--border); transition:all .15s;"
                   onmouseover="this.style.background='rgba(45,49,71,.09)'" onmouseout="this.style.background='rgba(45,49,71,.05)'">
                    <div style="width:38px;height:38px;border-radius:10px;background:var(--navy);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:700;color:var(--navy);">Manage Pets</div>
                        <div style="font-size:.75rem;color:var(--muted);">{{ $totalPets }} total · {{ $availablePets }} available</div>
                    </div>
                    <i class="bi bi-chevron-right ms-auto" style="color:var(--navy-light);font-size:.8rem;"></i>
                </a>
            </div>
        </div>

    </div>
</div>

@endsection