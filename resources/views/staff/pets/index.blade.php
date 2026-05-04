@extends('layouts.app')

@section('title', 'Pet Management')
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

    /* ── Pet grid card ── */
    .pet-grid-card {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); overflow: hidden;
        display: flex; flex-direction: column;
        box-shadow: var(--shadow-sm);
        transition: transform .22s ease, box-shadow .22s ease;
    }
    .pet-grid-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }

    /* ── Image ── */
    .pet-img-wrap { position: relative; overflow: hidden; height: 185px; }
    .pet-img {
        width: 100%; height: 185px; object-fit: cover;
        display: block; transition: transform .35s ease;
    }
    .pet-grid-card:hover .pet-img { transform: scale(1.07); }
    .pet-img-ph {
        height: 185px; background: var(--coral-light);
        display: flex; align-items: center; justify-content: center; font-size: 3.5rem;
    }

    /* ── Approval badge ── */
    .approval-badge {
        position: absolute; top: 10px; right: 10px;
        font-size: .65rem; font-weight: 700; padding: .3em .75em;
        border-radius: 20px; backdrop-filter: blur(4px); letter-spacing: .03em;
    }

    /* ── Body ── */
    .pet-body { padding: .9rem 1rem; flex: 1; display: flex; flex-direction: column; gap: .3rem; }
    .pet-name { font-size: .95rem; font-weight: 800; color: var(--navy); margin: 0; }
    .pet-meta { font-size: .75rem; color: var(--muted); margin: 0; }

    /* ── Tags ── */
    .pet-tags { display: flex; gap: .35rem; flex-wrap: wrap; margin-top: .25rem; }
    .pet-tag  { font-size: .68rem; font-weight: 600; padding: .22em .65em; border-radius: 6px; background: var(--bg); color: var(--text); }
    .tag-fee  { background: var(--sage-light); color: #2D5A3D; }

    /* ── Footer ── */
    .pet-footer {
        padding: .65rem 1rem; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        background: var(--bg);
    }
    .status-chip {
        font-size: .67rem; font-weight: 700; padding: .28em .75em;
        border-radius: 20px; text-transform: uppercase; letter-spacing: .04em;
    }
    .chip-available  { background: var(--sage-light);         color: #2D5A3D; }
    .chip-adopted    { background: var(--coral-light);         color: var(--coral-dark); }
    .chip-pending    { background: var(--gold-light);          color: #7A5A1A; }
    .chip-treatment  { background: rgba(45,49,71,.08);         color: var(--navy); }

    /* ── Action buttons ── */
    .action-btn {
        width: 28px; height: 28px; border-radius: 7px;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--border); background: var(--white);
        color: var(--muted); font-size: .82rem; text-decoration: none;
        transition: all .15s; cursor: pointer; padding: 0;
    }
    .action-btn:hover        { background: var(--coral-subtle); color: var(--coral); border-color: var(--coral-light); }
    .action-btn.danger:hover { background: #FEF0EE; color: #8B2516; border-color: #FECDD3; }

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
                       value="{{ request('search') }}" placeholder="Search pets by name...">
            </div>
        </div>
        <div class="col-sm-3 col-md-2">
            <select name="category" class="form-select form-select-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                        {{ $cat->icon }} {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3 col-md-2">
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

{{-- ── Results count ── --}}
<div class="results-row d-flex align-items-center justify-content-between mb-3">
    <p class="mb-0" style="font-size:.83rem; color:var(--muted);">
        Showing <strong style="color:var(--navy);">{{ $pets->firstItem() }}–{{ $pets->lastItem() }}</strong>
        of <strong style="color:var(--navy);">{{ $pets->total() }}</strong> pets
    </p>
</div>

{{-- ── Pet Grid ── --}}
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
    <div class="row g-3" id="petGrid">
        @foreach($pets as $i => $pet)
        <div class="col-sm-6 col-md-4 col-xl-3 pet-col" data-index="{{ $i }}">
            <div class="pet-grid-card">

                {{-- Image + Approval badge ── --}}
                <div class="pet-img-wrap">
                    @if($pet->primary_image)
                        <img src="{{ $pet->primary_image_url }}" class="pet-img" alt="{{ $pet->name }}">
                    @else
                        <div class="pet-img-ph">
                            {{ ($pet->category->name ?? '') === 'Dog' ? '🐶' : (($pet->category->name ?? '') === 'Cat' ? '🐱' : '🐾') }}
                        </div>
                    @endif
                    <span class="approval-badge"
                          style="{{ $pet->is_admin_approved
                                ? 'background:rgba(143,175,154,.9); color:#fff;'
                                : 'background:rgba(230,194,122,.9); color:#7A5A1A;' }}">
                        {{ $pet->is_admin_approved ? '✓ Approved' : '⏳ Pending Approval' }}
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
                        @if($pet->gender)
                        <span class="pet-tag">{{ ucfirst($pet->gender) }}</span>
                        @endif
                        @if($pet->adoption_fee)
                        <span class="pet-tag tag-fee">₱{{ number_format($pet->adoption_fee) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Footer ── --}}
                <div class="pet-footer">
                    @php
                        $chipMap = [
                            'available'       => 'chip-available',
                            'adopted'         => 'chip-adopted',
                            'pending'         => 'chip-pending',
                            'under_treatment' => 'chip-treatment',
                        ];
                    @endphp
                    <span class="status-chip {{ $chipMap[$pet->status] ?? 'chip-pending' }}">
                        {{ strtoupper(str_replace('_',' ',$pet->status)) }}
                    </span>

                    <div class="d-flex gap-1">
                        <a href="{{ route('staff.pets.show', $pet) }}" class="action-btn" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('staff.pets.edit', $pet) }}" class="action-btn" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('staff.pets.destroy', $pet) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete {{ addslashes($pet->name) }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button class="action-btn danger" title="Delete" type="submit">
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