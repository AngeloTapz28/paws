@extends('layouts.app')
@section('title', 'Edit Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row g-4">
    {{-- Profile Form --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-person-circle me-2 text-primary"></i>Personal Information</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    {{-- Avatar Upload --}}
                    <div class="mb-4 text-center">
                        <div class="position-relative d-inline-block">
                            @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" id="avatarPreview"
                                 class="rounded-circle border shadow-sm"
                                 style="width:100px;height:100px;object-fit:cover">
                            @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center
                                        justify-content-center shadow-sm" id="avatarPreview"
                                 style="width:100px;height:100px;font-size:2rem">
                                {{ strtoupper(substr($user->first_name,0,1)) }}
                            </div>
                            @endif
                            <label for="avatarInput" class="position-absolute bottom-0 end-0
                                btn btn-sm btn-primary rounded-circle p-1" style="cursor:pointer">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                        </div>
                        <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/*">
                        <div class="text-muted small mt-2">JPG, PNG, WebP — max 2MB</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">First Name</label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                value="{{ old('first_name', $user->first_name) }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Name</label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                value="{{ old('last_name', $user->last_name) }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                            <div class="form-text">Email cannot be changed here.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $user->phone) }}" placeholder="+63 9XX XXX XXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Prefer not to say</option>
                                <option value="male" @selected($user->gender === 'male')>Male</option>
                                <option value="female" @selected($user->gender === 'female')>Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="dob" class="form-control"
                                value="{{ old('dob', optional($user->dob)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control" rows="2"
                                placeholder="Street, Barangay, City, Province">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('profile.show') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-shield-lock me-2 text-warning"></i>Change Password</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Current Password</label>
                            <input type="password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">New Password</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3 pt-3 border-top">
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-key me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Right sidebar: Account Info --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4">
            @if($user->avatar)
            <img src="{{ Storage::url($user->avatar) }}"
                 class="rounded-circle mx-auto mb-3 border shadow-sm"
                 style="width:80px;height:80px;object-fit:cover">
            @else
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center
                        mx-auto mb-3 shadow-sm" style="width:80px;height:80px;font-size:1.8rem">
                {{ strtoupper(substr($user->first_name,0,1)) }}
            </div>
            @endif
            <h5 class="fw-bold mb-1">{{ $user->full_name }}</h5>
            <div class="text-muted small mb-3">{{ $user->email }}</div>
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                @foreach($user->roles as $role)
                <span class="badge bg-{{ $role->color ?? 'primary' }}">{{ $role->display_name }}</span>
                @endforeach
            </div>
            <hr>
            <div class="text-start text-muted small">
                <div class="d-flex justify-content-between py-1">
                    <span>Member since</span>
                    <span class="text-dark">{{ $user->created_at->format('M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span>Account status</span>
                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}-subtle
                                  text-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
                @if($user->last_login_at)
                <div class="d-flex justify-content-between py-1">
                    <span>Last login</span>
                    <span class="text-dark">{{ $user->last_login_at->diffForHumans() }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(ev) {
        const prev = document.getElementById('avatarPreview');
        if (prev.tagName === 'IMG') {
            prev.src = ev.target.result;
        } else {
            // Replace div with img
            const img = document.createElement('img');
            img.src = ev.target.result;
            img.id = 'avatarPreview';
            img.className = 'rounded-circle border shadow-sm';
            img.style = 'width:100px;height:100px;object-fit:cover';
            prev.replaceWith(img);
        }
    };
    reader.readAsDataURL(file);
});
</script>
@endpush