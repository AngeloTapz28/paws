@extends('layouts.app')

@section('title', 'All Pets')
@section('page-title', 'Pet Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">All Pets</li>
@endsection

@section('page-actions')
    {{-- Admin can only oversee pets; creation is done by Staff --}}
    <a href="{{ route('staff.pets.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Pet (Staff)
    </a>
@endsection

@push('styles')
<style>
    .filter-bar {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: var(--shadow-sm);
    }
    .search-wrap { position: relative; }
    .search-wrap .bi-search {
        position: absolute; left: .75rem; top: 50%;
        transform: translateY(-50%); color: var(--muted); font-size: .85rem; pointer-events: none;
    }
    .search-wrap input { padding-left: 2.1rem; }

    /* Pet grid card */
    .pet-grid-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
        box-shadow: var(--shadow-sm);
        display: flex; flex-direction: column;
    }
    .pet-grid-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
    .pet-img {
        height: 180px; width: 100%; object-fit: cover;
    }
    .pet-img-placeholder {
        height: 180px; width: 100%;
        background: var(--coral-light);
        display: flex; align-items: center; justify-content: center;
        font-size: 3.5rem;
    }
    .pet-body {
        padding: 1rem; flex: 1; display: flex; flex-direction: column; gap: .3rem;
    }
    .pet-name {
        font-weight: 700; font-size: .95rem; color: var(--navy); margin: 0;
    }
    .pet-meta { font-size: .75rem; color: var(--muted); }
    .pet-footer {
        padding: .7rem 1rem; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        background: var(--bg);
    }

    .status-pill {
        font-size: .68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .04em; padding: .25em .75em; border-radius: 20px;
    }
    .pill-available  { background: var(--sage-light);  color: #2D5A3D; }
    .pill-pending    { background: var(--gold-light);  color: #7A5A1A; }
    .pill-adopted    { background: var(--coral-light); color: var(--coral-dark); }
    .pill-treatment  { background: rgba(45,49,71,.08); color: var(--navy); }

    /* Approval badge overlay */
    .approval-overlay {
        position: absolute; top: 10px; right: 10px;
        font-size: .65rem; font-weight: 700; text-transform: uppercase;
        padding: .25em .7em; border-radius: 20px;
    }
    .approval-approved { background: var(--sage); color: #fff; }
    .approval-pending  { background: #E6C27A; color: var(--navy); }
    .pet-img-wrap { position: relative; }

    .pet-action-btn {
        width: 28px; height: 28px; border-radius: 7px;
        border: 1px solid var(--border); background: var(--white);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .8rem; color: var(--muted); text-decoration: none;
        transition: all .15s; cursor: pointer;
    }
    .pet-action-btn:hover { background: var(--coral-light); color: var(--coral); border-color: transparent; }
    .pet-action-btn.approve:hover { background: var(--sage-light); color: #2D5A3D; border-color: transparent; }
    .pet-action-btn.reject:hover  { background: #FEF0EE; color: #8B2516; border-color: transparent; }
</style>
@endpush

@section('content')

{{-- Filter Bar --}}
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4 col-lg-4">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Search pets by name…">
            </div>
        </div>
        <div class="col-md-3 col-lg-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                @foreach(['available','pending','adopted','under_treatment'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>
                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-lg-2">
            <select name="approval" class="form-select form-select-sm">
                <option value="">All Approvals</option>
                <option value="pending"  @selected(request('approval') === 'pending')>Pending Approval</option>
                <option value="approved" @selected(request('approval') === 'approved')>Approved</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('admin.pets.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- Results header --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="mb-0" style="font-size:.83rem; color:var(--muted);">
        Showing <strong style="color:var(--navy);">{{ $pets->firstItem() }}–{{ $pets->lastItem() }}</strong>
        of <strong style="color:var(--navy);">{{ $pets->total() }}</strong> pets
    </p>
    @if($pets->where('is_admin_approved', false)->count() > 0 || request('approval') === 'pending')
        <span style="font-size:.78rem; background:var(--gold-light); color:#7A5A1A;
                     padding:.3em .9em; border-radius:20px; font-weight:600;">
            <i class="bi bi-clock me-1"></i>
            {{ $pets->where('is_admin_approved', false)->count() }} pending approval on this page
        </span>
    @endif
</div>

{{-- Pet Grid --}}
@if($pets->isEmpty())
    <div class="card">
        <div class="empty-state py-5">
            <span class="empty-icon">🐾</span>
            <h5>No Pets Found</h5>
            <p>Pets added by staff will appear here for approval.</p>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($pets as $pet)
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="pet-grid-card">

                {{-- Image with approval overlay --}}
                <div class="pet-img-wrap">
                    @if($pet->primary_image)
                        <img src="{{ $pet->primary_image_url }}" class="pet-img" alt="{{ $pet->name }}">
                    @else
                        <div class="pet-img-placeholder">
                            {{ ($pet->category->name ?? '') === 'Dog' ? '🐶' : (($pet->category->name ?? '') === 'Cat' ? '🐱' : '🐾') }}
                        </div>
                    @endif
                    <span class="approval-overlay {{ $pet->is_admin_approved ? 'approval-approved' : 'approval-pending' }}">
                        {{ $pet->is_admin_approved ? '✓ Approved' : '⏳ Pending' }}
                    </span>
                </div>

                {{-- Body --}}
                <div class="pet-body">
                    <p class="pet-name">{{ $pet->name }}</p>
                    <p class="pet-meta">
                        {{ $pet->category->name ?? '—' }}
                        @if($pet->breed) · {{ $pet->breed->name }} @endif
                    </p>
                    <div class="d-flex align-items-center gap-2 mt-auto" style="flex-wrap:wrap;">
                        <span style="font-size:.72rem; color:var(--muted); background:var(--bg); padding:.2em .6em; border-radius:5px;">
                            {{ ucfirst($pet->gender) }}
                        </span>
                        <span style="font-size:.72rem; color:var(--muted); background:var(--bg); padding:.2em .6em; border-radius:5px;">
                            {{ $pet->age_label }}
                        </span>
                        @if($pet->adoption_fee > 0)
                        <span style="font-size:.72rem; color:#2D5A3D; background:var(--sage-light); padding:.2em .6em; border-radius:5px; font-weight:600;">
                            ₱{{ number_format($pet->adoption_fee) }}
                        </span>
                        @endif
                    </div>
                    <div style="font-size:.72rem; color:var(--muted); margin-top:.2rem;">
                        Added by {{ $pet->addedBy->name ?? 'Unknown' }}
                    </div>
                </div>

                {{-- Footer --}}
                <div class="pet-footer">
                    @php
                        $statusMap = [
                            'available'       => 'pill-available',
                            'pending'         => 'pill-pending',
                            'adopted'         => 'pill-adopted',
                            'under_treatment' => 'pill-treatment',
                        ];
                    @endphp
                    <span class="status-pill {{ $statusMap[$pet->status] ?? 'pill-pending' }}">
                        {{ ucfirst(str_replace('_', ' ', $pet->status)) }}
                    </span>

                    <div class="d-flex gap-1">
                        {{-- View via staff route (admin has staff middleware too) --}}
                        <a href="{{ route('staff.pets.show', $pet) }}" class="pet-action-btn" title="View Details">
                            <i class="bi bi-eye"></i>
                        </a>

                        @if(!$pet->is_admin_approved)
                            {{-- Approve --}}
                            <form action="{{ route('admin.pets.approve', $pet) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="pet-action-btn approve" title="Approve Listing">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            {{-- Reject --}}
                            <form action="{{ route('admin.pets.reject', $pet) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Reject listing for {{ addslashes($pet->name) }}?')">
                                @csrf
                                <button type="submit" class="pet-action-btn reject" title="Reject Listing">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($pets->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $pets->withQueryString()->links() }}
    </div>
    @endif
@endif

@endsection