{{-- ══════════════════════════════════════════════════
     CREATE USER  —  resources/views/admin/users/create.blade.php
══════════════════════════════════════════════════ --}}
@extends('layouts.app')
@section('title', 'Add User')
@section('page-title', 'Create New User')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@push('styles')
<style>
    .form-card { border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
    .form-section-title {
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: var(--muted); margin-bottom: .75rem;
        display: flex; align-items: center; gap: .5rem;
    }
    .form-section-title::after { content: ''; flex: 1; height: 1px; background: var(--border); }
    .field-hint { font-size: .72rem; color: var(--muted); margin-top: .25rem; }
    .pass-toggle {
        cursor: pointer; background: var(--bg); border-color: var(--border); color: var(--muted);
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important;
        transition: color .15s, background .15s;
    }
    .pass-toggle:hover { color: var(--coral); background: var(--coral-subtle); }

    /* ── Form controls ── */
    .form-control, .form-select {
        transition: border-color .2s, box-shadow .2s, transform .15s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--coral); box-shadow: 0 0 0 3px rgba(217,119,87,.15);
        transform: translateY(-1px);
    }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
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
    .form-card { opacity: 0; animation: fadeUp .45s ease .1s both; }

    /* Field groups stagger — JS */
    .field-group { opacity: 0; }
    .field-group.visible { animation: fieldIn .38s ease both; }

    /* Footer */
    .card-footer { opacity: 0; animation: fadeUp .4s ease .75s both; }

    /* Submit button pulse */
    .btn-primary { animation: pulseGlow 2.5s ease 1.2s 2; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7 col-xl-6">

<form method="POST" action="{{ route('admin.users.store') }}">
@csrf

<div class="card form-card">
    <div class="card-header d-flex align-items-center gap-2" style="padding:1.1rem 1.4rem;">
        <div style="width:34px;height:34px;border-radius:9px;background:var(--coral-light);
                    display:flex;align-items:center;justify-content:center;color:var(--coral);font-size:1rem;">
            <i class="bi bi-person-plus-fill"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-bold" style="color:var(--navy);">New User Details</h6>
            <p class="mb-0" style="font-size:.72rem; color:var(--muted);">Fill in the information below to create an account</p>
        </div>
    </div>

    <div class="card-body" style="padding:1.4rem;">

        {{-- Personal Info ── --}}
        <div class="form-section-title">
            <i class="bi bi-person-badge" style="color:var(--coral);"></i> Personal Info
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6 field-group" data-idx="0">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="e.g. Juan dela Cruz" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 field-group" data-idx="1">
                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="user@example.com" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 field-group" data-idx="2">
                <label class="form-label">Phone</label>
                <input type="text" name="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone') }}" placeholder="+63 912 345 6789">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3 field-group" data-idx="3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">— Select —</option>
                    <option value="male"   @selected(old('gender') === 'male')>Male</option>
                    <option value="female" @selected(old('gender') === 'female')>Female</option>
                    <option value="other"  @selected(old('gender') === 'other')>Other</option>
                </select>
            </div>
            <div class="col-md-3 field-group" data-idx="4">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control"
                       value="{{ old('date_of_birth') }}" max="{{ date('Y-m-d') }}">
            </div>
        </div>

        {{-- Account Settings ── --}}
        <div class="form-section-title">
            <i class="bi bi-shield-check" style="color:var(--coral);"></i> Account Settings
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6 field-group" data-idx="5">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                    <option value="">— Select Role —</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                            {{ $role->display_name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 field-group" data-idx="6">
                <label class="form-label">Account Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach(['active'=>'Active','inactive'=>'Inactive','suspended'=>'Suspended'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('status','active') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 field-group" data-idx="7">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password" id="passwordInput"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Min. 8 characters" required>
                    <button type="button" class="btn pass-toggle" onclick="togglePass('passwordInput', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 field-group" data-idx="8">
                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="passwordConfirm"
                           class="form-control" placeholder="Repeat password" required>
                    <button type="button" class="btn pass-toggle" onclick="togglePass('passwordConfirm', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <div class="card-footer d-flex justify-content-end gap-2" style="background:var(--bg); padding:1rem 1.4rem;">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="bi bi-person-check me-1"></i> Create User
        </button>
    </div>
</div>

</form>
</div>
</div>
@endsection

@push('scripts')
<script>
function togglePass(id, btn) {
    const inp = document.getElementById(id);
    const isPass = inp.type === 'password';
    inp.type = isPass ? 'text' : 'password';
    btn.querySelector('i').className = isPass ? 'bi bi-eye-slash' : 'bi bi-eye';
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.field-group').forEach(el => {
        const delay = 300 + (parseInt(el.dataset.idx) * 70);
        setTimeout(() => el.classList.add('visible'), delay);
    });
});
</script>
@endpush