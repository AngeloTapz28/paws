@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('breadcrumbs')
    <li class="breadcrumb-item active">My Profile</li>
@endsection

@push('styles')
<style>
    /* ── Avatar section ── */
    .avatar-wrap {
        display: flex; align-items: center; gap: 1.25rem;
        padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border);
    }
    .profile-avatar {
        width: 72px; height: 72px; border-radius: 50%;
        object-fit: cover; border: 3px solid var(--coral-light);
        box-shadow: 0 4px 14px rgba(217,119,87,.2);
        transition: transform .2s, box-shadow .2s;
    }
    .profile-avatar:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(217,119,87,.3); }
    .profile-avatar-fallback {
        width: 72px; height: 72px; border-radius: 50%;
        background: var(--coral); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem; font-weight: 700;
        border: 3px solid var(--coral-light);
        box-shadow: 0 4px 14px rgba(217,119,87,.25);
        transition: transform .2s;
    }
    .profile-avatar-fallback:hover { transform: scale(1.05); }
    .avatar-name { font-size: 1.1rem; font-weight: 800; color: var(--navy); margin-bottom: .15rem; }
    .avatar-role {
        font-size: .72rem; font-weight: 600; padding: .25em .75em;
        border-radius: 20px; background: var(--coral-subtle); color: var(--coral);
        display: inline-block;
    }

    /* ── Form ── */
    .form-label { font-size: .8rem; font-weight: 600; color: var(--navy-mid); margin-bottom: .35rem; }
    .form-control, .form-select {
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        font-size: .875rem; transition: border-color .2s, box-shadow .2s, transform .15s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--coral); box-shadow: 0 0 0 3px rgba(217,119,87,.15);
        outline: none; transform: translateY(-1px);
    }
    .form-control:disabled, .form-control[readonly] {
        background: var(--bg); color: var(--muted); cursor: not-allowed;
    }

    /* ── Save button ── */
    .btn-save {
        background: var(--coral); color: #fff; border: none;
        border-radius: 20px; padding: .6rem 1.75rem;
        font-size: .9rem; font-weight: 700;
        transition: background .2s, transform .15s, box-shadow .2s;
        display: inline-flex; align-items: center; gap: .4rem;
    }
    .btn-save:hover {
        background: var(--coral-dark); color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 5px 16px rgba(217,119,87,.35);
    }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes avatarPop {
        0%   { opacity: 0; transform: scale(0.7); }
        70%  { transform: scale(1.08); }
        100% { opacity: 1; transform: scale(1); }
    }
    @keyframes fieldIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(217,119,87,0); }
        50%       { box-shadow: 0 0 0 8px rgba(217,119,87,.2); }
    }

    /* Card fades up */
    .profile-card { opacity: 0; animation: fadeUp .45s ease .1s both; }

    /* Avatar pops */
    .profile-avatar,
    .profile-avatar-fallback { animation: avatarPop .5s cubic-bezier(.34,1.56,.64,1) .25s both; opacity: 0; animation-fill-mode: forwards; }

    /* Avatar name/role */
    .avatar-name { opacity: 0; animation: fadeUp .4s ease .4s both; }
    .avatar-role { opacity: 0; animation: fadeUp .4s ease .46s both; }

    /* Form field groups stagger */
    .field-group { opacity: 0; }
    .field-group.visible { animation: fieldIn .38s ease both; }

    /* Save button */
    .btn-save { animation: pulseGlow 2.5s ease 1.5s 2; }
</style>
@endpush

@section('content')

<div class="row justify-content-center">
<div class="col-lg-7">

    <div class="card profile-card">

        {{-- Avatar section ── --}}
        <div class="avatar-wrap">
            @if($user->avatar)
                <img src="{{ $user->avatar_url }}" class="profile-avatar" alt="{{ $user->name }}">
            @else
                <div class="profile-avatar-fallback">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
            @endif
            <div>
                <div class="avatar-name">{{ $user->name }}</div>
                <span class="avatar-role">
                    {{ $user->getPrimaryRole()?->display_name ?? 'Adopter' }}
                </span>
            </div>
        </div>

        {{-- Form ── --}}
        <form method="POST" action="{{ route('adopter.profile.update') }}">
            @csrf @method('PATCH')

            <div class="card-body" style="padding:1.5rem;">
                <div class="row g-3">

                    <div class="col-md-6 field-group" data-idx="0">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 field-group" data-idx="1">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control"
                               value="{{ $user->email }}" disabled>
                        <div style="font-size:.73rem; color:var(--muted); margin-top:.3rem;">
                            Email cannot be changed.
                        </div>
                    </div>

                    <div class="col-md-6 field-group" data-idx="2">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="+63 9XX XXX XXXX">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 field-group" data-idx="3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Prefer not to say</option>
                            <option value="male"   @selected(old('gender', $user->gender) === 'male')>Male</option>
                            <option value="female" @selected(old('gender', $user->gender) === 'female')>Female</option>
                            <option value="other"  @selected(old('gender', $user->gender) === 'other')>Other</option>
                        </select>
                    </div>

                    <div class="col-md-6 field-group" data-idx="4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth"
                               class="form-control"
                               value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-12 field-group" data-idx="5">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"
                                  placeholder="Street, Barangay, City, Province">{{ old('address', $user->address) }}</textarea>
                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-end" style="background:var(--white); border-top:1px solid var(--border); padding:1rem 1.5rem;">
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-lg"></i> Save Changes
                </button>
            </div>

        </form>
    </div>

</div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.field-group').forEach(el => {
        const delay = 500 + (parseInt(el.dataset.idx) * 80);
        setTimeout(() => el.classList.add('visible'), delay);
    });
});
</script>
@endpush