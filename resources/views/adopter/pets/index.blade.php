@extends('layouts.app')
@section('title', 'Browse Pets')
@section('page-title', 'Browse Pets')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Browse Pets</li>
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

    /* Pet card */
    .pet-card-adopt {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden;
        transition: transform .2s, box-shadow .2s; box-shadow: var(--shadow-sm);
        display: flex; flex-direction: column; height: 100%;
    }
    .pet-card-adopt:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
    .pet-card-adopt .pc-img { height: 200px; width: 100%; object-fit: cover; }
    .pet-card-adopt .pc-placeholder {
        height: 200px; background: var(--coral-light);
        display: flex; align-items: center; justify-content: center; font-size: 4rem;
    }
    .pet-card-adopt .pc-body { padding: 1rem; flex: 1; display: flex; flex-direction: column; gap: .3rem; }
    .pet-card-adopt .pc-name { font-size: 1rem; font-weight: 700; color: var(--navy); margin: 0; }
    .pet-card-adopt .pc-meta { font-size: .78rem; color: var(--muted); }
    .pet-card-adopt .pc-fee {
        font-size: .78rem; font-weight: 700; color: #2D5A3D;
        background: var(--sage-light); padding: .2em .65em; border-radius: 20px; display: inline-block;
    }
    .pet-card-adopt .pc-footer {
        padding: .85rem 1rem; border-top: 1px solid var(--border); margin-top: auto;
    }
    .btn-adopt-now {
        display: block; width: 100%; text-align: center; padding: .55rem;
        background: var(--coral); color: #fff; border: none; border-radius: var(--radius-sm);
        font-size: .855rem; font-weight: 600; text-decoration: none;
        transition: background .15s, transform .1s;
    }
    .btn-adopt-now:hover { background: var(--coral-dark); color: #fff; transform: translateY(-1px); }

    /* Tag chips */
    .pet-tag {
        font-size: .68rem; padding: .22em .6em; border-radius: 6px;
        background: var(--bg); color: var(--muted); font-weight: 500;
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
                       value="{{ request('search') }}" placeholder="Search by name, breed…">
            </div>
        </div>
        <div class="col-md-3 col-lg-2">
            <select name="category" class="form-select form-select-sm">
                <option value="">All Types</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-lg-2">
            <select name="gender" class="form-select form-select-sm">
                <option value="">Any Gender</option>
                <option value="male"   @selected(request('gender') === 'male')>Male</option>
                <option value="female" @selected(request('gender') === 'female')>Female</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('adopter.pets.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- Results count --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <p class="mb-0" style="font-size:.83rem; color:var(--muted);">
        <strong style="color:var(--navy);">{{ $pets->total() }}</strong> pets available for adoption
    </p>
</div>

{{-- Grid --}}
@if($pets->isEmpty())
    <div class="card">
        <div class="empty-state py-5">
            <span class="empty-icon">🐾</span>
            <h5>No Pets Found</h5>
            <p>Try adjusting your search filters.</p>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($pets as $pet)
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="pet-card-adopt">
                @if($pet->primary_image)
                    <img src="{{ $pet->primary_image_url }}" class="pc-img" alt="{{ $pet->name }}">
                @else
                    <div class="pc-placeholder">
                        {{ ($pet->category->name ?? '') === 'Dog' ? '🐶' : (($pet->category->name ?? '') === 'Cat' ? '🐱' : '🐾') }}
                    </div>
                @endif

                <div class="pc-body">
                    <p class="pc-name">{{ $pet->name }}</p>
                    <p class="pc-meta">
                        {{ $pet->category->name ?? '—' }}
                        @if($pet->breed) · {{ $pet->breed->name }} @endif
                    </p>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        <span class="pet-tag">{{ ucfirst($pet->gender) }}</span>
                        <span class="pet-tag">{{ $pet->age_label }}</span>
                        @if($pet->size)
                            <span class="pet-tag">{{ ucfirst($pet->size) }}</span>
                        @endif
                        @if($pet->is_vaccinated)
                            <span class="pet-tag" style="background:var(--sage-light); color:#2D5A3D;">💉 Vaccinated</span>
                        @endif
                        @if($pet->is_neutered)
                            <span class="pet-tag" style="background:var(--sage-light); color:#2D5A3D;">✂️ Neutered</span>
                        @endif
                    </div>
                    @if($pet->adoption_fee > 0)
                        <div class="mt-2">
                            <span class="pc-fee">₱{{ number_format($pet->adoption_fee) }} adoption fee</span>
                        </div>
                    @else
                        <div class="mt-2">
                            <span class="pc-fee" style="background:var(--coral-subtle); color:var(--coral);">Free adoption</span>
                        </div>
                    @endif
                </div>

                <div class="pc-footer">
                    <a href="{{ route('adopter.pets.show', $pet) }}" class="btn-adopt-now">
                        <i class="bi bi-heart me-1"></i> Meet {{ $pet->name }}
                    </a>
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