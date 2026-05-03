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
    .app-card {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.1rem 1.25rem;
        display: flex; align-items: center; gap: 1rem;
        transition: box-shadow .2s, transform .2s; box-shadow: var(--shadow-sm);
        text-decoration: none; color: inherit;
        margin-bottom: .75rem;
    }
    .app-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); color: inherit; }
    .app-card .ac-pet-img {
        width: 56px; height: 56px; border-radius: 12px;
        object-fit: cover; border: 2px solid var(--border); flex-shrink: 0;
    }
    .app-card .ac-pet-placeholder {
        width: 56px; height: 56px; border-radius: 12px;
        background: var(--coral-light); display: flex; align-items: center;
        justify-content: center; font-size: 1.8rem; flex-shrink: 0;
    }
    .app-card .ac-number { font-size: .78rem; font-weight: 700; color: var(--coral); margin-bottom: .2rem; }
    .app-card .ac-pet-name { font-size: .92rem; font-weight: 700; color: var(--navy); margin-bottom: .15rem; }
    .app-card .ac-meta { font-size: .75rem; color: var(--muted); }
    .app-card .ac-arrow {
        margin-left: auto; width: 32px; height: 32px; border-radius: 9px;
        background: var(--bg); color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem; flex-shrink: 0; transition: all .15s;
    }
    .app-card:hover .ac-arrow { background: var(--coral-light); color: var(--coral); }

    .status-badge {
        font-size: .68rem; font-weight: 700; padding: .3em .8em;
        border-radius: 20px; display: inline-block; text-transform: uppercase; letter-spacing: .04em;
    }

    .filter-bar {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: .85rem 1.25rem;
        margin-bottom: 1.25rem; box-shadow: var(--shadow-sm);
    }
</style>
@endpush

@section('content')

{{-- Filter --}}
<div class="filter-bar">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                @foreach(['pending','submitted','under_review','interview','approved','rejected','completed','withdrawn'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>
                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="{{ route('adopter.applications.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
        <div class="col-auto ms-auto" style="font-size:.8rem; color:var(--muted);">
            {{ $applications->total() }} {{ Str::plural('application', $applications->total()) }}
        </div>
    </form>
</div>

{{-- Application Cards --}}
@forelse($applications as $app)
<a href="{{ route('adopter.applications.show', $app) }}" class="app-card">
    @if($app->pet?->primary_image)
    <img src="{{ $app->pet?->primary_image_url }}" class="ac-pet-img" alt="">
    @else
        <div class="ac-pet-placeholder">🐾</div>
    @endif

    <div class="flex-grow-1 min-w-0">
        <div class="ac-number">{{ $app->application_number }}</div>
        <div class="ac-pet-name">{{ $app->pet?->name ?? 'Deleted Pet' }}</div>
        <div class="ac-meta">
            {{ $app->pet?->category?->name ?? '—' }}
            · Submitted {{ $app->submitted_at?->format('M d, Y') ?? $app->created_at->format('M d, Y') }}
        </div>
    </div>

    <div class="text-end flex-shrink-0">
        @php
            $statusStyles = [
                'approved'   => 'background:var(--sage-light); color:#2D5A3D;',
                'completed'  => 'background:var(--sage-light); color:#2D5A3D;',
                'pending'    => 'background:var(--gold-light); color:#7A5A1A;',
                'submitted'  => 'background:var(--gold-light); color:#7A5A1A;',
                'under_review' => 'background:var(--coral-subtle); color:var(--coral-dark);',
                'interview'  => 'background:var(--coral-subtle); color:var(--coral-dark);',
                'rejected'   => 'background:#FEF0EE; color:#8B2516;',
                'withdrawn'  => 'background:#F3F4F6; color:#6B7280;',
            ];
            $style = $statusStyles[$app->status] ?? 'background:var(--bg); color:var(--muted);';
        @endphp
        <span class="status-badge" style="{{ $style }}">
            {{ ucfirst(str_replace('_', ' ', $app->status)) }}
        </span>
    </div>

    <div class="ac-arrow"><i class="bi bi-chevron-right"></i></div>
</a>
@empty
<div class="card">
    <div class="empty-state py-5">
        <span class="empty-icon">📋</span>
        <h5>No Applications Yet</h5>
        <p>You haven't applied to adopt any pets yet.</p>
        <a href="{{ route('adopter.pets.index') }}" class="btn btn-primary btn-sm mt-2">
            <i class="bi bi-search-heart me-1"></i> Browse Pets
        </a>
    </div>
</div>
@endforelse

@if($applications->hasPages())
<div class="d-flex justify-content-center mt-3">
    {{ $applications->withQueryString()->links() }}
</div>
@endif

@endsection