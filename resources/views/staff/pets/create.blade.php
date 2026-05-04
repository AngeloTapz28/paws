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

@push('styles')
<style>
    /* ── Section header ── */
    .section-hdr {
        display: flex; align-items: center; gap: .5rem;
        font-size: .78rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: var(--muted);
        padding: .85rem 1.25rem; border-bottom: 1px solid var(--border);
    }
    .section-hdr .sh-icon {
        width: 26px; height: 26px; border-radius: 7px;
        background: var(--coral-subtle); color: var(--coral);
        display: flex; align-items: center; justify-content: center;
        font-size: .78rem; flex-shrink: 0;
    }

    /* ── Form controls ── */
    .form-label { font-size: .8rem; font-weight: 600; color: var(--navy-mid); margin-bottom: .35rem; }
    .form-control, .form-select {
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        font-size: .875rem; transition: border-color .2s, box-shadow .2s, transform .15s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--coral); box-shadow: 0 0 0 3px rgba(217,119,87,.15);
        outline: none; transform: translateY(-1px);
    }
    .form-check-input:checked { background-color: var(--coral); border-color: var(--coral); }

    /* ── Upload placeholder ── */
    .upload-placeholder {
        width: 100%; height: 200px;
        border: 2px dashed var(--border); border-radius: var(--radius-sm);
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; cursor: pointer; color: var(--muted);
        transition: border-color .2s, background .2s, color .2s;
    }
    .upload-placeholder:hover { border-color: var(--coral); background: var(--coral-subtle); color: var(--coral); }

    /* ── Submit buttons ── */
    .btn-add-pet {
        background: var(--coral); color: #fff; border: none;
        border-radius: var(--radius-sm); padding: .7rem;
        font-size: .9rem; font-weight: 700; width: 100%;
        transition: background .2s, transform .15s, box-shadow .2s;
        display: flex; align-items: center; justify-content: center; gap: .45rem;
    }
    .btn-add-pet:hover {
        background: var(--coral-dark); color: #fff;
        transform: translateY(-1px); box-shadow: 0 5px 16px rgba(217,119,87,.35);
    }

    /* ── Info alert ── */
    .approval-notice {
        background: var(--gold-light); color: #7A5A1A;
        border-radius: var(--radius-sm); padding: .65rem .9rem;
        font-size: .8rem; display: flex; align-items: flex-start; gap: .5rem;
        border: 1px solid #DBBF72; margin-bottom: .75rem;
    }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes slideInLeft  { from { opacity:0; transform:translateX(-20px); } to { opacity:1; transform:translateX(0); } }
    @keyframes slideInRight { from { opacity:0; transform:translateX(20px);  } to { opacity:1; transform:translateX(0); } }
    @keyframes fadeUp       { from { opacity:0; transform:translateY(14px);  } to { opacity:1; transform:translateY(0); } }
    @keyframes pulseGlow    { 0%,100% { box-shadow:0 0 0 0 rgba(217,119,87,0); } 50% { box-shadow:0 0 0 8px rgba(217,119,87,.2); } }

    .card-basic   { opacity:0; animation: slideInLeft  .45s ease .1s  both; }
    .card-health  { opacity:0; animation: slideInLeft  .45s ease .25s both; }
    .card-fee     { opacity:0; animation: slideInLeft  .45s ease .4s  both; }
    .card-photo   { opacity:0; animation: slideInRight .45s ease .15s both; }
    .card-status  { opacity:0; animation: slideInRight .45s ease .3s  both; }
    .card-actions { opacity:0; animation: fadeUp       .45s ease .45s both; }
    .btn-add-pet  { animation: pulseGlow 2.5s ease 1.2s 2; }
</style>
@endpush

