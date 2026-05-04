<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — PAWS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --coral:       #D97757;
            --coral-light: #F2E8E3;
            --navy:        #3A3F58;
            --bg:          #F7F5F2;
        }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg); min-height: 100vh; }

        /* ── Left panel ── */
        .left-panel {
            background: linear-gradient(135deg, var(--navy) 0%, #4A5070 100%);
            min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 48px 40px; color: #fff;
            position: relative; overflow: hidden;
        }
        .left-panel::before {
            content: '🐾'; position: absolute; font-size: 220px;
            opacity: .05; bottom: -30px; right: -20px; pointer-events: none;
        }
        .left-brand-icon {
            width: 64px; height: 64px; background: var(--coral);
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; font-size: 28px; margin-bottom: 20px;
        }
        .left-panel h2 { font-size: 1.8rem; font-weight: 800; margin-bottom: 12px; }
        .left-panel p  { font-size: .9rem; opacity: .75; line-height: 1.75; max-width: 280px; text-align: center; }

        /* Lock icon illustration */
        .lock-illustration {
            width: 90px; height: 90px;
            background: rgba(217,119,87,.15);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.4rem;
            margin: 0 auto 20px;
            border: 2px solid rgba(217,119,87,.25);
        }

        /* ── Right panel ── */
        .right-panel {
            min-height: 100vh; display: flex; align-items: center;
            justify-content: center; padding: 40px 24px; background: #fff;
        }
        .box { width: 100%; max-width: 420px; }
        .box h3 { font-size: 1.5rem; font-weight: 700; color: var(--navy); margin-bottom: 6px; }
        .box p.sub { font-size: .875rem; color: #888; margin-bottom: 28px; line-height: 1.7; }

        /* ── Form ── */
        .form-label { font-size: .85rem; font-weight: 600; color: var(--navy); margin-bottom: 6px; }
        .form-control {
            border: 1.5px solid #E5E7EB; border-radius: 10px;
            padding: 11px 14px; font-size: .9rem;
            transition: border-color .2s, box-shadow .2s, transform .15s;
        }
        .form-control:focus {
            border-color: var(--coral);
            box-shadow: 0 0 0 3px rgba(217,119,87,.15);
            outline: none; transform: translateY(-1px);
        }
        .input-icon-wrap { position: relative; }
        .input-icon-wrap .bi {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%); color: #aaa; font-size: .9rem;
            transition: color .2s;
        }
        .input-icon-wrap:focus-within .bi { color: var(--coral); }
        .input-icon-wrap .form-control { padding-left: 38px; }

        .btn-send {
            background: var(--coral); color: #fff; border: none;
            border-radius: 25px; padding: 13px; font-size: .95rem;
            font-weight: 600; width: 100%;
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-send:hover {
            background: #c4654a; transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217,119,87,.4); color: #fff;
        }
        .btn-send:active { transform: translateY(0); box-shadow: none; }

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
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(217,119,87,0); }
            50%       { box-shadow: 0 0 0 8px rgba(217,119,87,.2); }
        }
        @keyframes lockWiggle {
            0%, 100% { transform: rotate(0deg); }
            20%       { transform: rotate(-8deg); }
            40%       { transform: rotate(8deg); }
            60%       { transform: rotate(-5deg); }
            80%       { transform: rotate(5deg); }
        }

        /* Panel entrances */
        .left-panel  { animation: slideInLeft  .6s cubic-bezier(.25,.46,.45,.94) both; }
        .right-panel { animation: slideInRight .6s cubic-bezier(.25,.46,.45,.94) .1s both; }

        /* Brand icon floats */
        .left-brand-icon { animation: float 3.5s ease-in-out infinite; }

        /* Lock illustration wiggles once after load to hint interaction */
        .lock-illustration { animation: fadeUp .5s ease .3s both; }
        .lock-illustration.wiggle { animation: lockWiggle .6s ease; }

        /* Right form stagger */
        .box h3      { animation: fadeUp .5s ease .25s both; }
        .box p.sub   { animation: fadeUp .5s ease .32s both; }
        .box .mb-4   { animation: fadeUp .5s ease .40s both; }
        .btn-send    { animation: fadeUp .5s ease .50s both, pulseGlow 2.5s ease 1.1s 2; }
        .back-link   { animation: fadeUp .5s ease .58s both; }
    </style>
</head>
<body>
<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">

        {{-- Left Panel --}}
        <div class="col-lg-5 d-none d-lg-flex">
            <div class="left-panel w-100 text-center">
                <div class="left-brand-icon">🐾</div>
                <h2>Reset Password</h2>
                <p>No worries! Enter your email and we'll send you a link to reset your password.</p>
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="col-lg-7 right-panel">
            <div class="box">

                {{-- Mobile logo --}}
                <div class="text-center d-lg-none mb-4">
                    <div class="left-brand-icon mx-auto mb-2">🐾</div>
                    <strong style="color:var(--navy);font-size:1.2rem;">PAWS</strong>
                </div>

                {{-- Lock illustration --}}
                <div class="lock-illustration" id="lockIcon">
                    🔑
                </div>

                <h3>Forgot Password?</h3>
                <p class="sub">
                    No problem! Just enter your email address and we'll send you a password reset link.
                </p>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="alert alert-success rounded-3 mb-3" style="font-size:.85rem; background:#EAF0EC; color:#2D5A3D; border:none;">
                        <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label" for="email">Email Address</label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-envelope"></i>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="you@example.com"
                                   required autofocus>
                        </div>
                        @error('email')
                            <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-send">
                        <i class="bi bi-send me-2"></i>Send Reset Link
                    </button>
                </form>

                <div class="back-link mt-3">
                    <a href="{{ route('login') }}">
                        <i class="bi bi-arrow-left me-1"></i>Back to Login
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Wiggle the lock icon once after page loads to draw attention
    window.addEventListener('load', () => {
        setTimeout(() => {
            const lock = document.getElementById('lockIcon');
            lock.classList.add('wiggle');
            lock.addEventListener('animationend', () => lock.classList.remove('wiggle'), { once: true });
        }, 900);

        // Re-wiggle if user hovers it
        document.getElementById('lockIcon').addEventListener('mouseenter', function () {
            this.classList.remove('wiggle');
            void this.offsetWidth; // force reflow to restart animation
            this.classList.add('wiggle');
        });
    });
</script>
</body>
</html>