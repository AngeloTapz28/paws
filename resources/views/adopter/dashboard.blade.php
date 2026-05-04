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
        position: relative; overflow: hidden;
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
    .hero-banner .hero-greeting { font-size: 1.55rem; font-weight: 800; color: var(--navy); margin: 0; line-height: 1.2; }
    .hero-banner .hero-sub      { font-size: .875rem; color: #8A7060; margin: .35rem 0 1rem; line-height: 1.5; }
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
        box-shadow: var(--shadow-sm);
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
        transform: scale(0.9); opacity: 0;
        transition: transform .3s, opacity .3s;
    }
    .journey-step.revealed { transform: scale(1); opacity: 1; }
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
        font-size: .7rem; color: var(--border); flex-shrink: 0; width: 20px;
        transition: color .4s;
    }
    .journey-arrow.lit { color: var(--coral-light); }

    /* ── Activity List ── */
    .activity-item {
        display: flex; align-items: center; gap: .85rem;
        padding: .85rem 1.25rem; border-bottom: 1px solid var(--border);
        transition: background .15s;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: var(--coral-subtle); }
    .act-thumb {
        width: 42px; height: 42px; border-radius: 10px;
        object-fit: cover; border: 2px solid var(--border); flex-shrink: 0;
        transition: transform .2s, border-color .2s;
    }
    .activity-item:hover .act-thumb { transform: scale(1.08); border-color: var(--coral); }
    .act-thumb-ph {
        width: 42px; height: 42px; border-radius: 10px;
        background: var(--coral-light); display: flex; align-items: center;
        justify-content: center; font-size: 1.3rem; flex-shrink: 0;
    }
    .act-desc { font-size: .845rem; color: var(--text); line-height: 1.4; }
    .act-desc strong { color: var(--navy); font-weight: 700; }
    .act-time { font-size: .72rem; color: var(--muted); margin-top: .12rem; }
    .act-badge { font-size: .67rem; font-weight: 700; padding: .3em .8em; border-radius: 20px; white-space: nowrap; flex-shrink: 0; }
    .sec-hdr {
        display: flex; align-items: center; justify-content: space-between;
        padding: .85rem 1.25rem; border-bottom: 1px solid var(--border);
    }
    .sec-hdr h6 { font-size: .9rem; font-weight: 700; color: var(--navy); margin: 0; }
    .sec-hdr a  { font-size: .78rem; color: var(--coral); text-decoration: none; display: flex; align-items: center; gap: .3rem; }
    .sec-hdr a:hover { text-decoration: underline; }

    /* ── Pet Grid Cards ── */
    .pet-grid-wrap { padding: .9rem; }
    .pet-grid-card {
        border-radius: 14px; overflow: hidden; border: 1px solid var(--border);
        transition: transform .22s, box-shadow .22s;
    }
    .pet-grid-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
    .pgc-img { width: 100%; height: 130px; object-fit: cover; transition: transform .35s; }
    .pet-grid-card:hover .pgc-img { transform: scale(1.06); }
    .pgc-img-ph {
        height: 130px; background: var(--coral-light);
        display: flex; align-items: center; justify-content: center; font-size: 2.5rem;
    }
    .pgc-body { padding: .65rem .8rem .8rem; background: var(--white); }
    .pgc-name { font-size: .85rem; font-weight: 700; color: var(--navy); }
    .pgc-meta { font-size: .72rem; color: var(--muted); margin-bottom: .5rem; }
    .btn-view-profile {
        display: block; text-align: center; background: var(--coral);
        color: #fff; border-radius: 20px; padding: .35rem .75rem;
        font-size: .72rem; font-weight: 600; text-decoration: none;
        transition: background .15s, transform .15s;
    }
    .btn-view-profile:hover { background: var(--coral-dark); transform: translateY(-1px); color: #fff; }

    /* ── Stats summary ── */
    .stat-mini-val { font-size: 1.5rem; font-weight: 800; line-height: 1; }
    .stat-mini-lbl { font-size: .63rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; margin-top: .2rem; }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes heroBannerIn {
        from { opacity: 0; transform: translateY(-20px) scale(.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(20px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes cardPop {
        0%   { opacity: 0; transform: translateY(18px) scale(.97); }
        60%  { transform: translateY(-3px) scale(1.01); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes avatarPop {
        0%   { transform: scale(0); }
        70%  { transform: scale(1.08); }
        100% { transform: scale(1); }
    }

    /* Hero */
    .hero-banner { animation: heroBannerIn .55s cubic-bezier(.25,.46,.45,.94) both; }
    .hero-banner .hero-avatar,
    .hero-banner .hero-avatar-fallback {
    animation: avatarPop .5s cubic-bezier(.34,1.56,.64,1) .3s forwards;
}
@keyframes avatarPop {
    0%   { opacity: 0; transform: scale(0); }
    70%  { opacity: 1; transform: scale(1.08); }
    100% { opacity: 1; transform: scale(1); }
}
    .hero-greeting { opacity: 0; animation: fadeUp .4s ease .35s both; }
    .hero-sub      { opacity: 0; animation: fadeUp .4s ease .45s both; }
    .hero-cta      { opacity: 0; animation: fadeUp .4s ease .55s both; }

    /* Left column */
    .col-left  { opacity: 0; animation: slideInLeft  .45s ease .2s both; }
    .col-right { opacity: 0; animation: slideInRight .45s ease .3s both; }

    /* Journey card inside left col */
    .journey-card { opacity: 0; animation: fadeUp .4s ease .4s both; }

    /* Activity card */
    .activity-card { opacity: 0; animation: fadeUp .4s ease .55s both; }

    /* Activity items — JS staggers */
    .activity-item { opacity: 0; }
    .activity-item.visible { animation: slideInLeft .38s ease both; }

    /* Pet cards — JS staggers */
    .pet-col { opacity: 0; }
    .pet-col.visible { animation: cardPop .42s cubic-bezier(.25,.46,.45,.94) both; }

    /* Stats card */
    .stats-card { opacity: 0; animation: fadeUp .4s ease .5s both; }

    /* Stat values count up via JS */
</style>
@endpush

@section('content')

@php
    $nameParts  = explode(' ', auth()->user()->name);
    $honorifics = ['system', 'dr.', 'dr', 'mr.', 'mr', 'ms.', 'ms', 'mrs.', 'mrs', 'prof.', 'prof'];
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
                    You've given <strong>{{ $stats['completed'] }} {{ Str::plural('pet', $stats['completed']) }} a forever home!</strong> 🎉
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
    <div class="col-lg-7 d-flex flex-column gap-3 col-left">

        {{-- Adoption Journey --}}
        <div class="journey-card">
            <div class="jc-title">
                <i class="bi bi-map" style="color:var(--coral);"></i> Your Adoption Journey
            </div>
            <div class="journey-steps" id="journeySteps">
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
                    @php $state = $i < $journeyStep ? 'done' : ($i === $journeyStep ? 'j-active' : 'inactive'); @endphp
                    <div class="journey-step {{ $state }}" data-step="{{ $i }}">
                        <div class="js-icon"><i class="bi {{ $step['icon'] }}"></i></div>
                        <div class="js-label">{{ $step['label'] }}</div>
                        <div class="js-sub">{{ $step['sub'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="card activity-card">
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
                @foreach($applications as $i => $app)
                <div class="activity-item" data-index="{{ $i }}">
                    @if($app->pet?->primary_image)
                        <img src="{{ $app->pet?->primary_image_url }}" class="act-thumb" alt="">
                    @else
                        <div class="act-thumb-ph">🐾</div>
                    @endif
                    <div class="flex-grow-1 min-w-0">
                        <div class="act-desc">
                            Application <strong>{{ $app->application_number }}</strong> ·
                            @if(in_array($app->status, ['pending','submitted']))
                                Submitted for <strong>{{ $app->pet?->name ?? 'Unknown Pet' }}</strong>
                            @elseif(in_array($app->status, ['reviewing','under_review']))
                                <strong>{{ $app->pet?->name ?? 'Pet' }}</strong> under review
                            @elseif($app->status === 'approved')
                                <strong>{{ $app->pet?->name ?? 'Pet' }}</strong> approved! 🎉
                            @elseif($app->status === 'completed')
                                Adopted <strong>{{ $app->pet?->name ?? 'Pet' }}</strong> successfully! 🏠
                            @elseif($app->status === 'rejected')
                                Application for <strong>{{ $app->pet?->name ?? 'Pet' }}</strong> was not approved
                            @else
                                {{ ucfirst(str_replace('_',' ',$app->status)) }}
                            @endif
                        </div>
                        <div class="act-time">{{ $app->created_at->diffForHumans() }}</div>
                    </div>
                    @php
    $statusStyle = match($app->status) {
        'pending', 'submitted'              => 'background:var(--gold-light); color:#7A5A1A;',
        'reviewing', 'under_review'         => 'background:var(--coral-subtle); color:var(--coral-dark);',
        'interview'                         => 'background:rgba(45,49,71,.08); color:var(--navy);',
        'approved'                          => 'background:var(--sage-light); color:#2D5A3D;',
        'completed'                         => 'background:var(--sage-light); color:#2D5A3D;',
        'rejected'                          => 'background:#FEF0EE; color:#8B2516;',
        'withdrawn', 'cancelled','returned' => 'background:#F3F4F6; color:#6B7280;',
        default                             => 'background:var(--bg); color:var(--muted);',
    };
@endphp
<span class="act-badge" style="{{ $statusStyle }}">{{ $app->status_label }}</span>
                </div>
                @endforeach
            @endif
        </div>

    </div>

    {{-- ── RIGHT COLUMN ── --}}
    <div class="col-lg-5 d-flex flex-column gap-3 col-right">

        {{-- Pets Looking for a Home --}}
        @if($featuredPets->count())
        <div class="card">
            <div class="sec-hdr">
                <h6><i class="bi bi-heart-fill me-2" style="color:var(--coral);"></i>Pets Looking for a Home</h6>
                <a href="{{ route('adopter.pets.index') }}">Browse All <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="pet-grid-wrap">
                <div class="row g-2" id="petGrid">
                    @foreach($featuredPets->take(6) as $i => $pet)
                    <div class="col-6 pet-col" data-index="{{ $i }}">
                        <div class="pet-grid-card">
                            @if($pet->primary_image)
                                <img src="{{ $pet->primary_image_url }}" class="pgc-img" alt="{{ $pet->name }}">
                            @else
                                <div class="pgc-img-ph">
                                    {{ ($pet->category->name ?? '') === 'Dog' ? '🐶' : (($pet->category->name ?? '') === 'Cat' ? '🐱' : '🐾') }}
                                </div>
                            @endif
                            <div class="pgc-body">
                                <div class="pgc-name">{{ $pet->name }}</div>
                                <div class="pgc-meta">{{ $pet->category->name ?? '—' }} · {{ $pet->age_label ?? '' }}</div>
                                <a href="{{ route('adopter.pets.show', $pet) }}" class="btn-view-profile">
                                    View Profile
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- My Adoption Stats --}}
        <div class="card stats-card" style="border-top: 3px solid var(--coral);">
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
                        <div class="stat-mini-val" style="color:{{ $s['color'] }};" data-count="{{ $s['val'] }}">0</div>
                        <div class="stat-mini-lbl">{{ $s['lbl'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── 1. Journey steps pop in with stagger ──
    document.querySelectorAll('.journey-step').forEach(step => {
        const i     = parseInt(step.dataset.step);
        const delay = 600 + (i * 120);
        setTimeout(() => step.classList.add('revealed'), delay);
    });

    // ── 2. Activity items slide in ──
    document.querySelectorAll('.activity-item').forEach(item => {
        const delay = 700 + (parseInt(item.dataset.index) * 90);
        setTimeout(() => item.classList.add('visible'), delay);
    });

    // ── 3. Pet cards pop in row-aware ──
    document.querySelectorAll('.pet-col').forEach(col => {
        const i     = parseInt(col.dataset.index);
        const row   = Math.floor(i / 2);
        const col_i = i % 2;
        const delay = 500 + (row * 120) + (col_i * 60);
        setTimeout(() => col.classList.add('visible'), delay);
    });

    // ── 4. Stats count-up ──
    function countUp(el) {
        const target = parseInt(el.dataset.count);
        if (isNaN(target) || target === 0) { el.textContent = '0'; return; }
        const dur = 700, step = 16, inc = target / (dur / step);
        let cur = 0;
        const t = setInterval(() => {
            cur += inc;
            if (cur >= target) { clearInterval(t); el.textContent = target; }
            else el.textContent = Math.floor(cur);
        }, step);
    }

    setTimeout(() => {
        document.querySelectorAll('[data-count]').forEach(countUp);
    }, 550);

});
</script>
@endpush