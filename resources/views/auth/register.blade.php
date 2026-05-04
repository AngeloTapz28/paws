<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — PAWS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --coral:       #D97757;
            --coral-light: #F2E8E3;
            --sage:        #8FAF9A;
            --navy:        #3A3F58;
            --bg:          #F7F5F2;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            min-height: 100vh;
        }

        /* ── Left panel ── */
        .left-panel {
            background: linear-gradient(135deg, var(--navy) 0%, #4A5070 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '🐾';
            position: absolute;
            font-size: 220px;
            opacity: .05;
            bottom: -30px;
            right: -20px;
            pointer-events: none;
        }
        .left-brand-icon {
            width: 64px; height: 64px;
            background: var(--coral);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
        }
        .left-panel h2 { font-size: 1.8rem; font-weight: 800; margin-bottom: 12px; }
        .left-panel p  { font-size: .9rem; opacity: .75; line-height: 1.75; max-width: 280px; text-align: center; }

        .steps-list { margin-top: 32px; text-align: left; width: 100%; max-width: 260px; }
        .step-item {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 16px;
            opacity: 0; /* start hidden — JS will reveal with stagger */
        }
        .step-circle {
            width: 32px; height: 32px; border-radius: 50%;
            background: rgba(217,119,87,.3); color: var(--coral);
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; font-weight: 700; flex-shrink: 0;
            border: 1px solid rgba(217,119,87,.4);
            transition: background .3s, transform .3s;
        }
        .step-item.visible .step-circle {
            animation: stepPop .4s cubic-bezier(.34,1.56,.64,1) both;
        }
        .step-text { font-size: .83rem; opacity: .8; }

        /* ── Right panel ── */
        .right-panel {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            background: #fff;
        }
        .register-box { width: 100%; max-width: 440px; }
        .register-box h3 { font-size: 1.5rem; font-weight: 700; color: var(--navy); margin-bottom: 6px; }
        .register-box p.sub { font-size: .875rem; color: #888; margin-bottom: 28px; }

        /* ── Form ── */
        .form-label { font-size: .85rem; font-weight: 600; color: var(--navy); margin-bottom: 6px; }
        .form-control {
            border: 1.5px solid #E5E7EB;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: .9rem;
            transition: border-color .2s, box-shadow .2s, transform .15s;
        }
        .form-control:focus {
            border-color: var(--coral);
            box-shadow: 0 0 0 3px rgba(217,119,87,.15);
            outline: none;
            transform: translateY(-1px);
        }
        .input-icon-wrap { position: relative; }
        .input-icon-wrap .bi {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #aaa; font-size: .9rem;
            transition: color .2s;
        }
        .input-icon-wrap:focus-within .bi { color: var(--coral); }
        .input-icon-wrap .form-control { padding-left: 38px; }

        .btn-register {
            background: var(--coral);
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 13px;
            font-size: .95rem;
            font-weight: 600;
            width: 100%;
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-register:hover {
            background: #c4654a;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217,119,87,.4);
            color: #fff;
        }
        .btn-register:active { transform: translateY(0); box-shadow: none; }

        .divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; color: #ccc; font-size: .8rem; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #eee; }

        .btn-login-link {
            display: block;
            text-align: center;
            border: 1.5px solid var(--coral);
            color: var(--coral);
            border-radius: 25px;
            padding: 11px;
            font-size: .9rem;
            font-weight: 500;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-login-link:hover { background: var(--coral); color: #fff; transform: translateY(-1px); }

        .back-link { text-align: center; margin-top: 20px; font-size: .82rem; color: #aaa; }
        .back-link a { color: var(--coral); text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }

        /* ════════════════════════════════
           ANIMATIONS
        ════════════════════════════════ */

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(40px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-6px); }
        }

        @keyframes stepPop {
            from { opacity: 0; transform: translateX(-16px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(217,119,87,0); }
            50%       { box-shadow: 0 0 0 8px rgba(217,119,87,.2); }
        }

        /* Panel entrance */
        .left-panel  { animation: slideInLeft  .6s cubic-bezier(.25,.46,.45,.94) both; }
        .right-panel { animation: slideInRight .6s cubic-bezier(.25,.46,.45,.94) .1s both; }

        /* Brand icon float */
        .left-brand-icon { animation: float 3.5s ease-in-out infinite; }

        /* Right panel form stagger */
        .register-box h3           { animation: fadeUp .5s ease .25s both; }
        .register-box p.sub        { animation: fadeUp .5s ease .32s both; }
        .register-box .mb-3:nth-child(1) { animation: fadeUp .5s ease .38s both; }
        .register-box .mb-3:nth-child(2) { animation: fadeUp .5s ease .44s both; }
        .register-box .mb-3:nth-child(3) { animation: fadeUp .5s ease .50s both; }
        .register-box .mb-4        { animation: fadeUp .5s ease .56s both; }
        .btn-register              { animation: fadeUp .5s ease .62s both, pulseGlow 2.5s ease 1.3s 2; }
        .divider                   { animation: fadeUp .5s ease .68s both; }
        .btn-login-link            { animation: fadeUp .5s ease .74s both; }
        .back-link                 { animation: fadeUp .5s ease .80s both; }
    </style>
</head>
<body>
<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">

        {{-- Left Panel --}}
        <div class="col-lg-5 d-none d-lg-flex">
            <div class="left-panel w-100 text-center">
                <div class="left-brand-icon">🐾</div>
                <h2>Join PAWS Today!</h2>
                <p>Create your free account and start your pet adoption journey.</p>

                <div class="steps-list">
                    <div class="step-item">
                        <div class="step-circle">1</div>
                        <div class="step-text">Create your free account</div>
                    </div>
                    <div class="step-item">
                        <div class="step-circle">2</div>
                        <div class="step-text">Browse available pets</div>
                    </div>
                    <div class="step-item">
                        <div class="step-circle">3</div>
                        <div class="step-text">Submit an adoption application</div>
                    </div>
                    <div class="step-item">
                        <div class="step-circle">4</div>
                        <div class="step-text">Bring your pet home!</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="col-lg-7 right-panel">
            <div class="register-box">

                {{-- Logo (mobile only) --}}
                <div class="text-center d-lg-none mb-4">
                    <div class="left-brand-icon mx-auto mb-2">🐾</div>
                    <strong style="color:var(--navy);font-size:1.2rem;">PAWS</strong>
                </div>

                <h3>Create Account</h3>
                <p class="sub">Fill in your details to get started.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-3">
                        <label class="form-label" for="name">Full Name</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-person"></i>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Juan dela Cruz"
                                   required autofocus>
                        </div>
                        @error('name')
                            <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label" for="email">Email Address</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope"></i>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="you@example.com"
                                   required>
                        </div>
                        @error('email')
                            <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock"></i>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 characters"
                                   required>
                        </div>
                        @error('password')
                            <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-4">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control"
                                   placeholder="Repeat password"
                                   required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-register">
                        <i class="bi bi-person-check me-2"></i>Create Account
                    </button>
                </form>

                <div class="divider">already have an account?</div>

                <a href="{{ route('login') }}" class="btn-login-link">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In Instead
                </a>

                <div class="back-link">
                    <a href="{{ url('/') }}"><i class="bi bi-arrow-left me-1"></i>Back to Home</a>
                </div>

            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Stagger the step items on the left panel with a slight delay each
    window.addEventListener('load', () => {
        const steps = document.querySelectorAll('.step-item');
        steps.forEach((step, i) => {
            setTimeout(() => {
                step.style.animation = `stepPop .45s cubic-bezier(.34,1.56,.64,1) both`;
                step.classList.add('visible');
            }, 600 + (i * 150)); // starts after panel animates in
        });
    });
</script>
</body>
</html>