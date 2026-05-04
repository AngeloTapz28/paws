@extends('layouts.app')

@section('title', 'All Pets')
@section('page-title', 'Pet Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">All Pets</li>
@endsection

@section('page-actions')
    <a href="{{ route('staff.pets.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Pet (Staff)
    </a>
@endsection

@push('styles')
<style>
    /* ── Filter bar ── */
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

    /* ── Pet card ── */
    .pet-grid-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        display: flex; flex-direction: column;
        transition: transform .22s ease, box-shadow .22s ease;
    }
    .pet-grid-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    /* ── Image ── */
    .pet-img-wrap {
        position: relative;
        overflow: hidden;
        height: 185px;
    }
    .pet-img {
        width: 100%; height: 185px;
        object-fit: cover;
        transition: transform .35s ease;
    }
    .pet-grid-card:hover .pet-img { transform: scale(1.06); }

    .pet-img-placeholder {
        height: 185px; width: 100%;
        background: var(--coral-light);
        display: flex; align-items: center; justify-content: center;
        font-size: 3.5rem;
        transition: transform .35s ease;
    }
    .pet-grid-card:hover .pet-img-placeholder { transform: scale(1.04); }

    /* ── Approval badge ── */
    .approval-overlay {
        position: absolute; top: 10px; right: 10px;
        font-size: .65rem; font-weight: 700;
        padding: .3em .75em; border-radius: 20px;
        letter-spacing: .03em;
        backdrop-filter: blur(4px);
    }
    .approval-approved { background: rgba(143,175,154,.9); color: #fff; }
    .approval-pending  { background: rgba(230,194,122,.9); color: #7A5A1A; }

    /* ── Card body ── */
    .pet-body { padding: .9rem 1rem; flex: 1; display: flex; flex-direction: column; gap: .4rem; }
    .pet-name { font-size: .95rem; font-weight: 700; color: var(--navy); margin: 0; line-height: 1.25; }
    .pet-meta { font-size: .75rem; color: var(--muted); margin: 0; }

    .pet-tags { display: flex; gap: .4rem; flex-wrap: wrap; margin-top: .15rem; }
    .pet-tag {
        font-size: .68rem; font-weight: 600; padding: .22em .65em;
        border-radius: 20px; display: inline-block;
    }
    .tag-gender { background: var(--coral-subtle); color: var(--coral); }
    .tag-fee    { background: var(--sage-light);   color: #2D5A3D; }

    .pet-added-by { font-size: .72rem; color: var(--muted); margin-top: auto; padding-top: .4rem; }

    /* ── Card footer ── */
    .pet-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding: .6rem 1rem;
        border-top: 1px solid var(--border);
        background: var(--bg);
    }
    .status-chip {
        font-size: .67rem; font-weight: 700; padding: .28em .75em;
        border-radius: 20px; text-transform: uppercase; letter-spacing: .04em;
    }
    .chip-available  { background: var(--sage-light); color: #2D5A3D; }
    .chip-adopted    { background: var(--coral-light); color: var(--coral-dark); }
    .chip-pending    { background: var(--gold-light); color: #7A5A1A; }
    .chip-treatment  { background: rgba(45,49,71,.08); color: var(--navy); }

    .view-btn {
        width: 28px; height: 28px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); background: var(--white);
        color: var(--muted); text-decoration: none; font-size: .85rem;
        transition: all .15s;
    }
    .view-btn:hover { background: var(--coral-subtle); color: var(--coral); border-color: var(--coral-light); }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes cardPop {
        0%   { opacity: 0; transform: translateY(20px) scale(.97); }
        60%  { transform: translateY(-3px) scale(1.01); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Filter bar & results row */
    .filter-bar   { animation: fadeDown .4s ease both; }
    .results-row  { opacity: 0; animation: fadeUp .4s ease .25s both; }

    /* Cards start hidden — JS staggers them */
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
                       value="{{ request('search') }}" placeholder="Search pets by name...">
            </div>
        </div>
        <div class="col-sm-3 col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                @foreach(['available','adopted','pending','under_treatment'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3 col-md-2">
            <select name="approval" class="form-select form-select-sm">
                <option value="">All Approvals</option>
                <option value="approved" @selected(request('approval') === 'approved')>Approved</option>
                <option value="pending"  @selected(request('approval') === 'pending')>Pending</option>
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('admin.pets.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- ── Results header ── --}}
<div class="results-row d-flex align-items-center justify-content-between mb-3">
    <p class="mb-0" style="font-size:.83rem; color:var(--muted);">
        Showing <strong style="color:var(--navy);">{{ $pets->firstItem() }}–{{ $pets->lastItem() }}</strong>
        of <strong style="color:var(--navy);">{{ $pets->total() }}</strong> pets
    </p>
    @if($pets->where('is_admin_approved', false)->count() > 0)
        <span style="font-size:.78rem; background:var(--gold-light); color:#7A5A1A;
                     padding:.3em .9em; border-radius:20px; font-weight:600;">
            <i class="bi bi-clock me-1"></i>
            {{ $pets->where('is_admin_approved', false)->count() }} pending approval on this page
        </span>
    @endif
</div>

{{-- ── Pet Grid ── --}}
@if($pets->isEmpty())
    <div class="card">
        <div class="empty-state py-5">
            <span class="empty-icon">🐾</span>
            <h5>No Pets Found</h5>
            <p>Pets added by staff will appear here for approval.</p>
        </div>
    </div>
@else
    <div class="row g-3" id="petGrid">
        @foreach($pets as $i => $pet)
        <div class="col-sm-6 col-md-4 col-xl-3 pet-col" data-index="{{ $i }}">
            <div class="pet-grid-card">

                {{-- Image with approval badge ── --}}
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

                {{-- Body ── --}}
                <div class="pet-body">
                    <p class="pet-name">{{ $pet->name }}</p>
                    <p class="pet-meta">
                        {{ $pet->category->name ?? '—' }}
                        @if($pet->breed) · {{ $pet->breed->name }} @endif
                    </p>
                    <div class="pet-tags">
                        <span class="pet-tag tag-gender">{{ ucfirst($pet->gender) }}</span>
                        <span class="pet-tag tag-fee">₱{{ number_format($pet->adoption_fee) }}</span>
                    </div>
                    <p class="pet-added-by">
                        <i class="bi bi-person me-1"></i>Added by {{ $pet->addedBy->name ?? '—' }}
                    </p>
                </div>

                {{-- Footer ── --}}
                <div class="pet-footer">
                    @php
                        $statusMap = [
                            'available'       => 'chip-available',
                            'adopted'         => 'chip-adopted',
                            'pending'         => 'chip-pending',
                            'under_treatment' => 'chip-treatment',
                        ];
                    @endphp
                    <span class="status-chip {{ $statusMap[$pet->status] ?? 'chip-pending' }}">
                        {{ strtoupper(str_replace('_', ' ', $pet->status)) }}
                    </span>

                    <div class="d-flex gap-1">
                        {{-- Approve button if pending ── --}}
                        @if(!$pet->is_admin_approved)
                        <form action="{{ route('admin.pets.approve', $pet) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="view-btn" title="Approve" style="color:var(--sage); border-color:var(--sage-light);">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('staff.pets.show', $pet) }}" class="view-btn" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
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
    // Stagger pet cards popping in
    document.querySelectorAll('.pet-col').forEach(col => {
        const i     = parseInt(col.dataset.index);
        // Row-aware stagger: cards in same row animate together, next row delayed
        const row   = Math.floor(i / 4);
        const col_i = i % 4;
        const delay = 300 + (row * 120) + (col_i * 60);
        setTimeout(() => col.classList.add('visible'), delay);
    });
});
</script>
@endpush