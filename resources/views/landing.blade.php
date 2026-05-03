<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAWS — Pet Adoption & Welfare System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --coral:       #D97757;
            --coral-light: #F2E8E3;
            --sage:        #8FAF9A;
            --sage-light:  #EAF0EC;
            --gold:        #E6C27A;
            --gold-light:  #FBF3E2;
            --navy:        #3A3F58;
            --bg:          #F7F5F2;
            --text:        #333333;
        }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg); color: var(--text); }

        /* Topbar */
        .topbar { background: var(--navy); color: rgba(255,255,255,.7); font-size: .8rem; padding: 7px 0; }
        .topbar a { color: rgba(255,255,255,.7); text-decoration: none; margin-left: 16px; }
        .topbar a:hover { color: #fff; }

        /* Navbar */
        .main-navbar { background: #fff; border-bottom: 1px solid #eee; box-shadow: 0 2px 12px rgba(0,0,0,.06); padding: 12px 0; position: sticky; top: 0; z-index: 1000; }
        .brand-icon { width: 44px; height: 44px; background: var(--coral); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .brand-name { font-size: 1.1rem; font-weight: 700; color: var(--navy); line-height: 1.1; }
        .brand-sub  { font-size: .65rem; color: var(--coral); text-transform: uppercase; letter-spacing: 1px; }
        .main-navbar .nav-link { color: var(--navy) !important; font-size: .875rem; font-weight: 500; padding: 6px 4px !important; border-bottom: 2px solid transparent; transition: all .2s; }
        .main-navbar .nav-link:hover { color: var(--coral) !important; border-bottom-color: var(--coral); }
        .btn-nav-login { border: 1.5px solid var(--coral); color: var(--coral); background: transparent; border-radius: 20px; padding: 7px 20px; font-size: .85rem; font-weight: 500; transition: all .2s; text-decoration: none; }
        .btn-nav-login:hover { background: var(--coral); color: #fff; }
        .btn-nav-register { background: var(--coral); color: #fff; border: none; border-radius: 20px; padding: 7px 20px; font-size: .85rem; font-weight: 500; transition: background .2s; text-decoration: none; }
        .btn-nav-register:hover { background: #c4654a; color: #fff; }

        /* Hero */
        .hero-section { background: url('/images/hero-pet.png') center/cover no-repeat; color: #fff; padding: 80px 0 100px; position: relative; overflow: hidden; }
        .hero-section::before { content: ''; position: absolute; inset: 0; background: rgba(58,63,88,0.50); z-index: 0; }
        .hero-section .container { position: relative; z-index: 1; }
        .hero-section::after { content: '🐾'; position: absolute; right: 8%; bottom: -20px; font-size: 180px; opacity: .05; pointer-events: none; }
        .hero-tag { display: inline-block; background: rgba(217,119,87,.25); color: var(--gold); border: 1px solid rgba(230,194,122,.3); padding: 5px 16px; border-radius: 20px; font-size: .78rem; margin-bottom: 20px; letter-spacing: .5px; }
        .hero-section h1 { font-size: clamp(2rem, 4vw, 2.8rem); font-weight: 800; line-height: 1.15; margin-bottom: 18px; }
        .hero-section h1 span { color: var(--coral); }
        .hero-section .lead { font-size: 1rem; opacity: .8; line-height: 1.75; max-width: 500px; margin-bottom: 32px; }
        .btn-hero-primary { background: var(--coral); color: #fff; border: none; border-radius: 25px; padding: 13px 32px; font-size: .95rem; font-weight: 600; transition: all .2s; text-decoration: none; display: inline-block; }
        .btn-hero-primary:hover { background: #c4654a; transform: translateY(-2px); color: #fff; }
        .btn-hero-outline { background: transparent; color: #fff; border: 1.5px solid rgba(255,255,255,.5); border-radius: 25px; padding: 13px 32px; font-size: .95rem; transition: all .2s; text-decoration: none; display: inline-block; }
        .btn-hero-outline:hover { background: rgba(255,255,255,.1); color: #fff; }

        /* Stats */
        .stats-bar { background: #fff; box-shadow: 0 4px 24px rgba(0,0,0,.08); border-radius: 16px; margin-top: -36px; position: relative; z-index: 10; overflow: hidden; }
        .stat-item { padding: 24px 16px; text-align: center; border-right: 1px solid #f0f0f0; }
        .stat-item:last-child { border-right: none; }
        .stat-num { font-size: 2rem; font-weight: 800; color: var(--coral); }
        .stat-lbl { font-size: .7rem; color: #999; text-transform: uppercase; letter-spacing: .6px; margin-top: 4px; }

        /* Section shared */
        .section-tag { display: inline-block; background: var(--coral-light); color: var(--coral); font-size: .75rem; padding: 4px 14px; border-radius: 20px; margin-bottom: 10px; font-weight: 500; }
        .section-title { font-size: 1.6rem; font-weight: 700; color: var(--navy); margin-bottom: 8px; }
        .section-sub { font-size: .9rem; color: #777; line-height: 1.6; }

        /* Pet Cards */
        .pet-card { background: #fff; border-radius: 18px; overflow: hidden; box-shadow: 0 2px 18px rgba(0,0,0,.07); transition: transform .2s, box-shadow .2s; border: none; }
        .pet-card:hover { transform: translateY(-5px); box-shadow: 0 8px 30px rgba(0,0,0,.12); }
        .pet-card-img { height: 200px; object-fit: cover; width: 100%; }
        .pet-card-placeholder { height: 200px; display: flex; align-items: center; justify-content: center; font-size: 4rem; background: var(--coral-light); }
        .badge-avail { background: var(--sage-light); color: var(--sage); font-size: .7rem; padding: 3px 10px; border-radius: 20px; font-weight: 500; }
        .btn-adopt { background: var(--coral); color: #fff; border: none; border-radius: 20px; padding: 9px 0; font-size: .85rem; font-weight: 500; width: 100%; transition: background .2s; text-decoration: none; display: block; text-align: center; }
        .btn-adopt:hover { background: #c4654a; color: #fff; }

        /* Steps */
        .steps-section { background: var(--sage-light); }
        .step-card { background: #fff; border-radius: 18px; padding: 32px 20px; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,.05); height: 100%; }
        .step-num { width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 1.1rem; font-weight: 700; }
        .step-title { font-size: .95rem; font-weight: 600; color: var(--navy); margin-bottom: 8px; }
        .step-desc { font-size: .82rem; color: #888; line-height: 1.65; margin: 0; }

        /* Mission */
        .mission-img-box { background: var(--coral-light); border-radius: 24px; height: 320px; display: flex; align-items: center; justify-content: center; font-size: 7rem; }
        .btn-mission { background: var(--coral); color: #fff; border: none; border-radius: 25px; padding: 12px 30px; font-size: .9rem; font-weight: 500; transition: background .2s; text-decoration: none; display: inline-block; }
        .btn-mission:hover { background: #c4654a; color: #fff; }

        /* CTA */
        .cta-section { background: linear-gradient(120deg, var(--coral) 0%, #C45E3E 100%); color: #fff; }
        .btn-cta-white { background: #fff; color: var(--coral); border: none; border-radius: 25px; padding: 13px 36px; font-size: .95rem; font-weight: 600; transition: transform .15s; text-decoration: none; display: inline-block; }
        .btn-cta-white:hover { transform: translateY(-2px); color: var(--coral); }
        .btn-cta-outline { background: transparent; color: #fff; border: 1.5px solid rgba(255,255,255,.6); border-radius: 25px; padding: 13px 36px; font-size: .95rem; transition: all .2s; text-decoration: none; display: inline-block; }
        .btn-cta-outline:hover { background: rgba(255,255,255,.15); color: #fff; }

        /* Footer */
        .main-footer { background: var(--navy); color: rgba(255,255,255,.55); font-size: .82rem; }
        .footer-brand { color: #fff; font-size: 1rem; font-weight: 600; }
    </style>
</head>
<body>

{{-- Topbar --}}
<div class="topbar">
    <div class="container d-flex justify-content-between align-items-center">
        <span><i class="bi bi-geo-alt me-1"></i>Davao City, Philippines</span>
        <div>
            @auth
                <a href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>My Dashboard</a>
            @else
                <a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
                <a href="{{ route('register') }}"><i class="bi bi-person-plus me-1"></i>Register</a>
            @endauth
        </div>
    </div>
</div>

{{-- Navbar --}}
<nav class="main-navbar">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            <div class="brand-icon">🐾</div>
            <div>
                <div class="brand-name">PAWS</div>
                <div class="brand-sub">Pet Adoption System</div>
            </div>
        </a>
        <ul class="nav d-none d-lg-flex gap-3 mb-0">
            <li class="nav-item"><a class="nav-link" href="#pets">Available Pets</a></li>
            <li class="nav-item"><a class="nav-link" href="#how">How to Adopt</a></li>
            <li class="nav-item"><a class="nav-link" href="#mission">About</a></li>
        </ul>
        <div class="d-flex gap-2">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-nav-register">
                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-nav-login">Login</a>
                <a href="{{ route('register') }}" class="btn-nav-register">Register</a>
            @endauth
        </div>
    </div>
</nav>

{{-- Hero --}}
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="hero-tag"><i class="bi bi-heart-fill me-1"></i>Find your forever companion</div>
                <h1>The Voice of the<br><span>Voiceless</span> Animals</h1>
                <p class="lead">
                    PAWS connects loving families with pets in need of a forever home.
                    Browse available animals, apply online, and change a life today.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#pets" class="btn-hero-primary">
                        <i class="bi bi-search-heart me-2"></i>Adopt a Pet
                    </a>
                    <a href="#how" class="btn-hero-outline">How it Works</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<div class="container">
    <div class="stats-bar row g-0 mb-5">
        <div class="col-6 col-md-3 stat-item">
            <div class="stat-num">{{ \App\Models\Pet::where('status','adopted')->count() }}+</div>
            <div class="stat-lbl">Pets Adopted</div>
        </div>
        <div class="col-6 col-md-3 stat-item">
            <div class="stat-num">{{ \App\Models\Pet::where('status','available')->count() }}</div>
            <div class="stat-lbl">Awaiting Homes</div>
        </div>
        <div class="col-6 col-md-3 stat-item">
            <div class="stat-num">{{ \App\Models\User::whereHas('roles', fn($q)=>$q->where('name','adopter'))->count() }}</div>
            <div class="stat-lbl">Registered Adopters</div>
        </div>
        <div class="col-6 col-md-3 stat-item">
            <div class="stat-num">{{ \App\Models\PetCategory::where('is_active',true)->count() }}</div>
            <div class="stat-lbl">Pet Categories</div>
        </div>
    </div>
</div>

{{-- Available Pets --}}
<section id="pets" class="py-5" style="background:var(--bg);">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-tag">Available for Adoption</div>
            <h2 class="section-title">Pets Looking for a Home</h2>
            <p class="section-sub">All pets are vaccinated, health-checked, and ready to love you.</p>
        </div>

        @php
            $featuredPets = \App\Models\Pet::with(['category','breed'])
                ->where('status','available')
                ->where('is_admin_approved', true)
                ->latest()->take(6)->get();
        @endphp

        @if($featuredPets->isEmpty())
            <div class="text-center py-5 text-muted">
                <div style="font-size:4rem;">🐾</div>
                <p>No pets available right now. Check back soon!</p>
            </div>
        @else
            <div class="row g-4">
                @foreach($featuredPets as $pet)
                <div class="col-sm-6 col-lg-4">
                    <div class="pet-card h-100">
                        <div class="position-relative">
                            @if($pet->primary_image)
                                <img src="{{ $pet->primary_image_url }}"
                                     class="pet-card-img" alt="{{ $pet->name }}">
                            @else
                                <div class="pet-card-placeholder">🐾</div>
                            @endif
                            <span class="badge-avail position-absolute" style="top:12px;left:12px;">
                                Available
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h5 class="fw-bold mb-0" style="color:var(--navy);">{{ $pet->name }}</h5>
                                <span class="badge rounded-pill"
                                      style="background:var(--gold-light);color:#9A6F1A;font-size:.7rem;">
                                    {{ $pet->category->name ?? '—' }}
                                </span>
                            </div>
                            <p class="text-muted mb-2" style="font-size:.8rem;">
                                {{ $pet->breed->name ?? 'Mixed Breed' }} &bull; {{ ucfirst($pet->gender) }}
                            </p>
                            @if($pet->description)
                            <p class="mb-3" style="font-size:.83rem;color:#666;
                               display:-webkit-box;-webkit-line-clamp:2;
                               -webkit-box-orient:vertical;overflow:hidden;">
                                {{ $pet->description }}
                            </p>
                            @endif
                            @auth
                                <a href="{{ route('adopter.pets.show', $pet) }}" class="btn-adopt">
                                    <i class="bi bi-heart me-1"></i>Adopt {{ $pet->name }}
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="btn-adopt">
                                    <i class="bi bi-heart me-1"></i>Adopt {{ $pet->name }}
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- How to Adopt --}}
<section id="how" class="py-5 steps-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-tag">Simple Process</div>
            <h2 class="section-title">How to Adopt</h2>
            <p class="section-sub">Four simple steps to bring home your new best friend.</p>
        </div>
        <div class="row g-4">
            <div class="col-sm-6 col-lg-3">
                <div class="step-card">
                    <div class="step-num" style="background:var(--coral-light);color:var(--coral);">1</div>
                    <div class="step-title"><i class="bi bi-person-plus me-1"></i>Register</div>
                    <p class="step-desc">Create a free account and complete your adopter profile.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="step-card">
                    <div class="step-num" style="background:var(--sage-light);color:var(--sage);">2</div>
                    <div class="step-title"><i class="bi bi-search-heart me-1"></i>Browse Pets</div>
                    <p class="step-desc">Explore available pets and find the perfect match for your lifestyle.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="step-card">
                    <div class="step-num" style="background:var(--gold-light);color:#B8862A;">3</div>
                    <div class="step-title"><i class="bi bi-file-earmark-text me-1"></i>Apply Online</div>
                    <p class="step-desc">Submit an adoption application. Our staff will review it promptly.</p>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="step-card">
                    <div class="step-num" style="background:#EEF2FF;color:var(--navy);">4</div>
                    <div class="step-title"><i class="bi bi-house-heart me-1"></i>Bring Home</div>
                    <p class="step-desc">Once approved, complete the fee and take your pet home!</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Mission --}}
<section id="mission" class="py-5" style="background:#fff;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="mission-img-box">🐾</div>
            </div>
            <div class="col-lg-7">
                <div class="section-tag">Our Mission</div>
                <h2 class="section-title">Every Animal Deserves Love and a Safe Home</h2>
                <p class="text-muted mb-3" style="line-height:1.8;">
                    PAWS is dedicated to rescuing, rehabilitating, and rehoming animals in need.
                    We believe every pet deserves a loving family and a safe forever home.
                </p>
                <p class="text-muted mb-4" style="line-height:1.8;">
                    Our team of dedicated staff and volunteers work tirelessly to ensure
                    every animal in our care receives proper medical attention, nutrition, and love.
                </p>
                <a href="{{ route('register') }}" class="btn-mission">
                    <i class="bi bi-heart me-2"></i>Start Your Adoption Journey
                </a>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-section py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-3" style="font-size:1.9rem;">Ready to Change a Life?</h2>
        <p class="mb-4" style="opacity:.85;max-width:480px;margin:0 auto 28px;line-height:1.7;font-size:.95rem;">
            Every pet in our care deserves a loving home.
            Join PAWS today and start your adoption journey.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('register') }}" class="btn-cta-white">
                <i class="bi bi-heart-fill me-2"></i>Get Started
            </a>
            <a href="{{ route('login') }}" class="btn-cta-outline">
                Already have an account?
            </a>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="main-footer py-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <div class="footer-brand">🐾 PAWS</div>
            <div class="mt-1">Pet Adoption &amp; Welfare System · Davao City</div>
        </div>
        <div style="font-size:.75rem;color:rgba(255,255,255,.3);">
            &copy; {{ date('Y') }} PAWS. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>