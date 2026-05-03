@extends('layouts.app')
@section('title', 'Edit ' . $user->name)
@section('page-title', 'Edit User')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
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
    .user-header-avatar {
        width: 52px; height: 52px; border-radius: 50%; object-fit: cover;
        border: 3px solid var(--coral-light); flex-shrink: 0;
    }
    .user-header-fallback {
        width: 52px; height: 52px; border-radius: 50%;
        background: var(--coral-light); color: var(--coral);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; font-weight: 700; flex-shrink: 0;
    }
    .pass-toggle { cursor: pointer; background: var(--bg); border-color: var(--border); color: var(--muted); border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important; }
    .pass-toggle:hover { color: var(--coral); background: var(--coral-subtle); }
    .danger-zone {
        border: 1px solid #F5C6C0; border-radius: var(--radius);
        background: #FFFAFA; margin-top: 1.25rem;
    }
    .danger-zone .card-header { background: #FEF0EE; border-bottom: 1px solid #F5C6C0; border-radius: var(--radius) var(--radius) 0 0 !important; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7 col-xl-6">

<form method="POST" action="{{ route('admin.users.update', $user) }}">
@csrf @method('PUT')

<div class="card form-card">
    {{-- Card Header with user avatar --}}
    <div class="card-header d-flex align-items-center gap-3" style="padding:1.1rem 1.4rem;">
        @if($user->avatar)
            <img src="{{ $user->avatar_url }}" class="user-header-avatar" alt="">
        @else
            <div class="user-header-fallback">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
        @endif
        <div>
            <h6 class="mb-0 fw-bold" style="color:var(--navy);">{{ $user->name }}</h6>
            <p class="mb-0" style="font-size:.72rem; color:var(--muted);">{{ $user->email }}</p>
        </div>
        <div class="ms-auto">
            @foreach($user->roles as $role)
                <span style="font-size:.7rem; font-weight:700; padding:.28em .75em; border-radius:20px;
                             background:var(--coral-subtle); color:var(--coral);">
                    {{ $role->display_name }}
                </span>
            @endforeach
        </div>
    </div>

    <div class="card-body" style="padding:1.4rem;">

        <div class="form-section-title"><i class="bi bi-person" style="color:var(--coral);"></i> Personal Info</div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $user->phone) }}" placeholder="+63 912 345 6789">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">— Select —</option>
                    <option value="male"   @selected(old('gender', $user->gender) === 'male')>Male</option>
                    <option value="female" @selected(old('gender', $user->gender) === 'female')>Female</option>
                    <option value="other"  @selected(old('gender', $user->gender) === 'other')>Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control"
                       value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                       max="{{ date('Y-m-d') }}">
            </div>
        </div>

        <div class="form-section-title"><i class="bi bi-shield-check" style="color:var(--coral);"></i> Account Settings</div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Account Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach(['active'=>'Active','inactive'=>'Inactive','suspended'=>'Suspended'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('status', $user->status) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Admin Notes</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                          placeholder="Internal notes about this user (not visible to them)…">{{ old('notes', $user->notes) }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

    </div>

    <div class="card-footer d-flex align-items-center justify-content-between" style="background:var(--bg); padding:1rem 1.4rem;">
        <span style="font-size:.73rem; color:var(--muted);">
            <i class="bi bi-clock me-1"></i>Last updated {{ $user->updated_at->format('M d, Y h:i A') }}
        </span>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-check-circle me-1"></i> Save Changes
            </button>
        </div>
    </div>
</div>
</form>

{{-- Change Password Section --}}
<div class="danger-zone">
    <div class="card-header d-flex align-items-center gap-2" style="padding:.9rem 1.4rem;">
        <i class="bi bi-key-fill" style="color:#C0392B; font-size:1rem;"></i>
        <h6 class="mb-0 fw-bold" style="color:#C0392B;">Change Password</h6>
    </div>
    <div style="padding:1.25rem 1.4rem;">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <input type="hidden" name="name"   value="{{ $user->name }}">
            <input type="hidden" name="email"  value="{{ $user->email }}">
            <input type="hidden" name="status" value="{{ $user->status }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">New Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="newPass"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Leave blank to keep current">
                        <button type="button" class="btn pass-toggle" onclick="togglePass('newPass', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="newPassConfirm"
                               class="form-control" placeholder="Repeat new password">
                        <button type="button" class="btn pass-toggle" onclick="togglePass('newPassConfirm', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-sm"
                        style="background:#C0392B;color:#fff;border:none;border-radius:var(--radius-sm);">
                    <i class="bi bi-key me-1"></i> Update Password
                </button>
            </div>
        </form>
    </div>
</div>

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
</script>
@endpush