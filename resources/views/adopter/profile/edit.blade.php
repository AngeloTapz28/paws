@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header fw-semibold">
            <i class="bi bi-person-circle me-2 text-primary"></i>Personal Information
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('adopter.profile.update') }}">
                @csrf @method('PATCH')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control bg-light"
                               value="{{ $user->email }}" disabled>
                        <div class="form-text">Email cannot be changed.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="+63 9XX XXX XXXX">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Prefer not to say</option>
                            <option value="male" @selected($user->gender === 'male')>Male</option>
                            <option value="female" @selected($user->gender === 'female')>Female</option>
                            <option value="other" @selected($user->gender === 'other')>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control"
                               value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                               max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" class="form-control" rows="2"
                                  placeholder="Street, Barangay, City, Province">{{ old('address', $user->address) }}</textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
@endsection