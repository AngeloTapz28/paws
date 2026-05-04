@extends('layouts.app')
@section('title', 'Browse Pets')
@section('page-title', 'Browse Pets')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Browse Pets</li>
@endsection

@push('styles')
<style>
    /* ── Filter bar ── */
    .filter-bar {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1rem 1.25rem;
        margin-bottom: 1rem; box-shadow: var(--shadow-sm);
    }
    .search-wrap { position: relative; }
    .search-wrap .bi-search {
        position: absolute; left: .75rem; top: 50%;
        transform: translateY(-50%); color: var(--muted); font-size: .85rem; pointer-events: none;
    }
    .search-wrap input { padding-left: 2.1rem; }

    /* ── Pet card ── */
    .pet-card-adopt {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden;
        display: flex; flex-direction: column;
        box-shadow: var(--shadow-sm);
        transition: transform .22s ease, box-shadow .22s ease;
    }
    .pet-card-adopt:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }

    /* ── Image ── */
    .pc-img-wrap { position: relative; overflow: hidden; height: 220px; }
    .pc-img {
        width: 100%; height: 220px; object-fit: cover;
        transition: transform .35s ease;
        display: block;
    }
    .pet-card-adopt:hover .pc-img { transform: scale(1.07); }
    .pc-placeholder {
        height: 220px; background: var(--coral-light);
        display: flex; align-items: center; justify-content: center;
        font-size: 4rem;
    }

    /* ── Body ── */
    .pc-body { padding: .9rem 1rem; flex: 1; display: flex; flex-direction: column; gap: .3rem; }
    .pc-name { font-size: 1rem; font-weight: 800; color: var(--navy); margin: 0; }
    .pc-meta { font-size: .76rem; color: var(--muted); margin: 0; }

    /* ── Tags ── */
    .pc-tags { display: flex; gap: .35rem; flex-wrap: wrap; margin-top: .3rem; }
    .pet-tag {
        font-size: .68rem; font-weight: 500; padding: .22em .65em;
        border-radius: 6px; background: var(--bg); color: var(--text);
    }
    .tag-vaccinated {
        font-size: .68rem; font-weight: 600; padding: .22em .65em;
        border-radius: 6px; background: var(--sage-light); color: #2D5A3D;
        display: inline-flex; align-items: center; gap: .25rem;
    }

    /* ── Fee ── */
    .pc-fee {
        font-size: .8rem; font-weight: 700; color: var(--coral);
        background: var(--coral-subtle); padding: .25em .75em;
        border-radius: 20px; display: inline-block; margin-top: .35rem;
    }

    /* ── Footer button ── */
    .pc-footer { padding: .85rem 1rem; border-top: 1px solid var(--border); }
    .btn-adopt-now {
        display: block; width: 100%; text-align: center; padding: .6rem;
        background: var(--coral); color: #fff; border: none; border-radius: var(--radius-sm);
        font-size: .87rem; font-weight: 600; text-decoration: none;
        transition: background .15s, transform .12s, box-shadow .15s;
        display: flex; align-items: center; justify-content: center; gap: .4rem;
    }
    .btn-adopt-now:hover {
        background: var(--coral-dark); color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(217,119,87,.35);
    }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes cardPop {
        0%   { opacity: 0; transform: translateY(22px) scale(.96); }
        60%  { transform: translateY(-4px) scale(1.01); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Filter bar */
    .filter-bar { animation: fadeDown .4s ease both; }

    /* Results row */
    .results-row { opacity: 0; animation: fadeUp .4s ease .2s both; }

    /* Pet columns — JS staggers */
    .pet-col { opacity: 0; }
    .pet-col.visible { animation: cardPop .45s cubic-bezier(.25,.46,.45,.94) both; }
</style>
@endpush

@section('content')

{{-- ── Filter Bar ── --}}
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-sm-5 col-md-4">
            <div class="search-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="Search by name, breed...">
            </div>
        </div>
        <div class="col-sm-3 col-md-2">
            <select name="category" class="form-select form-select-sm">
                <option value="">All Types</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3 col-md-2">
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

{{-- ── Results Count ── --}}
<div class="results-row d-flex align-items-center justify-content-between mb-3">
    <p class="mb-0" style="font-size:.83rem; color:var(--muted);">
        <strong style="color:var(--navy);">{{ $pets->total() }}</strong> pets available for adoption
    </p>
    @if($pets->total() > 0)
    <span style="font-size:.75rem; color:var(--muted);">
        Showing {{ $pets->firstItem() }}–{{ $pets->lastItem() }}
    </span>
    @endif
</div>

{{-- ── Pet Grid ── --}}
@if($pets->isEmpty())
    <div class="card">
        <div class="empty-state py-5">
            <span class="empty-icon">🐾</span>
            <h5>No Pets Found</h5>
            <p>Try adjusting your search filters.</p>
            <a href="{{ route('adopter.pets.index') }}" class="btn btn-primary btn-sm">Clear Filters</a>
        </div>
    </div>
@else
    <div class="row g-3" id="petGrid">
        @foreach($pets as $i => $pet)
        <div class="col-sm-6 col-md-4 col-xl-3 pet-col" data-index="{{ $i }}">
            <div class="pet-card-adopt">

                {{-- Image ── --}}
                <div class="pc-img-wrap">
                    @if($pet->primary_image)
                        <img src="{{ $pet->primary_image_url }}" class="pc-img" alt="{{ $pet->name }}">
                    @else
                        <div class="pc-placeholder">
                            {{ ($pet->category->name ?? '') === 'Dog' ? '🐶' : (($pet->category->name ?? '') === 'Cat' ? '🐱' : '🐾') }}
                        </div>
                    @endif
                </div>

                {{-- Body ── --}}
                <div class="pc-body">
                    <p class="pc-name">{{ $pet->name }}</p>
                    <p class="pc-meta">
                        {{ $pet->category->name ?? '—' }}
                        @if($pet->breed) · {{ $pet->breed->name }} @endif
                    </p>
                    <div class="pc-tags">
                        @if($pet->gender)
                        <span class="pet-tag">{{ ucfirst($pet->gender) }}</span>
                        @endif
                        @if($pet->size)
                        <span class="pet-tag">{{ ucfirst($pet->size) }}</span>
                        @endif
                        @if($pet->is_vaccinated)
                        <span class="tag-vaccinated">
                            <i class="bi bi-patch-check-fill" style="font-size:.65rem;"></i> Vaccinated
                        </span>
                        @endif
                    </div>
                    <span class="pc-fee">₱{{ number_format($pet->adoption_fee) }} adoption fee</span>
                </div>

                {{-- Footer ── --}}
                <div class="pc-footer">
                    <a href="{{ route('adopter.pets.show', $pet) }}" class="btn-adopt-now">
                        <i class="bi bi-heart"></i> Meet {{ $pet->name }}
                    </a>
                </div>

            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination ── --}}
    @if($pets->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $pets->withQueryString()->links() }}
    </div>
    @endif
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Stagger pet cards popping in — row-aware
    document.querySelectorAll('.pet-col').forEach(col => {
        const i     = parseInt(col.dataset.index);
        const row   = Math.floor(i / 4); // 4 per row on xl
        const col_i = i % 4;
        const delay = 280 + (row * 110) + (col_i * 55);
        setTimeout(() => col.classList.add('visible'), delay);
    });
});
</script>
@endpush