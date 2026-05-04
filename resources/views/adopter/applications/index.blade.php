@extends('layouts.app')
@section('title', 'My Applications')
@section('page-title', 'My Applications')

@section('breadcrumbs')
    <li class="breadcrumb-item active">My Applications</li>
@endsection

@section('page-actions')
    <a href="{{ route('adopter.pets.index') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Application
    </a>
@endsection

@push('styles')
<style>
    /* ── Filter bar ── */
    .filter-bar {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: .85rem 1.25rem;
        margin-bottom: 1rem; box-shadow: var(--shadow-sm);
        display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
    }

    /* ── App card ── */
    .app-card {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.1rem 1.25rem;
        display: flex; align-items: center; gap: 1rem;
        box-shadow: var(--shadow-sm); text-decoration: none; color: inherit;
        transition: box-shadow .2s, transform .2s, border-color .2s;
        position: relative; overflow: hidden;
    }
    .app-card::before {
        content: ''; position: absolute; left: 0; top: 0; bottom: 0;
        width: 3px; background: var(--coral-light);
        transition: background .2s;
    }
    .app-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); color: inherit; border-color: var(--coral-light); }
    .app-card:hover::before { background: var(--coral); }

    /* ── Pet image ── */
    .ac-pet-img {
        width: 56px; height: 56px; border-radius: 12px;
        object-fit: cover; border: 2px solid var(--border); flex-shrink: 0;
        transition: transform .2s, border-color .2s;
    }
    .app-card:hover .ac-pet-img { transform: scale(1.06); border-color: var(--coral); }
    .ac-pet-placeholder {
        width: 56px; height: 56px; border-radius: 12px;
        background: var(--coral-light); display: flex; align-items: center;
        justify-content: center; font-size: 1.8rem; flex-shrink: 0;
    }

    /* ── Text ── */
    .ac-number   { font-size: .78rem; font-weight: 700; color: var(--coral); margin-bottom: .2rem; }
    .ac-pet-name { font-size: .92rem; font-weight: 700; color: var(--navy); margin-bottom: .15rem; }
    .ac-meta     { font-size: .75rem; color: var(--muted); }

    /* ── Status badge ── */
    .status-chip {
        font-size: .68rem; font-weight: 700; padding: .3em .8em;
        border-radius: 20px; text-transform: uppercase; letter-spacing: .04em;
        white-space: nowrap; flex-shrink: 0;
    }

    /* ── Arrow ── */
    .ac-arrow {
        width: 32px; height: 32px; border-radius: 9px;
        background: var(--bg); color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem; flex-shrink: 0; transition: all .15s;
    }
    .app-card:hover .ac-arrow { background: var(--coral-light); color: var(--coral); }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideInCard {
        from { opacity: 0; transform: translateX(-20px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    .filter-bar { animation: fadeDown .4s ease both; }

    .app-card { opacity: 0; }
    .app-card.visible { animation: slideInCard .42s ease both; }
</style>
@endpush

@section('content')

{{-- ── Filter Bar ── --}}
<div class="filter-bar">
    <form method="GET" class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">
        <select name="status" class="form-select form-select-sm" style="max-width:200px;">
            <option value="">All Statuses</option>
            @foreach(['pending','submitted','under_review','interview','approved','rejected','completed','withdrawn','returned'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>
                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                </option>
            @endforeach
        </select>
        <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
        <a href="{{ route('adopter.applications.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    </form>
    <span style="font-size:.78rem; color:var(--muted); flex-shrink:0;">
        {{ $applications->total() }} {{ Str::plural('application', $applications->total()) }}
    </span>
</div>

{{-- ── Application Cards ── --}}
<div class="d-flex flex-column gap-2" id="appList">
    @forelse($applications as $i => $app)

    @php
        $statusColors = [
            'pending'      => 'background:var(--gold-light); color:#7A5A1A;',
            'submitted'    => 'background:var(--gold-light); color:#7A5A1A;',
            'under_review' => 'background:var(--coral-subtle); color:var(--coral-dark);',
            'interview'    => 'background:rgba(45,49,71,.08); color:var(--navy);',
            'approved'     => 'background:var(--sage-light); color:#2D5A3D;',
            'completed'    => 'background:var(--sage-light); color:#2D5A3D;',
            'rejected'     => 'background:#FEF0EE; color:#8B2516;',
            'withdrawn'    => 'background:#F3F4F6; color:#6B7280;',
            'returned'     => 'background:#F3F4F6; color:#6B7280;',
        ];
        $sc = $statusColors[$app->status] ?? 'background:var(--bg); color:var(--muted);';
    @endphp

    <a href="{{ route('adopter.applications.show', $app) }}" class="app-card" data-index="{{ $i }}">

        {{-- Pet image ── --}}
        @if($app->pet?->primary_image)
            <img src="{{ $app->pet->primary_image_url }}" class="ac-pet-img" alt="">
        @else
            <div class="ac-pet-placeholder">🐾</div>
        @endif

        {{-- Info ── --}}
        <div class="flex-grow-1 min-w-0">
            <div class="ac-number">{{ $app->application_number }}</div>
            <div class="ac-pet-name">{{ $app->pet?->name ?? 'Deleted Pet' }}</div>
            <div class="ac-meta">
                {{ $app->pet?->petCategory?->name ?? '—' }}
                &middot; Submitted {{ $app->created_at->format('M d, Y') }}
            </div>
        </div>

        {{-- Status ── --}}
        <span class="status-chip" style="{{ $sc }}">
            {{ ucfirst(str_replace('_', ' ', $app->status)) }}
        </span>

        {{-- Arrow ── --}}
        <div class="ac-arrow">
            <i class="bi bi-chevron-right"></i>
        </div>

    </a>

    @empty
    <div class="card">
        <div class="empty-state py-5">
            <span class="empty-icon">📋</span>
            <h5>No Applications Yet</h5>
            <p>Browse pets and submit your first adoption application!</p>
            <a href="{{ route('adopter.pets.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-search-heart me-1"></i> Browse Pets
            </a>
        </div>
    </div>
    @endforelse
</div>

@if($applications->hasPages())
<div class="d-flex justify-content-center mt-3">
    {{ $applications->withQueryString()->links() }}
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.app-card').forEach(card => {
        const delay = 200 + (parseInt(card.dataset.index) * 90);
        setTimeout(() => card.classList.add('visible'), delay);
    });
});
</script>
@endpush