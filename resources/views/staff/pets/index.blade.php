@extends('layouts.app')

@section('title', 'Pets')
@section('page-title', 'Pet Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Pets</li>
@endsection

@section('page-actions')
    <a href="{{ route('staff.pets.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Pet
    </a>
@endsection

@push('styles')
<style>
    .filter-bar {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1rem 1.25rem;
        margin-bottom: 1.25rem; box-shadow: var(--shadow-sm);
    }
    .search-wrap { position: relative; }
    .search-wrap .bi-search {
        position: absolute; left: .75rem; top: 50%;
        transform: translateY(-50%); color: var(--muted); font-size: .85rem; pointer-events: none;
    }
    .search-wrap input { padding-left: 2.1rem; }

    .pet-grid-card {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden;
        transition: transform .2s, box-shadow .2s; box-shadow: var(--shadow-sm);
        display: flex; flex-direction: column;
    }
    .pet-grid-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
    .pet-img { height: 180px; width: 100%; object-fit: cover; }
    .pet-img-ph {
        height: 180px; background: var(--coral-light);
        display: flex; align-items: center; justify-content: center; font-size: 3.5rem;
    }
    .pet-body { padding: .9rem 1rem; flex: 1; display: flex; flex-direction: column; gap: .25rem; }
    .pet-name { font-weight: 700; font-size: .95rem; color: var(--navy); margin: 0; }
    .pet-meta { font-size: .75rem; color: var(--muted); }
    .pet-footer {
        padding: .7rem 1rem; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        background: var(--bg);
    }
    .status-pill {
        font-size: .67rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .04em; padding: .25em .75em; border-radius: 20px;
    }
    .pill-available  { background: var(--sage-light);  color: #2D5A3D; }
    .pill-pending    { background: var(--gold-light);  color: #7A5A1A; }
    .pill-adopted    { background: var(--coral-light); color: var(--coral-dark); }
    .pill-treatment  { background: rgba(45,49,71,.08); color: var(--navy); }

    .pet-action-btn {
        width: 28px; height: 28px; border-radius: 7px;
        border: 1px solid var(--border); background: var(--white);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .8rem; color: var(--muted); text-decoration: none; transition: all .15s;
    }
    .pet-action-btn:hover        { background: var(--coral-light); color: var(--coral); border-color: transparent; }
    .pet-action-btn.danger:hover { background: #FEF0EE; color: #8B2516; border-color: transparent; }

    .approval-badge {
        position: absolute; top: 9px; right: 9px;
        font-size: .63rem; font-weight: 700; padding: .22em .65em; border-radius: 20px;
    }
</style>
@endpush

@section('content')

{{-- Filter Bar --}}
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4 col-lg-5">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Search pets by name…">
            </div>
        </div>
        <div class="col-md-3 col-lg-2">
            <select name="category" class="form-select form-select-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-lg-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                @foreach(['available','pending','adopted','under_treatment'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>
                        {{ ucfirst(str_replace('_',' ',$s)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('staff.pets.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- Results count --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="mb-0" style="font-size:.83rem; color:var(--muted);">
        Showing <strong style="color:var(--navy);">{{ $pets->firstItem() }}–{{ $pets->lastItem() }}</strong>
        of <strong style="color:var(--navy);">{{ $pets->total() }}</strong> pets
    </p>
</div>

@if($pets->isEmpty())
    <div class="card">
        <div class="empty-state py-5">
            <span class="empty-icon">🐾</span>
            <h5>No Pets Found</h5>
            <p>Try adjusting your filters or add a new pet.</p>
            <a href="{{ route('staff.pets.create') }}" class="btn btn-primary btn-sm mt-2">
                <i class="bi bi-plus-lg me-1"></i> Add First Pet
            </a>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($pets as $pet)
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="pet-grid-card">
                <div class="position-relative">
                    @if($pet->primary_image)
                        <img src="{{ $pet->primary_image_url }}" class="pet-img" alt="{{ $pet->name }}">
                    @else
                        <div class="pet-img-ph">
                            {{ ($pet->category->name ?? '') === 'Dog' ? '🐶' : (($pet->category->name ?? '') === 'Cat' ? '🐱' : '🐾') }}
                        </div>
                    @endif
                    @if(!$pet->is_admin_approved)
                        <span class="approval-badge" style="background:var(--gold-light); color:#7A5A1A;">
                            ⏳ Pending Approval
                        </span>
                    @else
                        <span class="approval-badge" style="background:var(--sage-light); color:#2D5A3D;">
                            ✓ Approved
                        </span>
                    @endif
                </div>

                <div class="pet-body">
                    <p class="pet-name">{{ $pet->name }}</p>
                    <p class="pet-meta">
                        {{ $pet->category->name ?? '—' }}
                        @if($pet->breed) · {{ $pet->breed->name }} @endif
                    </p>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        <span style="font-size:.7rem; color:var(--muted); background:var(--bg); padding:.18em .55em; border-radius:5px;">
                            {{ ucfirst($pet->gender) }}
                        </span>
                        <span style="font-size:.7rem; color:var(--muted); background:var(--bg); padding:.18em .55em; border-radius:5px;">
                            {{ $pet->age_label }}
                        </span>
                        @if($pet->adoption_fee > 0)
                        <span style="font-size:.7rem; color:#2D5A3D; background:var(--sage-light); padding:.18em .55em; border-radius:5px; font-weight:600;">
                            ₱{{ number_format($pet->adoption_fee) }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="pet-footer">
                    @php
                        $pillMap = ['available'=>'pill-available','pending'=>'pill-pending','adopted'=>'pill-adopted','under_treatment'=>'pill-treatment'];
                    @endphp
                    <span class="status-pill {{ $pillMap[$pet->status] ?? 'pill-pending' }}">
                        {{ ucfirst(str_replace('_',' ',$pet->status)) }}
                    </span>
                    <div class="d-flex gap-1">
                        <a href="{{ route('staff.pets.show', $pet) }}" class="pet-action-btn" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('staff.pets.edit', $pet) }}" class="pet-action-btn" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('staff.pets.destroy', $pet) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete {{ addslashes($pet->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="pet-action-btn danger" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($pets->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $pets->withQueryString()->links() }}
    </div>
    @endif
@endif

@endsection