@section('content')
<form action="{{ route('staff.pets.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">

        {{-- ══ LEFT COLUMN ══ --}}
        <div class="col-lg-8 d-flex flex-column gap-3">

            {{-- Basic Info ── --}}
            <div class="card card-basic">
                <div class="section-hdr">
                    <div class="sh-icon"><i class="bi bi-info-circle"></i></div>
                    Basic Information
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Pet Name <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="e.g. Buddy" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="pet_category_id" id="categorySelect"
                                    class="form-select @error('pet_category_id') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('pet_category_id') == $cat->id)>
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
                                            @selected(old('breed_id') == $breed->id)>
                                        {{ $breed->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="unknown" @selected(old('gender','unknown') === 'unknown')>Unknown</option>
                                <option value="male"    @selected(old('gender') === 'male')>Male</option>
                                <option value="female"  @selected(old('gender') === 'female')>Female</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth"
                                   class="form-control @error('date_of_birth') is-invalid @enderror"
                                   value="{{ old('date_of_birth') }}" max="{{ date('Y-m-d') }}">
                            @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                @foreach(['tiny','small','medium','large','extra_large'] as $s)
                                    <option value="{{ $s }}" @selected(old('size') === $s)>
                                        {{ ucfirst(str_replace('_',' ',$s)) }}
                                    </option>
                                @endforeach
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

            {{-- Health Info ── --}}
            <div class="card card-health">
                <div class="section-hdr">
                    <div class="sh-icon" style="background:var(--sage-light);color:var(--sage);"><i class="bi bi-heart-pulse"></i></div>
                    Health Information
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_vaccinated"
                                       id="vaccinated" value="1" @checked(old('is_vaccinated'))>
                                <label class="form-check-label" for="vaccinated" style="font-size:.855rem;">Vaccinated</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_neutered"
                                       id="neutered" value="1" @checked(old('is_neutered'))>
                                <label class="form-check-label" for="neutered" style="font-size:.855rem;">Neutered/Spayed</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_microchipped"
                                       id="microchipped" value="1" @checked(old('is_microchipped'))>
                                <label class="form-check-label" for="microchipped" style="font-size:.855rem;">Microchipped</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Adoption Fee ── --}}
            <div class="card card-fee">
                <div class="section-hdr">
                    <div class="sh-icon" style="background:var(--gold-light);color:#B8892A;"><i class="bi bi-cash-coin"></i></div>
                    Adoption Fee
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                            <select name="adoption_fee_type" id="feeType" class="form-select" required>
                                <option value="fixed"    @selected(old('adoption_fee_type','fixed') === 'fixed')>Fixed Amount</option>
                                <option value="donation" @selected(old('adoption_fee_type') === 'donation')>Donation Based</option>
                                <option value="free"     @selected(old('adoption_fee_type') === 'free')>Free</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="feeAmountWrapper">
                            <label class="form-label">Amount (₱)</label>
                            <input type="number" name="adoption_fee" step="0.01"
                                   class="form-control @error('adoption_fee') is-invalid @enderror"
                                   value="{{ old('adoption_fee', 0) }}" placeholder="0.00">
                            @error('adoption_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ══ RIGHT COLUMN ══ --}}
        <div class="col-lg-4 d-flex flex-column gap-3">

            {{-- Photo Upload ── --}}
            <div class="card card-photo">
                <div class="section-hdr">
                    <div class="sh-icon"><i class="bi bi-camera"></i></div>
                    Photos
                </div>
                <div class="card-body p-4">
                    <label class="form-label">Primary Photo <span class="text-danger">*</span></label>

                    <div id="imagePreviewWrapper" class="mb-3">
                        <img id="imagePreview" src="" alt=""
                             style="width:100%;height:200px;object-fit:cover;border-radius:var(--radius-sm);display:none;border:2px solid var(--border);">
                        <div id="uploadPlaceholder" class="upload-placeholder" onclick="document.getElementById('primaryImage').click()">
                            <i class="bi bi-image" style="font-size:2rem; margin-bottom:.5rem;"></i>
                            <small style="font-weight:600;">Click to upload photo</small>
                            <small style="font-size:.72rem; margin-top:.25rem; color:var(--muted);">JPEG, PNG, WebP — max 2MB</small>
                        </div>
                    </div>

                    <input type="file" name="primary_image" id="primaryImage"
                           class="form-control @error('primary_image') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/webp" required>
                    @error('primary_image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <div class="mt-3">
                        <label class="form-label">Additional Photos <span style="color:var(--muted); font-weight:400;">(optional)</span></label>
                        <input type="file" name="images[]" class="form-control" multiple
                               accept="image/jpeg,image/png,image/webp">
                        <div style="font-size:.72rem; color:var(--muted); margin-top:.3rem;">Upload up to 5 additional photos</div>
                    </div>
                </div>
            </div>

            {{-- Listing Status ── --}}
            <div class="card card-status">
                <div class="section-hdr">
                    <div class="sh-icon" style="background:var(--sage-light);color:var(--sage);"><i class="bi bi-toggle-on"></i></div>
                    Listing Status
                </div>
                <div class="card-body p-4">
                    <div class="approval-notice">
                        <i class="bi bi-info-circle-fill mt-1" style="flex-shrink:0;"></i>
                        New pets require admin approval before appearing publicly.
                    </div>
                    <select name="status" class="form-select">
                        <option value="available" selected>Available</option>
                        <option value="not_available">Not Available</option>
                        <option value="under_treatment">Under Treatment</option>
                    </select>
                </div>
            </div>

            {{-- Submit ── --}}
            <div class="card-actions d-flex flex-column gap-2">
                <button type="submit" class="btn-add-pet">
                    <i class="bi bi-plus-circle"></i> Add Pet
                </button>
                <a href="{{ route('staff.pets.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Image preview ──
    document.getElementById('primaryImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (ev) => {
            const img = document.getElementById('imagePreview');
            const ph  = document.getElementById('uploadPlaceholder');
            img.src = ev.target.result;
            img.style.display = 'block';
            ph.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });

    document.getElementById('uploadPlaceholder').addEventListener('click', () => {
        document.getElementById('primaryImage').click();
    });

    // ── Breed filter by category ──
    document.getElementById('categorySelect').addEventListener('change', function() {
        const catId = this.value;
        document.querySelectorAll('#breedSelect option').forEach(opt => {
            if (!opt.value) { opt.style.display = ''; return; }
            opt.style.display = (opt.dataset.category === catId) ? '' : 'none';
        });
        document.getElementById('breedSelect').value = '';
    });

    // ── Fee amount toggle ──
    function toggleFee() {
        const wrapper = document.getElementById('feeAmountWrapper');
        if (wrapper) wrapper.style.display = document.getElementById('feeType').value === 'fixed' ? '' : 'none';
    }
    document.getElementById('feeType').addEventListener('change', toggleFee);
    toggleFee();

});
</script>
@endpush