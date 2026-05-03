@extends('layouts.app')

@section('title', 'Pet Health Records')
@section('page-title', 'Pet Health Records')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Pet Health Records</li>
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

    /* Pet health card */
    .health-card {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden;
        transition: transform .2s, box-shadow .2s; box-shadow: var(--shadow-sm);
        display: flex; flex-direction: column;
    }
    .health-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
    .hc-img { height: 160px; width: 100%; object-fit: cover; }
    .hc-ph {
        height: 160px; background: var(--sage-light);
        display: flex; align-items: center; justify-content: center; font-size: 3.5rem;
    }
    .hc-body { padding: .9rem 1rem; flex: 1; display: flex; flex-direction: column; gap: .3rem; }
    .hc-name { font-size: .95rem; font-weight: 700; color: var(--navy); margin: 0; }
    .hc-meta { font-size: .75rem; color: var(--muted); }
    .hc-footer {
        padding: .7rem 1rem; border-top: 1px solid var(--border);
        background: var(--bg); display: flex; align-items: center; gap: .5rem;
    }
    .health-badge {
        font-size: .65rem; font-weight: 700; padding: .25em .7em;
        border-radius: 20px; display: inline-block; text-transform: uppercase; letter-spacing: .04em;
    }
    .btn-view-records {
        display: block; width: 100%; text-align: center; padding: .5rem;
        background: var(--coral); color: #fff; border: none;
        border-radius: var(--radius-sm); font-size: .82rem; font-weight: 600;
        text-decoration: none; transition: background .15s;
    }
    .btn-view-records:hover { background: var(--coral-dark); color: #fff; }
</style>
@endpush

@section('content')

{{-- Filter Bar --}}
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-5">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Search by pet name…">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                @foreach(['available','pending','under_treatment','adopted'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>
                        {{ ucfirst(str_replace('_',' ',$s)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('vet.pets.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- Results count --}}
<div class="mb-3">
    <p class="mb-0" style="font-size:.83rem; color:var(--muted);">
        <strong style="color:var(--navy);">{{ $pets->total() }}</strong> pets in the system
    </p>
</div>

@if($pets->isEmpty())
    <div class="card">
        <div class="empty-state py-5">
            <span class="empty-icon">🐾</span>
            <h5>No Pets Found</h5>
            <p>No pets match your search filters.</p>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($pets as $pet)
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="health-card">
                {{-- Image --}}
                @if($pet->primary_image)
                    <img src="{{ $pet->primary_image_url }}" class="hc-img" alt="{{ $pet->name }}">
                @else
                    <div class="hc-ph">
                        {{ ($pet->category->name ?? '') === 'Dog' ? '🐶' : (($pet->category->name ?? '') === 'Cat' ? '🐱' : '🐾') }}
                    </div>
                @endif

                {{-- Body --}}
                <div class="hc-body">
                    <p class="hc-name">{{ $pet->name }}</p>
                    <p class="hc-meta">
                        {{ $pet->petCategory->name ?? '—' }}
                        @if($pet->breed) · {{ $pet->breed->name }} @endif
                    </p>

                    {{-- Health badges --}}
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        @if($pet->is_vaccinated)
                            <span class="health-badge" style="background:var(--sage-light); color:#2D5A3D;">💉 Vaccinated</span>
                        @else
                            <span class="health-badge" style="background:#FEF0EE; color:#8B2516;">💉 Not Vaccinated</span>
                        @endif
                        @if($pet->is_neutered)
                            <span class="health-badge" style="background:var(--sage-light); color:#2D5A3D;">✂️ Neutered</span>
                        @endif
                        @if($pet->is_vet_approved)
                            <span class="health-badge" style="background:var(--sage-light); color:#2D5A3D;">✓ Vet Approved</span>
                        @else
                            <span class="health-badge" style="background:var(--gold-light); color:#7A5A1A;">⏳ Needs Review</span>
                        @endif
                    </div>

                    <div style="font-size:.73rem; color:var(--muted); margin-top:.4rem;">
                        <i class="bi bi-clipboard-pulse me-1"></i>
                        {{ $pet->medicalRecords->count() }} medical {{ Str::plural('record', $pet->medicalRecords->count()) }}
                    </div>
                </div>

                {{-- Footer --}}
                <div class="hc-footer">
                    <a href="{{ route('vet.pets.show', $pet) }}" class="btn-view-records">
                        <i class="bi bi-clipboard-pulse me-1"></i> View Health Records
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