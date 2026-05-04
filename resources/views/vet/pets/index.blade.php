@extends('layouts.app')

@section('title', 'Pet Health Records')
@section('page-title', 'Pet Health Records')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Pet Health Records</li>
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

    /* ── Health card ── */
    .health-card {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden;
        display: flex; flex-direction: column;
        box-shadow: var(--shadow-sm);
        transition: transform .22s ease, box-shadow .22s ease;
    }
    .health-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }

    /* ── Image ── */
    .hc-img-wrap { position: relative; overflow: hidden; height: 185px; }
    .hc-img {
        width: 100%; height: 185px; object-fit: cover;
        display: block; transition: transform .35s ease;
    }
    .health-card:hover .hc-img { transform: scale(1.07); }
    .hc-img-ph {
        height: 185px; background: var(--coral-light);
        display: flex; align-items: center; justify-content: center; font-size: 3.5rem;
    }

    /* ── Body ── */
    .hc-body { padding: .9rem 1rem; flex: 1; display: flex; flex-direction: column; gap: .3rem; }
    .hc-name { font-size: .95rem; font-weight: 800; color: var(--navy); margin: 0; }
    .hc-meta { font-size: .75rem; color: var(--muted); margin: 0; }

    /* ── Health badges ── */
    .health-badge {
        font-size: .67rem; font-weight: 600; padding: .25em .65em;
        border-radius: 20px; display: inline-flex; align-items: center; gap: .25rem;
    }
    .hb-vaccinated    { background: var(--sage-light); color: #2D5A3D; }
    .hb-not-vaccinated{ background: #FEF0EE;           color: #8B2516; }
    .hb-approved      { background: var(--sage-light); color: #2D5A3D; }
    .hb-needs-review  { background: var(--gold-light); color: #7A5A1A; }
    .hb-neutered      { background: var(--sage-light); color: #2D5A3D; }

    /* ── Record count ── */
    .record-count { font-size:.72rem; color:var(--muted); display:flex; align-items:center; gap:.3rem; margin-top:.25rem; }

    /* ── Footer button ── */
    .hc-footer { padding: .75rem 1rem; border-top: 1px solid var(--border); }
    .btn-view-records {
        display: flex; width: 100%; align-items: center; justify-content: center; gap: .4rem;
        background: var(--coral); color: #fff; border: none; border-radius: var(--radius-sm);
        padding: .55rem; font-size: .83rem; font-weight: 600; text-decoration: none;
        transition: background .15s, transform .12s, box-shadow .15s;
    }
    .btn-view-records:hover {
        background: var(--coral-dark); color: #fff;
        transform: translateY(-1px); box-shadow: 0 4px 12px rgba(217,119,87,.3);
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
        60%  { transform: translateY(-3px) scale(1.01); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    .filter-bar  { animation: fadeDown .4s ease both; }
    .results-row { opacity: 0; animation: fadeUp .4s ease .2s both; }

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
                       value="{{ request('search') }}" placeholder="Search by pet name...">
            </div>
        </div>
        <div class="col-sm-3 col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="approved"     @selected(request('status') === 'approved')>Vet Approved</option>
                <option value="needs_review" @selected(request('status') === 'needs_review')>Needs Review</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('vet.pets.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- ── Results count ── --}}
<div class="results-row mb-3">
    <p class="mb-0" style="font-size:.83rem; color:var(--muted);">
        <strong style="color:var(--navy);">{{ $pets->total() }}</strong> pets in the system
    </p>
</div>

{{-- ── Pet Health Grid ── --}}
@if($pets->isEmpty())
    <div class="card">
        <div class="empty-state py-5">
            <span class="empty-icon">🩺</span>
            <h5>No Pets Found</h5>
            <p>Try adjusting your search filters.</p>
        </div>
    </div>
@else
    <div class="row g-3" id="petGrid">
        @foreach($pets as $i => $pet)
        <div class="col-sm-6 col-md-4 col-xl-3 pet-col" data-index="{{ $i }}">
            <div class="health-card">

                {{-- Image ── --}}
                <div class="hc-img-wrap">
                    @if($pet->primary_image)
                        <img src="{{ $pet->primary_image_url }}" class="hc-img" alt="{{ $pet->name }}">
                    @else
                        <div class="hc-img-ph">
                            {{ ($pet->category->name ?? '') === 'Dog' ? '🐶' : (($pet->category->name ?? '') === 'Cat' ? '🐱' : '🐾') }}
                        </div>
                    @endif
                </div>

                {{-- Body ── --}}
                <div class="hc-body">
                    <p class="hc-name">{{ $pet->name }}</p>
                    <p class="hc-meta">
                        {{ $pet->petCategory->name ?? '—' }}
                        @if($pet->breed) · {{ $pet->breed->name }} @endif
                    </p>

                    {{-- Health badges ── --}}
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        @if($pet->is_vaccinated)
                            <span class="health-badge hb-vaccinated">
                                <i class="bi bi-patch-check-fill" style="font-size:.6rem;"></i> Vaccinated
                            </span>
                        @else
                            <span class="health-badge hb-not-vaccinated">
                                <i class="bi bi-exclamation-circle-fill" style="font-size:.6rem;"></i> Not Vaccinated
                            </span>
                        @endif

                        @if($pet->is_vet_approved)
                            <span class="health-badge hb-approved">
                                <i class="bi bi-check-circle-fill" style="font-size:.6rem;"></i> Vet Approved
                            </span>
                        @else
                            <span class="health-badge hb-needs-review">
                                <i class="bi bi-clock-fill" style="font-size:.6rem;"></i> Needs Review
                            </span>
                        @endif

                        @if($pet->is_neutered)
                            <span class="health-badge hb-neutered">
                                <i class="bi bi-scissors" style="font-size:.6rem;"></i> Neutered
                            </span>
                        @endif
                    </div>

                    <div class="record-count">
                        <i class="bi bi-clipboard-pulse"></i>
                        {{ $pet->medicalRecords->count() }} medical {{ Str::plural('record', $pet->medicalRecords->count()) }}
                    </div>
                </div>

                {{-- Footer ── --}}
                <div class="hc-footer">
                    <a href="{{ route('vet.pets.show', $pet) }}" class="btn-view-records">
                        <i class="bi bi-clipboard-pulse"></i> View Health Records
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Row-aware stagger pop
    document.querySelectorAll('.pet-col').forEach(col => {
        const i     = parseInt(col.dataset.index);
        const row   = Math.floor(i / 4);
        const col_i = i % 4;
        const delay = 280 + (row * 110) + (col_i * 55);
        setTimeout(() => col.classList.add('visible'), delay);
    });
});
</script>
@endpush