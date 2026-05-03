@extends('layouts.app')
@section('title', 'My Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* ── Hero Banner ── */
    .hero-banner {
        background: linear-gradient(135deg, #FBF0E8 0%, #F5E6D8 100%);
        border-radius: 20px;
        padding: 1.75rem 2rem 0;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        min-height: 175px;
        border: 1px solid #EDD8C8;
    }
    .hero-banner .hero-avatar {
        width: 64px; height: 64px; border-radius: 50%;
        object-fit: cover; border: 3px solid var(--white);
        box-shadow: 0 4px 16px rgba(0,0,0,.12); flex-shrink: 0;
    }
    .hero-banner .hero-avatar-fallback {
        width: 64px; height: 64px; border-radius: 50%;
        background: var(--coral); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; font-weight: 700; flex-shrink: 0;
        box-shadow: 0 4px 16px rgba(217,119,87,.3);
    }
    .hero-banner .hero-greeting {
        font-size: 1.55rem; font-weight: 800; color: var(--navy); margin: 0; line-height: 1.2;
    }
    .hero-banner .hero-sub {
        font-size: .875rem; color: #8A7060; margin: .35rem 0 1rem; line-height: 1.5;
    }
    .hero-banner .hero-sub strong { color: var(--navy); font-weight: 700; }
    .hero-banner .hero-cta {
        display: inline-flex; align-items: center; gap: .5rem;
        background: var(--coral); color: #fff; border: none;
        border-radius: 25px; padding: .6rem 1.4rem;
        font-size: .875rem; font-weight: 600; text-decoration: none;
        transition: background .2s, transform .15s; margin-bottom: 1.5rem;
    }
    .hero-banner .hero-cta:hover { background: var(--coral-dark); color: #fff; transform: translateY(-1px); }
    .hero-banner .hero-dog-bg {
        position: absolute; right: 1.5rem; bottom: 0;
        font-size: 7.5rem; line-height: 1; opacity: .15; pointer-events: none;
    }
    .hero-banner .paw-bg {
        position: absolute; right: 2rem; top: 1rem;
        font-size: 4rem; opacity: .06; pointer-events: none;
        transform: rotate(15deg);
    }

    /* ── Journey Steps ── */
    .journey-card {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius); padding: 1.1rem 1.25rem;
        margin-bottom: 1.25rem; box-shadow: var(--shadow-sm);
    }
    .journey-card .jc-title {
        font-size: .78rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: var(--muted); margin-bottom: .9rem;
        display: flex; align-items: center; gap: .4rem;
    }
    .journey-steps { display: flex; align-items: center; gap: 0; }
    .journey-step {
        flex: 1; background: var(--bg); border: 1.5px solid var(--border);
        border-radius: 12px; padding: .7rem .6rem .6rem;
        text-align: center; position: relative;
    }
    .journey-step.done     { background: var(--coral-subtle); border-color: var(--coral-light); }
    .journey-step.j-active { background: var(--coral); border-color: var(--coral); box-shadow: 0 4px 14px rgba(217,119,87,.35); }
    .js-icon {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto .4rem; font-size: .9rem;
    }
    .journey-step.done     .js-icon { background: var(--coral-light); color: var(--coral); }
    .journey-step.j-active .js-icon { background: rgba(255,255,255,.22); color: #fff; }
    .journey-step.inactive .js-icon { background: var(--border); color: var(--muted); }
    .js-label { font-size: .72rem; font-weight: 600; line-height: 1.3; }
    .journey-step.done     .js-label { color: var(--coral-dark); }
    .journey-step.j-active .js-label { color: #fff; }
    .journey-step.inactive .js-label { color: var(--muted); }
    .js-sub { font-size: .63rem; margin-top: .15rem; }
    .journey-step.done     .js-sub { color: var(--coral); }
    .journey-step.j-active .js-sub { color: rgba(255,255,255,.75); }
    .journey-step.inactive .js-sub { color: transparent; }
    .journey-arrow {
        display: flex; align-items: center; justify-content: center;
        font-size: .7rem; color: var(--border); flex-shrink: 0;
        width: 20px;
    }
    .journey-arrow.lit { color: var(--coral-light); }

    /* ── Activity List ── */
    .activity-item {
        display: flex; align-items: center; gap: .85rem;
        padding: .85rem 1.25rem; border-bottom: 1px solid var(--border);
        transition: background .15s; cursor: default;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: var(--coral-subtle); }
    .act-thumb {
        width: 42px; height: 42px; border-radius: 10px;
        object-fit: cover; border: 2px solid var(--border); flex-shrink: 0;
    }
    .act-thumb-ph {
        width: 42px; height: 42px; border-radius: 10px;
        background: var(--coral-light); display: flex; align-items: center;
        justify-content: center; font-size: 1.3rem; flex-shrink: 0;
    }
    .act-desc { font-size: .845rem; color: var(--text); line-height: 1.4; }
    .act-desc strong { color: var(--navy); font-weight: 700; }
    .act-time { font-size: .72rem; color: var(--muted); margin-top: .12rem; }
    .act-badge {
        font-size: .67rem; font-weight: 700; padding: .3em .8em;
        border-radius: 20px; white-space: nowrap; flex-shrink: 0;
    }

    /* ── Pet grid cards ── */
    .pet-grid-wrap { padding: .9rem; }
    .pet-grid-card {
        border-radius: 14px; overflow: hidden; border: 1px solid var(--border);
        background: var(--white); transition: transform .2s, box-shadow .2s;
        box-shadow: var(--shadow-sm);
    }
    .pet-grid-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
    .pet-grid-card .pgc-img { width: 100%; height: 120px; object-fit: cover; }
    .pet-grid-card .pgc-ph {
        width: 100%; height: 120px; background: var(--coral-light);
        display: flex; align-items: center; justify-content: center; font-size: 3rem;
    }
    .pet-grid-card .pgc-body { padding: .65rem .75rem .75rem; }
    .pet-grid-card .pgc-name { font-size: .875rem; font-weight: 700; color: var(--navy); margin-bottom: .15rem; }
    .pet-grid-card .pgc-meta { font-size: .73rem; color: var(--muted); margin-bottom: .65rem; }
    .btn-view-profile {
        display: block; width: 100%; text-align: center;
        background: var(--coral); color: #fff; border: none;
        border-radius: 20px; padding: .38rem .9rem;
        font-size: .78rem; font-weight: 600; text-decoration: none;
        transition: background .15s;
    }
    .btn-view-profile:hover { background: var(--coral-dark); color: #fff; }

    /* ── Section header ── */
    .sec-hdr {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1rem 1.25rem; border-bottom: 1px solid var(--border);
    }
    .sec-hdr h6 { font-size: .92rem; font-weight: 700; color: var(--navy); margin: 0; }
    .sec-hdr a  { font-size: .78rem; color: var(--coral); text-decoration: none; display: flex; align-items: center; gap: .25rem; }
    .sec-hdr a:hover { text-decoration: underline; }
</style>
@endpush

@section('content')

@php
    $nameParts  = explode(' ', auth()->user()->name);
    $honorifics = ['system','dr.','dr','mr.','mr','ms.','ms','mrs.','mrs','prof.','prof'];
    $firstName  = collect($nameParts)->first(fn($p) => !in_array(strtolower($p), $honorifics)) ?? last($nameParts);

    $latestApp    = $applications->first();
    $latestStatus = $latestApp?->status ?? null;
    $journeyStep  = 0;
    if ($latestStatus) {
        if (in_array($latestStatus, ['pending','submitted']))                  $journeyStep = 1;
        if (in_array($latestStatus, ['reviewing','under_review','interview'])) $journeyStep = 2;
        if (in_array($latestStatus, ['approved','completed']))                 $journeyStep = 3;
    }
@endphp

{{-- ── HERO BANNER ── --}}
<div class="hero-banner">
    <span class="paw-bg">🐾</span>
    <div class="d-flex align-items-center gap-3 mb-1">
        @if(auth()->user()->avatar)
            <img src="{{ auth()->user()->avatar_url }}" class="hero-avatar" alt="">
        @else
            <div class="hero-avatar-fallback">{{ strtoupper(substr($firstName, 0, 1)) }}</div>
        @endif
        <div>
            <h2 class="hero-greeting">Welcome back, {{ $firstName }}!</h2>
            <p class="hero-sub mb-0">
                @if($stats['pending'] > 0)
                    You're <strong>{{ $stats['pending'] }} {{ Str::plural('application', $stats['pending']) }}</strong> away from giving a <strong>pet a home</strong> 🧡
                @elseif($stats['completed'] > 0)
                    You've given <strong>{{ $stats['completed'] }} {{ Str::plural('pet', $stats['completed']) }} a forever home!</strong> 
                @else
                    Start your journey — find your <strong>perfect companion</strong> today 🐾
                @endif
            </p>
        </div>
    </div>
    <a href="{{ $stats['pending'] > 0 ? route('adopter.applications.index') : route('adopter.pets.index') }}"
       class="hero-cta">
        <i class="bi bi-{{ $stats['pending'] > 0 ? 'send' : 'search-heart' }}"></i>
        {{ $stats['pending'] > 0 ? 'Continue Application' : 'Browse Pets' }}
    </a>
    <div class="hero-dog-bg">🐕</div>
</div>

{{-- ── MAIN LAYOUT ── --}}
<div class="row g-3">

    {{-- ── LEFT COLUMN ── --}}
    <div class="col-lg-7 d-flex flex-column gap-3">

        {{-- Adoption Journey --}}
        <div class="journey-card">
            <div class="jc-title">
                <i class="bi bi-map" style="color:var(--coral);"></i> Your Adoption Journey
            </div>
            <div class="journey-steps">
                @php
                    $steps = [
                        ['icon' => 'bi-search-heart',       'label' => 'Browse Pets',    'sub' => $featuredPets->count() . ' pets available'],
                        ['icon' => 'bi-send-fill',          'label' => 'Application',    'sub' => 'Submitted'],
                        ['icon' => 'bi-eye-fill',           'label' => 'Under Review',   'sub' => 'Being reviewed'],
                        ['icon' => 'bi-check-circle-fill',  'label' => 'Approved',       'sub' => 'Almost there!'],
                    ];
                @endphp
                @foreach($steps as $i => $step)
                    @if($i > 0)
                        <div class="journey-arrow {{ $i <= $journeyStep ? 'lit' : '' }}">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    @endif
                    @php
                        $state = $i < $journeyStep ? 'done' : ($i === $journeyStep ? 'j-active' : 'inactive');
                    @endphp
                    <div class="journey-step {{ $state }}">
                        <div class="js-icon"><i class="bi {{ $step['icon'] }}"></i></div>
                        <div class="js-label">{{ $step['label'] }}</div>
                        <div class="js-sub">{{ $step['sub'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="card">
            <div class="sec-hdr">
                <h6><i class="bi bi-clock-history me-2" style="color:var(--coral);"></i>Recent Activity</h6>
                <a href="{{ route('adopter.applications.index') }}">View All <i class="bi bi-arrow-right"></i></a>
            </div>
            @if($applications->isEmpty())
                <div class="empty-state py-4">
                    <span class="empty-icon">📋</span>
                    <h5>No Activity Yet</h5>
                    <p>Browse pets and submit your first adoption application!</p>
                    <a href="{{ route('adopter.pets.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-search-heart me-1"></i> Browse Pets
                    </a>
                </div>
            @else
                @foreach($applications as $app)
                <div class="activity-item">
                    @if($app->pet?->primary_image)
                        <img src="{{ $app->pet?->primary_image_url }}" class="act-thumb" alt="">
                    @else
                        <div class="act-thumb-ph">🐾</div>
                    @endif
                    <div class="flex-grow-1 min-w-0">
                        <div class="act-desc">
                            @if(in_array($app->status, ['pending','submitted']))
                                Submitted an application for <strong>{{ $app->pet?->name ?? 'Unknown Pet' }}</strong>
                            @elseif(in_array($app->status, ['reviewing','under_review']))
                                <strong>{{ $app->pet?->name ?? 'Unknown Pet' }}</strong> application is currently under review
                            @elseif($app->status === 'interview')
                                Interview scheduled for <strong>{{ $app->pet?->name ?? 'Unknown Pet' }}</strong>
                            @elseif($app->status === 'approved')
                                Application for <strong>{{ $app->pet?->name ?? 'Unknown Pet' }}</strong> has been approved! 
                            @elseif($app->status === 'completed')
                                You completed an adoption for <strong>{{ $app->pet?->name ?? 'Deleted Pet' }}</strong> 
                            @elseif($app->status === 'rejected')
                                Application for <strong>{{ $app->pet?->name ?? 'Unknown Pet' }}</strong> was not approved
                            @else
                                Application <strong>{{ $app->application_number }}</strong> · {{ ucfirst($app->status) }}
                            @endif
                        </div>
                        <div class="act-time">
                            {{ $app->submitted_at?->diffForHumans() ?? $app->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @php
                        $badgeStyles = [
                            'pending'      => 'background:var(--gold-light);   color:#7A5A1A;',
                            'submitted'    => 'background:var(--gold-light);   color:#7A5A1A;',
                            'under_review' => 'background:var(--coral-subtle); color:var(--coral-dark);',
                            'reviewing'    => 'background:var(--coral-subtle); color:var(--coral-dark);',
                            'interview'    => 'background:var(--coral-subtle); color:var(--coral-dark);',
                            'approved'     => 'background:var(--sage-light);   color:#2D5A3D;',
                            'completed'    => 'background:var(--sage-light);   color:#2D5A3D;',
                            'rejected'     => 'background:#FEF0EE;             color:#8B2516;',
                            'withdrawn'    => 'background:#F3F4F6;             color:#6B7280;',
                        ];
                        $bs = $badgeStyles[$app->status] ?? 'background:var(--bg);color:var(--muted);';
                    @endphp
                    <span class="act-badge" style="{{ $bs }}">
                        {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                    </span>
                </div>
                @endforeach
            @endif
        </div>

    </div>

    {{-- ── RIGHT COLUMN ── --}}
    <div class="col-lg-5 d-flex flex-column gap-3">

        {{-- Pets Looking for a Home --}}
        <div class="card">
            <div class="sec-hdr">
                <h6><i class="bi bi-heart-fill me-2" style="color:var(--coral);"></i>Pets Looking for a Home</h6>
                <a href="{{ route('adopter.pets.index') }}">Browse All <i class="bi bi-arrow-right"></i></a>
            </div>
            @if($featuredPets->isEmpty())
                <div class="empty-state py-4">
                    <span class="empty-icon">🐾</span>
                    <h5>No Pets Available</h5>
                    <p>Check back soon for new listings!</p>
                </div>
            @else
                <div class="pet-grid-wrap">
                    <div class="row g-2">
                        @foreach($featuredPets->take(4) as $pet)
                        <div class="col-6">
                            <div class="pet-grid-card">
                                @if($pet->primary_image)
                                    <img src="{{ $pet->primary_image_url }}" class="pgc-img" alt="{{ $pet->name }}">
                                @else
                                    <div class="pgc-ph">
                                        {{ ($pet->category->name ?? '') === 'Dog' ? '🐶' : (($pet->category->name ?? '') === 'Cat' ? '🐱' : '🐾') }}
                                    </div>
                                @endif
                                <div class="pgc-body">
                                    <div class="pgc-name">{{ $pet->name }}</div>
                                    <div class="pgc-meta">
                                        {{ $pet->category->name ?? '—' }} · {{ $pet->age_label ?? '' }}
                                    </div>
                                    <a href="{{ route('adopter.pets.show', $pet) }}" class="btn-view-profile">
                                        View Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Stats summary --}}
        <div class="card" style="border-top: 3px solid var(--coral);">
            <div class="card-header" style="padding:.85rem 1.25rem;">
                <h6 class="mb-0 fw-bold" style="color:var(--navy); font-size:.88rem;">
                    <i class="bi bi-graph-up me-2" style="color:var(--coral);"></i>My Adoption Stats
                </h6>
            </div>
            <div class="card-body" style="padding:1rem 1.25rem;">
                <div class="row g-2 text-center">
                    @foreach([
                        ['val' => $stats['total_applications'], 'lbl' => 'Total',     'color' => 'var(--coral)'],
                        ['val' => $stats['pending'],            'lbl' => 'Pending',   'color' => '#B8892A'],
                        ['val' => $stats['approved'],           'lbl' => 'Approved',  'color' => 'var(--sage)'],
                        ['val' => $stats['completed'],          'lbl' => 'Completed', 'color' => 'var(--navy)'],
                    ] as $s)
                    <div class="col-3">
                        <div style="font-size:1.5rem; font-weight:800; color:{{ $s['color'] }}; line-height:1;">{{ $s['val'] }}</div>
                        <div style="font-size:.63rem; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; margin-top:.2rem;">{{ $s['lbl'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

@endsection