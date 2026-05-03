@extends('layouts.app')

@section('title', 'Edit — ' . $pet->name)
@section('page-title', 'Edit Pet')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('staff.pets.index') }}">Pets</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.pets.show', $pet) }}">{{ $pet->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('staff.pets.show', $pet) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
    <h5 class="mb-0 fw-bold" style="color:var(--navy);">Edit — {{ $pet->name }}</h5>
</div>

<form action="{{ route('staff.pets.update', $pet) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    <div class="row g-4">

        {{-- LEFT: Image --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-image me-2" style="color:var(--coral);"></i>Pet Photo
                </div>
                <div class="card-body text-center p-4">
                    @if($pet->primary_image)
                        <img src="{{ Storage::url($pet->primary_image) }}"
                             class="rounded-3 w-100 mb-3" style="height:200px;object-fit:cover;" id="preview-img">
                    @else
                        <div class="rounded-3 d-flex align-items-center justify-content-center mb-3"
                             style="height:200px;background:var(--coral-light);" id="preview-placeholder">
                            <i class="bi bi-camera" style="font-size:2.5rem;color:var(--coral);opacity:.5;"></i>
                        </div>
                        <img src="" class="rounded-3 w-100 mb-3 d-none" style="height:200px;object-fit:cover;" id="preview-img">
                    @endif

                    <label class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-upload me-1"></i> Change Photo
                        <input type="file" name="primary_image" class="d-none" accept="image/*" id="photoInput">
                    </label>
                    <div class="form-text mt-1">JPG, PNG up to 5MB</div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Form fields --}}
        <div class="col-lg-8 d-flex flex-column gap-3">

            {{-- Basic Info --}}
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2" style="color:var(--coral);"></i>Basic Information
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pet Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $pet->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="male"   @selected(old('gender', $pet->gender) === 'male')>Male</option>
                                <option value="female" @selected(old('gender', $pet->gender) === 'female')>Female</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="pet_category_id" class="form-select @error('pet_category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        @selected(old('pet_category_id', $pet->pet_category_id) == $cat->id)>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pet_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Breed</label>
                            <select name="breed_id" class="form-select @error('breed_id') is-invalid @enderror">
                                <option value="">Select Breed</option>
                                @foreach($breeds as $breed)
                                    <option value="{{ $breed->id }}"
                                        @selected(old('breed_id', $pet->breed_id) == $breed->id)>
                                        {{ $breed->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('breed_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Age <span class="text-danger">*</span></label>
                            <input type="number" name="age" class="form-control @error('age') is-invalid @enderror"
                                   value="{{ old('age', $pet->age) }}" min="0" required>
                            @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Age Unit <span class="text-danger">*</span></label>
                            <select name="age_unit" class="form-select @error('age_unit') is-invalid @enderror" required>
                                <option value="months" @selected(old('age_unit', $pet->age_unit) === 'months')>Months</option>
                                <option value="years"  @selected(old('age_unit', $pet->age_unit) === 'years')>Years</option>
                            </select>
                            @error('age_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" name="weight" class="form-control @error('weight') is-invalid @enderror"
                                   value="{{ old('weight', $pet->weight) }}" step="0.01" min="0">
                            @error('weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Adoption Fee (₱) <span class="text-danger">*</span></label>
                            <input type="number" name="adoption_fee" class="form-control @error('adoption_fee') is-invalid @enderror"
                                   value="{{ old('adoption_fee', $pet->adoption_fee) }}" step="0.01" min="0" required>
                            @error('adoption_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="available"       @selected(old('status', $pet->status) === 'available')>Available</option>
                                <option value="pending"         @selected(old('status', $pet->status) === 'pending')>Pending</option>
                                <option value="adopted"         @selected(old('status', $pet->status) === 'adopted')>Adopted</option>
                                <option value="under_treatment" @selected(old('status', $pet->status) === 'under_treatment')>Under Treatment</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" placeholder="Tell adopters about this pet...">{{ old('description', $pet->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Health Info --}}
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-heart-pulse me-2" style="color:var(--coral);"></i>Health Information
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_vaccinated"
                                       value="1" id="vaccinated"
                                       {{ old('is_vaccinated', $pet->is_vaccinated) ? 'checked' : '' }}>
                                <label class="form-check-label" for="vaccinated">Vaccinated</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_neutered"
                                       value="1" id="neutered"
                                       {{ old('is_neutered', $pet->is_neutered) ? 'checked' : '' }}>
                                <label class="form-check-label" for="neutered">Neutered / Spayed</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Microchip Number</label>
                            <input type="text" name="microchip_number" class="form-control"
                                   value="{{ old('microchip_number', $pet->microchip_number) }}"
                                   placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color / Markings</label>
                            <input type="text" name="color" class="form-control"
                                   value="{{ old('color', $pet->color) }}"
                                   placeholder="e.g. Brown with white spots">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('staff.pets.show', $pet) }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Save Changes
                </button>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.getElementById('photoInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
            const img = document.getElementById('preview-img');
            const placeholder = document.getElementById('preview-placeholder');
            img.src = ev.target.result;
            img.classList.remove('d-none');
            if (placeholder) placeholder.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush