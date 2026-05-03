<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — PAWS System</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🐾</text></svg>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ════════════════════════════════
           DESIGN TOKENS
        ════════════════════════════════ */
        :root {
            --coral:        #D97757;
            --coral-dark:   #C4654A;
            --coral-light:  #F2E8E3;
            --coral-subtle: #FBF4F0;
            --sage:         #8FAF9A;
            --sage-light:   #EAF0EC;
            --gold:         #E6C27A;
            --gold-light:   #FBF3E2;
            --navy:         #2D3147;
            --navy-mid:     #3A3F58;
            --navy-light:   #4A5070;
            --bg:           #F7F5F2;
            --white:        #FFFFFF;
            --border:       #EDE8E3;
            --text:         #2A2A2A;
            --muted:        #9A9589;
            --sidebar-w:    272px;
            --topbar-h:     64px;
            --radius:       14px;
            --radius-sm:    9px;
            --shadow-sm:    0 1px 4px rgba(0,0,0,.05);
            --shadow-md:    0 4px 16px rgba(0,0,0,.08);
            --shadow-lg:    0 8px 32px rgba(0,0,0,.12);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: .9rem;
        }

        /* ════════════════════════════════
           SIDEBAR  — light card style
        ════════════════════════════════ */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--bg);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .3s ease;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.1rem .9rem 1rem;
            gap: .35rem;
            border-right: 1px solid var(--border);
        }

        /* ── Brand row ── */
        #sidebar .sidebar-brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .2rem .3rem .7rem;
            text-decoration: none;
            flex-shrink: 0;
        }

        #sidebar .sidebar-brand .brand-icon {
            width: 38px; height: 38px;
            background: var(--coral);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(217,119,87,.4);
        }

        #sidebar .brand-text .name {
            font-weight: 800;
            font-size: .95rem;
            color: var(--navy);
            line-height: 1.15;
            display: block;
            letter-spacing: .01em;
        }

        #sidebar .brand-text .tagline {
            font-size: .61rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .09em;
            font-weight: 600;
        }

        /* ── Hero promo card ── */
        #sidebar .sidebar-hero {
            border-radius: 18px;
            background: linear-gradient(135deg, #E08060 0%, #C9623E 55%, #A84F30 100%);
            padding: 1.15rem 1.1rem 1rem;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
            min-height: 118px;
            margin-bottom: .15rem;
        }

        /* Soft light circle decoration */
        #sidebar .sidebar-hero::before {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 100px; height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,.1);
            pointer-events: none;
        }

        #sidebar .sidebar-hero::after {
            content: '';
            position: absolute;
            bottom: -20px; right: 40px;
            width: 70px; height: 70px;
            border-radius: 50%;
            background: rgba(255,255,255,.07);
            pointer-events: none;
        }

        #sidebar .sidebar-hero .hero-emoji {
            position: absolute;
            right: 12px; bottom: 8px;
            font-size: 4.8rem;
            line-height: 1;
            opacity: .2;
            pointer-events: none;
            z-index: 0;
        }

        #sidebar .sidebar-hero h6 {
            color: #fff;
            font-size: .88rem;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: .8rem;
            max-width: 155px;
            position: relative;
            z-index: 1;
        }

        #sidebar .sidebar-hero .hero-btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(255,255,255,.2);
            color: #fff;
            border: 1.5px solid rgba(255,255,255,.3);
            border-radius: 20px;
            padding: .38rem 1rem;
            font-size: .76rem;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s, transform .15s;
            position: relative;
            z-index: 1;
        }

        #sidebar .sidebar-hero .hero-btn:hover {
            background: rgba(255,255,255,.32);
            color: #fff;
            transform: translateY(-1px);
        }

        /* ── Nav section label ── */
        #sidebar .nav-section-label {
            font-size: .61rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .13em;
            color: var(--muted);
            padding: .7rem .6rem .2rem;
        }

        /* ── Nav links ── */
        #sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .55rem .7rem;
            color: var(--navy-mid);
            font-size: .855rem;
            font-weight: 500;
            border-radius: 11px;
            text-decoration: none;
            transition: all .15s ease;
            position: relative;
        }

        #sidebar .nav-link:hover {
            background: var(--white);
            color: var(--coral);
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }

        #sidebar .nav-link.active {
            background: var(--white);
            color: var(--coral);
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
        }

        /* Icon box */
        #sidebar .nav-link .nav-icon {
            width: 28px; height: 28px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem;
            flex-shrink: 0;
            background: transparent;
            color: var(--muted);
            transition: all .15s;
        }

        #sidebar .nav-link:hover .nav-icon,
        #sidebar .nav-link.active .nav-icon {
            background: var(--coral-light);
            color: var(--coral);
        }

        /* Chevron on the right via pseudo-element */
        #sidebar .nav-link .nav-chevron {
            margin-left: auto;
            font-size: .7rem;
            color: var(--border);
            transition: color .15s, transform .15s;
            flex-shrink: 0;
        }

        #sidebar .nav-link:hover .nav-chevron,
        #sidebar .nav-link.active .nav-chevron {
            color: var(--coral);
            transform: translateX(2px);
        }

        /* Badge pill (for notifications count) */
        #sidebar .nav-badge {
            font-size: .62rem;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 .4rem;
            background: var(--coral);
            color: #fff;
            margin-left: auto;
        }

        /* ── Sidebar footer (user card) ── */
        #sidebar .sidebar-footer {
            margin-top: auto;
            padding-top: .6rem;
            flex-shrink: 0;
        }

        #sidebar .user-card {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .7rem .8rem;
            border-radius: 13px;
            background: var(--white);
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            border: 1px solid var(--border);
        }

        #sidebar .user-card .uc-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--coral-light);
            flex-shrink: 0;
        }

        #sidebar .user-card .uc-fallback {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--coral-light);
            color: var(--coral);
            display: flex; align-items: center; justify-content: center;
            font-size: .78rem; font-weight: 700;
            flex-shrink: 0;
        }

        #sidebar .user-card .uc-name {
            font-size: .8rem;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #sidebar .user-card .uc-role {
            font-size: .67rem;
            color: var(--muted);
            font-weight: 500;
        }

        #sidebar .user-card .uc-bell {
            margin-left: auto;
            width: 28px; height: 28px;
            border-radius: 8px;
            background: var(--coral-subtle);
            color: var(--coral);
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
            flex-shrink: 0;
            text-decoration: none;
            transition: background .15s;
            position: relative;
        }

        #sidebar .user-card .uc-bell:hover {
            background: var(--coral-light);
        }

        #sidebar .user-card .uc-bell-dot {
            position: absolute;
            top: 3px; right: 3px;
            width: 7px; height: 7px;
            background: var(--coral);
            border-radius: 50%;
            border: 2px solid var(--white);
        }

        /* ════════════════════════════════
           TOPBAR
        ════════════════════════════════ */
        #topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: var(--white);
            border-bottom: 1px solid var(--border);
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 1rem;
            box-shadow: var(--shadow-sm);
        }

        #topbar .page-title { font-size: .95rem; font-weight: 700; color: var(--navy); margin: 0; }
        #topbar .topbar-right { margin-left: auto; display: flex; align-items: center; gap: .4rem; }

        #topbar .btn-icon {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; border: none;
            background: transparent; color: var(--muted); font-size: 1.1rem;
            transition: background .15s, color .15s; position: relative;
        }
        #topbar .btn-icon:hover { background: var(--coral-light); color: var(--coral); }

        #topbar .notif-badge {
            position: absolute; top: 5px; right: 5px;
            width: 7px; height: 7px;
            background: var(--coral); border-radius: 50%; border: 2px solid var(--white);
        }

        #topbar .avatar-sm {
            width: 34px; height: 34px; border-radius: 50%; object-fit: cover;
            cursor: pointer; border: 2px solid var(--border); transition: border-color .15s;
        }
        #topbar .avatar-sm:hover { border-color: var(--coral); }

        /* ════════════════════════════════
           MAIN CONTENT
        ════════════════════════════════ */
        #main-content {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 1.75rem;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* ════════════════════════════════
           CARDS
        ════════════════════════════════ */
        .card { border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-sm); background: var(--white); }
        .card-header { background: var(--white); border-bottom: 1px solid var(--border); border-radius: var(--radius) var(--radius) 0 0 !important; padding: 1rem 1.25rem; font-weight: 600; font-size: .875rem; color: var(--navy); }
        .card-footer { background: var(--white); border-top: 1px solid var(--border); border-radius: 0 0 var(--radius) var(--radius) !important; }

        /* ════════════════════════════════
           STAT CARDS
        ════════════════════════════════ */
        .stat-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: var(--shadow-sm); transition: transform .2s, box-shadow .2s; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .stat-card .stat-icon { width: 52px; height: 52px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
        .stat-card .stat-value { font-size: 1.65rem; font-weight: 800; line-height: 1; color: var(--navy); }
        .stat-card .stat-label { font-size: .75rem; color: var(--muted); font-weight: 500; margin-top: .3rem; }
        .stat-card .stat-change { font-size: .73rem; font-weight: 600; margin-top: .35rem; }
        .stat-icon-coral  { background: var(--coral-light);  color: var(--coral); }
        .stat-icon-sage   { background: var(--sage-light);   color: var(--sage); }
        .stat-icon-gold   { background: var(--gold-light);   color: #B8892A; }
        .stat-icon-navy   { background: rgba(45,49,71,.1);   color: var(--navy); }

        /* ════════════════════════════════
           TABLES
        ════════════════════════════════ */
        .table th { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); border-bottom: 2px solid var(--border); padding: .75rem 1rem; white-space: nowrap; }
        .table td { padding: .75rem 1rem; vertical-align: middle; font-size: .865rem; border-color: var(--border); color: var(--text); }
        .table tbody tr:hover { background: var(--coral-subtle); }

        /* ════════════════════════════════
           PAGE HEADER
        ════════════════════════════════ */
        .page-header { margin-bottom: 1.5rem; }
        .page-header h1 { font-size: 1.3rem; font-weight: 700; color: var(--navy); margin: 0; }
        .breadcrumb { font-size: .78rem; margin-bottom: 0; }
        .breadcrumb-item + .breadcrumb-item::before { color: var(--muted); }
        .breadcrumb-item a { color: var(--coral); text-decoration: none; }
        .breadcrumb-item.active { color: var(--muted); }

        /* ════════════════════════════════
           BADGES
        ════════════════════════════════ */
        .badge { font-weight: 600; font-size: .68rem; padding: .35em .7em; border-radius: 20px; }
        .badge.bg-primary   { background: var(--coral)    !important; }
        .badge.bg-success   { background: var(--sage)     !important; color: #fff !important; }
        .badge.bg-warning   { background: var(--gold)     !important; color: var(--navy) !important; }
        .badge.bg-secondary { background: var(--muted)    !important; }

        /* ════════════════════════════════
           FORMS
        ════════════════════════════════ */
        .form-control, .form-select { font-size: .875rem; border-color: var(--border); border-radius: var(--radius-sm); background: var(--white); color: var(--text); }
        .form-control:focus, .form-select:focus { border-color: var(--coral); box-shadow: 0 0 0 3px rgba(217,119,87,.15); }
        .form-label { font-size: .78rem; font-weight: 600; color: var(--navy-mid); margin-bottom: .35rem; }
        .input-group-text { background: var(--coral-subtle); border-color: var(--border); color: var(--coral); border-radius: var(--radius-sm); }

        /* ════════════════════════════════
           BUTTONS
        ════════════════════════════════ */
        .btn { font-size: .855rem; font-weight: 600; border-radius: var(--radius-sm); transition: all .15s; }
        .btn-primary { background: var(--coral); border-color: var(--coral); color: #fff; }
        .btn-primary:hover, .btn-primary:focus { background: var(--coral-dark); border-color: var(--coral-dark); color: #fff; }
        .btn-outline-primary { color: var(--coral); border-color: var(--coral); }
        .btn-outline-primary:hover { background: var(--coral); border-color: var(--coral); color: #fff; }
        .btn-secondary { background: var(--navy-light); border-color: var(--navy-light); color: #fff; }
        .btn-secondary:hover { background: var(--navy-mid); border-color: var(--navy-mid); color: #fff; }
        .btn-outline-secondary { color: var(--muted); border-color: var(--border); }
        .btn-outline-secondary:hover { background: var(--bg); color: var(--text); border-color: var(--border); }
        .btn-success { background: var(--sage); border-color: var(--sage); color: #fff; }
        .btn-success:hover { background: #7A9D86; border-color: #7A9D86; color: #fff; }
        .btn-warning { background: var(--gold); border-color: var(--gold); color: var(--navy); }
        .btn-warning:hover { background: #D4AF63; border-color: #D4AF63; color: var(--navy); }

        /* ════════════════════════════════
           ALERTS
        ════════════════════════════════ */
        .alert { border-radius: var(--radius-sm); font-size: .865rem; border: none; }
        .alert-success { background: var(--sage-light);  color: #2D5A3D; }
        .alert-danger  { background: #FEF0EE;            color: #8B2516; }
        .alert-warning { background: var(--gold-light);  color: #7A5A1A; }
        .alert-info    { background: rgba(45,49,71,.07); color: var(--navy); }

        /* ════════════════════════════════
           DROPDOWNS
        ════════════════════════════════ */
        .dropdown-menu { border: 1px solid var(--border); border-radius: var(--radius-sm); box-shadow: var(--shadow-md); font-size: .855rem; }
        .dropdown-item { color: var(--text); border-radius: 6px; padding: .45rem .9rem; }
        .dropdown-item:hover { background: var(--coral-subtle); color: var(--coral); }
        .dropdown-item.text-danger:hover { background: #FEF0EE; color: #8B2516; }

        /* ════════════════════════════════
           EMPTY STATE
        ════════════════════════════════ */
        .empty-state { text-align: center; padding: 3rem 1rem; }
        .empty-state .empty-icon { font-size: 3.5rem; opacity: .25; display: block; margin-bottom: 1rem; }
        .empty-state h5 { font-weight: 700; color: var(--navy-mid); }
        .empty-state p  { color: var(--muted); font-size: .875rem; }

        /* ════════════════════════════════
           PAGINATION
        ════════════════════════════════ */
        .page-link { color: var(--coral); border-color: var(--border); border-radius: var(--radius-sm) !important; font-size: .82rem; padding: .35rem .7rem; }
        .page-link:hover { background: var(--coral-light); color: var(--coral-dark); }
        .page-item.active .page-link { background: var(--coral); border-color: var(--coral); color: #fff; }

        /* ════════════════════════════════
           PET CARDS
        ════════════════════════════════ */
        .pet-card { border-radius: var(--radius); overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow-sm); transition: transform .2s, box-shadow .2s; }
        .pet-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
        .pet-card img { width: 100%; height: 200px; object-fit: cover; }

        /* ════════════════════════════════
           MOBILE
        ════════════════════════════════ */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #topbar { left: 0; }
            #main-content { margin-left: 0; }
        }

        /* ════════════════════════════════
           SCROLLBAR
        ════════════════════════════════ */
        #sidebar::-webkit-scrollbar { width: 4px; }
        #sidebar::-webkit-scrollbar-track { background: transparent; }
        #sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ══════════════════════════════════════════ SIDEBAR ══════════════════════════════════════════ --}}
<nav id="sidebar">

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="brand-icon">🐾</div>
        <div class="brand-text">
            <span class="name">PAWS</span>
            <span class="tagline">Adoption System</span>
        </div>
    </a>

    {{-- Hero promo card --}}
    <div class="sidebar-hero">
        <h6>Find your perfect<br>companion</h6>
        @if(auth()->user()->isAdopter())
            <a href="{{ route('adopter.pets.index') }}" class="hero-btn">
                <i class="bi bi-plus-lg"></i> New Application
            </a>
        @elseif(auth()->user()->isStaff() || auth()->user()->isAdmin())
            <a href="{{ route('staff.pets.create') }}" class="hero-btn">
                <i class="bi bi-plus-lg"></i> Add New Pet
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="hero-btn">
                <i class="bi bi-grid"></i> Go to Dashboard
            </a>
        @endif
        <div class="hero-emoji">🐶</div>
    </div>

    {{-- Navigation links --}}
    @include('layouts.partials.sidebar')

    {{-- User footer card --}}
    <div class="sidebar-footer">
        <div class="user-card">
            @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar_url }}" class="uc-avatar" alt="{{ auth()->user()->name }}">
            @else
                <div class="uc-fallback">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            @endif
            <div style="min-width:0; flex:1;">
                <div class="uc-name">{{ Str::limit(auth()->user()->name, 16) }}</div>
                <div class="uc-role">{{ auth()->user()->getPrimaryRole()?->display_name ?? 'No Role' }}</div>
            </div>
            <a href="{{ route('notifications.index') }}" class="uc-bell" title="Notifications">
                <i class="bi bi-bell"></i>
                @if(auth()->user()->unread_notifications_count > 0)
                    <span class="uc-bell-dot"></span>
                @endif
            </a>
        </div>
    </div>

</nav>

{{-- ══════════════════════════════════════════ TOPBAR ══════════════════════════════════════════ --}}
<header id="topbar">
    <button class="btn-icon d-md-none" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>

    <h1 class="page-title d-none d-md-block">@yield('page-title', 'Dashboard')</h1>

    <div class="topbar-right">
        {{-- Notifications dropdown --}}
        <div class="dropdown">
            <button class="btn-icon" data-bs-toggle="dropdown">
                <i class="bi bi-bell"></i>
                @if(auth()->user()->unread_notifications_count > 0)
                    <span class="notif-badge"></span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end" style="width:320px;">
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                    <span class="fw-bold" style="font-size:.85rem; color:var(--navy);">Notifications</span>
                    @if(auth()->user()->unread_notifications_count > 0)
                        <form action="{{ route('notifications.readAll') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-link p-0" style="font-size:.75rem; color:var(--coral);">Mark all read</button>
                        </form>
                    @endif
                </div>
                @forelse(auth()->user()->systemNotifications()->latest()->take(5)->get() as $notif)
                    <a href="{{ $notif->link ?? '#' }}"
                       class="dropdown-item py-2 px-3"
                       style="font-size:.8rem; border-bottom:1px solid var(--border); white-space:normal;
                              {{ !$notif->isRead() ? 'background:var(--coral-subtle);' : '' }}">
                        <div class="d-flex gap-2">
                            <i class="bi {{ $notif->icon ?? 'bi-bell' }} mt-1" style="font-size:1rem; color:var(--coral);"></i>
                            <div>
                                <div class="fw-semibold" style="color:var(--navy);">{{ $notif->title }}</div>
                                <div style="color:var(--muted); font-size:.75rem;">{{ Str::limit($notif->message, 80) }}</div>
                                <div style="color:var(--muted); font-size:.7rem; margin-top:2px;">{{ $notif->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-3" style="font-size:.8rem; color:var(--muted);">No notifications</div>
                @endforelse
                <div class="text-center py-2">
                    <a href="{{ route('notifications.index') }}" style="font-size:.8rem; color:var(--coral);">View all</a>
                </div>
            </div>
        </div>

        {{-- User dropdown --}}
        <div class="dropdown">
            <img src="{{ auth()->user()->avatar_url }}"
                 alt="{{ auth()->user()->name }}"
                 class="avatar-sm"
                 data-bs-toggle="dropdown">
            <ul class="dropdown-menu dropdown-menu-end" style="min-width:190px;">
                <li class="px-3 py-2 border-bottom">
                    <div class="fw-semibold" style="font-size:.85rem; color:var(--navy);">{{ auth()->user()->name }}</div>
                    <div style="font-size:.73rem; color:var(--muted);">{{ auth()->user()->email }}</div>
                </li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2" style="color:var(--coral);"></i>Profile</a></li>
                <li><hr class="dropdown-divider" style="border-color:var(--border);"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="dropdown-item" style="color:#8B2516;">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

{{-- ══════════════════════════════════════════ MAIN CONTENT ══════════════════════════════════════════ --}}
<main id="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $error)
                    <li style="font-size:.85rem;">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @hasSection('breadcrumbs')
    <div class="page-header">
        <div class="d-flex align-items-start justify-content-between">
            <div>
                <h1>@yield('page-title', 'Dashboard')</h1>
                <nav aria-label="breadcrumb" class="mt-1">
                    <ol class="breadcrumb">@yield('breadcrumbs')</ol>
                </nav>
            </div>
            @hasSection('page-actions')
            <div>@yield('page-actions')</div>
            @endif
        </div>
    </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('open');
    });

    document.addEventListener('click', (e) => {
        const sidebar = document.getElementById('sidebar');
        const toggle  = document.getElementById('sidebarToggle');
        if (window.innerWidth < 768 && sidebar?.classList.contains('open')
            && !sidebar.contains(e.target) && !toggle?.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });

    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            try { new bootstrap.Alert(el).close(); } catch(e) {}
        });
    }, 5000);
</script>

@stack('scripts')
</body>
</html>