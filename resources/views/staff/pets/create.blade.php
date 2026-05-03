@extends('layouts.app')
@section('title', 'Add New Pet')
@section('page-title', 'Add New Pet')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('staff.pets.index') }}">Pets</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('page-actions')
    <a href="{{ route('staff.pets.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
@endsection

@section('content')
<form action="{{ route('staff.pets.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">

        {{-- ── Left Column ── --}}
        <div class="col-lg-8">

            {{-- Basic Info --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-info-circle me-2"></i>Basic Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pet Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="e.g. Buddy" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="pet_category_id" id="categorySelect"
                                    class="form-select @error('pet_category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('pet_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->icon }} {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pet_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Breed</label>
                            <select name="breed_id" id="breedSelect" class="form-select">
                                <option value="">Select breed (optional)</option>
                                @foreach($breeds as $breed)
                                    <option value="{{ $breed->id }}"
                                            data-category="{{ $breed->pet_category_id }}"
                                            {{ old('breed_id') == $breed->id ? 'selected' : '' }}>
                                        {{ $breed->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="unknown" {{ old('gender','unknown')=='unknown'?'selected':'' }}>Unknown</option>
                                <option value="male" {{ old('gender')=='male'?'selected':'' }}>Male</option>
                                <option value="female" {{ old('gender')=='female'?'selected':'' }}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth"
                                   class="form-control @error('date_of_birth') is-invalid @enderror"
                                   value="{{ old('date_of_birth') }}" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" name="weight" step="0.01"
                                   class="form-control @error('weight') is-invalid @enderror"
                                   value="{{ old('weight') }}" placeholder="0.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Size</label>
                            <select name="size" class="form-select">
                                <option value="">Select size</option>
                                <option value="tiny" {{ old('size')=='tiny'?'selected':'' }}>Tiny</option>
                                <option value="small" {{ old('size')=='small'?'selected':'' }}>Small</option>
                                <option value="medium" {{ old('size')=='medium'?'selected':'' }}>Medium</option>
                                <option value="large" {{ old('size')=='large'?'selected':'' }}>Large</option>
                                <option value="extra_large" {{ old('size')=='extra_large'?'selected':'' }}>Extra Large</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Color/Markings</label>
                            <input type="text" name="color" class="form-control"
                                   value="{{ old('color') }}" placeholder="e.g. Brown with white spots">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"
                                      placeholder="Tell us about this pet's personality, habits, and background...">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Special Needs</label>
                            <textarea name="special_needs" class="form-control" rows="2"
                                      placeholder="Any special care requirements or medical needs...">{{ old('special_needs') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Health Info --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-heart-pulse me-2"></i>Health Information</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_vaccinated"
                                       id="vaccinated" value="1" {{ old('is_vaccinated') ? 'checked' : '' }}>
                                <label class="form-check-label" for="vaccinated">Vaccinated</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_neutered"
                                       id="neutered" value="1" {{ old('is_neutered') ? 'checked' : '' }}>
                                <label class="form-check-label" for="neutered">Neutered/Spayed</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_microchipped"
                                       id="microchipped" value="1" {{ old('is_microchipped') ? 'checked' : '' }}>
                                <label class="form-check-label" for="microchipped">Microchipped</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Adoption Fee --}}
            <div class="card">
                <div class="card-header"><i class="bi bi-cash me-2"></i>Adoption Fee</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                            <select name="adoption_fee_type" id="feeType" class="form-select" required>
                                <option value="fixed" {{ old('adoption_fee_type','fixed')=='fixed'?'selected':'' }}>Fixed Amount</option>
                                <option value="donation" {{ old('adoption_fee_type')=='donation'?'selected':'' }}>Donation Based</option>
                                <option value="free" {{ old('adoption_fee_type')=='free'?'selected':'' }}>Free</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="feeAmountWrapper">
                            <label class="form-label">Amount (₱)</label>
                            <input type="number" name="adoption_fee" step="0.01"
                                   class="form-control @error('adoption_fee') is-invalid @enderror"
                                   value="{{ old('adoption_fee', 0) }}" placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Right Column ── --}}
        <div class="col-lg-4">

            {{-- Photo Upload --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-camera me-2"></i>Photos</div>
                <div class="card-body">
                    <label class="form-label">Primary Photo <span class="text-danger">*</span></label>
                    <div id="imagePreviewWrapper" class="mb-3">
                        <img id="imagePreview" src="" alt=""
                             style="width:100%;height:200px;object-fit:cover;border-radius:10px;display:none;border:2px solid var(--paws-border);">
                        <div id="uploadPlaceholder"
                             style="width:100%;height:200px;border:2px dashed var(--paws-border);border-radius:10px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;color:var(--paws-muted);">
                            <i class="bi bi-image" style="font-size:2rem;margin-bottom:.5rem;"></i>
                            <small>Click to upload photo</small>
                            <small style="font-size:.7rem;margin-top:.25rem;">JPEG, PNG, WebP — max 2MB</small>
                        </div>
                    </div>
                    <input type="file" name="primary_image" id="primaryImage"
                           class="form-control @error('primary_image') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/webp" required>
                    @error('primary_image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <div class="mt-3">
                        <label class="form-label">Additional Photos</label>
                        <input type="file" name="images[]" class="form-control" multiple
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Upload up to 5 additional photos</div>
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-toggle-on me-2"></i>Listing Status</div>
                <div class="card-body">
                    <div class="alert alert-info mb-2" style="font-size:.8rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        New pets require admin approval before appearing publicly.
                    </div>
                    <select name="status" class="form-select">
                        <option value="available" selected>Available</option>
                        <option value="not_available">Not Available</option>
                        <option value="under_treatment">Under Treatment</option>
                    </select>
                </div>
            </div>

            {{-- Submit --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add Pet
                </button>
                <a href="{{ route('staff.pets.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Image preview
document.getElementById('primaryImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (ev) => {
        document.getElementById('imagePreview').src = ev.target.result;
        document.getElementById('imagePreview').style.display = 'block';
        document.getElementById('uploadPlaceholder').style.display = 'none';
    };
    reader.readAsDataURL(file);
});

document.getElementById('uploadPlaceholder').addEventListener('click', () => {
    document.getElementById('primaryImage').click();
});

// Breed filter by category
document.getElementById('categorySelect').addEventListener('change', function() {
    const catId = this.value;
    document.querySelectorAll('#breedSelect option').forEach(opt => {
        if (!opt.value) { opt.style.display = ''; return; }
        opt.style.display = (opt.dataset.category === catId) ? '' : 'none';
    });
    document.getElementById('breedSelect').value = '';
});

// Fee amount toggle
document.getElementById('feeType').addEventListener('change', function() {
    const wrapper = document.getElementById('feeAmountWrapper');
    wrapper.style.display = this.value === 'fixed' ? '' : 'none';
});
document.getElementById('feeType').dispatchEvent(new Event('change'));
</script>
@endpush