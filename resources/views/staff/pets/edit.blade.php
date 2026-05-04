@extends('layouts.app')

@section('title', 'Edit — ' . $pet->name)
@section('page-title', 'Edit Pet')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('staff.pets.index') }}">Pets</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.pets.show', $pet) }}">{{ $pet->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
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
        display: flex; align-items: center; justify-content: center; font-size: .78rem;
        flex-shrink: 0;
    }

    /* ── Photo card ── */
    .photo-preview {
        width: 100%; height: 200px; object-fit: cover;
        border-radius: var(--radius-sm); display: block;
        transition: transform .3s ease;
    }
    .photo-preview:hover { transform: scale(1.02); }
    .photo-placeholder {
        height: 200px; background: var(--coral-light);
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 3rem; cursor: pointer; transition: background .2s;
    }
    .photo-placeholder:hover { background: #EDD8C8; }
    .btn-change-photo {
        display: flex; align-items: center; justify-content: center; gap: .4rem;
        width: 100%; padding: .55rem;
        border: 1.5px dashed var(--border); border-radius: var(--radius-sm);
        background: var(--bg); color: var(--muted); font-size: .83rem;
        cursor: pointer; transition: all .15s;
    }
    .btn-change-photo:hover { border-color: var(--coral); color: var(--coral); background: var(--coral-subtle); }

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
        transform: translateY(-1px); box-shadow: 0 5px 16px rgba(217,119,87,.35);
    }

    /* ════════════════════════════════
       ANIMATIONS
    ════════════════════════════════ */

    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-22px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(22px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes fieldIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes imageReveal {
        from { opacity: 0; transform: scale(.95); }
        to   { opacity: 1; transform: scale(1); }
    }
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(217,119,87,0); }
        50%       { box-shadow: 0 0 0 8px rgba(217,119,87,.2); }
    }

    /* Header bar */
    .header-bar { animation: fadeDown .4s ease both; }

    /* Photo card */
    .card-photo  { opacity: 0; animation: slideInLeft  .45s ease .15s both; }
    .photo-preview, .photo-placeholder { animation: imageReveal .5s ease .25s both; opacity: 0; animation-fill-mode: forwards; }

    /* Form cards stagger from right */
    .card-basic   { opacity: 0; animation: slideInRight .45s ease .2s  both; }
    .card-health  { opacity: 0; animation: slideInRight .45s ease .35s both; }
    .card-desc    { opacity: 0; animation: slideInRight .45s ease .48s both; }
    .card-submit  { opacity: 0; animation: slideInRight .45s ease .60s both; }

    /* Field groups stagger — JS */
    .field-group { opacity: 0; }
    .field-group.visible { animation: fieldIn .35s ease both; }

    /* Save button pulse */
    .btn-save { animation: pulseGlow 2.5s ease 1.5s 2; }
</style>
@endpush

@section('content')

{{-- ── Header bar ── --}}
<div class="header-bar d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('staff.pets.show', $pet) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
    <h5 class="mb-0 fw-bold" style="color:var(--navy);">Edit — {{ $pet->name }}</h5>
</div>

<form action="{{ route('staff.pets.update', $pet) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PATCH')

    <div class="row g-4">

        {{-- ══ LEFT: Photo ══ --}}
        <div class="col-lg-4">
            <div class="card card-photo">
                <div class="section-hdr">
                    <div class="sh-icon"><i class="bi bi-image"></i></div>
                    Pet Photo
                </div>
                <div class="card-body p-4">
                    @if($pet->primary_image)
                        <img src="{{ Storage::url($pet->primary_image) }}"
                             class="photo-preview mb-3" id="previewImg" alt="{{ $pet->name }}">
                    @else
                        <div class="photo-placeholder mb-3" id="previewPlaceholder" onclick="document.getElementById('photoInput').click()">
                            <i class="bi bi-camera" style="color:var(--coral); opacity:.5;"></i>
                        </div>
                        <img src="" class="photo-preview mb-3 d-none" id="previewImg" alt="">
                    @endif

                    <label class="btn-change-photo" for="photoInput">
                        <i class="bi bi-upload"></i> Change Photo
                    </label>
                    <input type="file" name="primary_image" id="photoInput"
                           class="d-none" accept="image/jpeg,image/png,image/webp">
                    <p class="text-center mt-2 mb-0" style="font-size:.72rem; color:var(--muted);">
                        JPG, PNG up to 5MB
                    </p>
                </div>
            </div>
        </div>

        {{-- ══ RIGHT: Form ══ --}}
        <div class="col-lg-8 d-flex flex-column gap-3">

            {{-- Basic Info ── --}}
            <div class="card card-basic">
                <div class="section-hdr">
                    <div class="sh-icon"><i class="bi bi-info-circle"></i></div>
                    Basic Information
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6 field-group" data-idx="0">
                            <label class="form-label">Pet Name <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $pet->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 field-group" data-idx="1">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="male"    @selected(old('gender', $pet->gender) === 'male')>Male</option>
                                <option value="female"  @selected(old('gender', $pet->gender) === 'female')>Female</option>
                                <option value="unknown" @selected(old('gender', $pet->gender) === 'unknown')>Unknown</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 field-group" data-idx="2">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="pet_category_id" id="categorySelect"
                                    class="form-select @error('pet_category_id') is-invalid @enderror" required>
                                <option value="">— Select Category —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('pet_category_id', $pet->pet_category_id) == $cat->id)>
                                        {{ $cat->icon }} {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pet_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 field-group" data-idx="3">
                            <label class="form-label">Breed</label>
                            <select name="breed_id" id="breedSelect" class="form-select">
                                <option value="">Select breed (optional)</option>
                                @foreach($breeds as $breed)
                                    <option value="{{ $breed->id }}"
                                            data-category="{{ $breed->pet_category_id }}"
                                            @selected(old('breed_id', $pet->breed_id) == $breed->id)>
                                        {{ $breed->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 field-group" data-idx="4">
                            <label class="form-label">Age <span class="text-danger">*</span></label>
                            <input type="number" name="age" min="0" step="1"
                                   class="form-control @error('age') is-invalid @enderror"
                                   value="{{ old('age', $pet->age) }}" required>
                            @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 field-group" data-idx="5">
                            <label class="form-label">Age Unit <span class="text-danger">*</span></label>
                            <select name="age_unit" class="form-select">
                                <option value="months" @selected(old('age_unit', $pet->age_unit) === 'months')>Months</option>
                                <option value="years"  @selected(old('age_unit', $pet->age_unit) === 'years')>Years</option>
                            </select>
                        </div>
                        <div class="col-md-4 field-group" data-idx="6">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" name="weight" min="0" step="0.1"
                                   class="form-control" value="{{ old('weight', $pet->weight) }}"
                                   placeholder="e.g. 10.00">
                        </div>
                        <div class="col-md-6 field-group" data-idx="7">
                            <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                            <select name="adoption_fee_type" id="feeType" class="form-select">
                                <option value="fixed"    @selected(old('adoption_fee_type', $pet->adoption_fee_type) === 'fixed')>Fixed Fee</option>
                                <option value="donation" @selected(old('adoption_fee_type', $pet->adoption_fee_type) === 'donation')>Donation</option>
                                <option value="free"     @selected(old('adoption_fee_type', $pet->adoption_fee_type) === 'free')>Free</option>
                            </select>
                        </div>
                        <div class="col-md-6 field-group" data-idx="8" id="feeAmountWrapper">
                            <label class="form-label">Adoption Fee (₱)</label>
                            <input type="number" name="adoption_fee" min="0" step="0.01"
                                   class="form-control @error('adoption_fee') is-invalid @enderror"
                                   value="{{ old('adoption_fee', $pet->adoption_fee) }}"
                                   placeholder="0.00">
                            @error('adoption_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 field-group" data-idx="9">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="available"       @selected(old('status', $pet->status) === 'available')>Available</option>
                                <option value="pending"         @selected(old('status', $pet->status) === 'pending')>Pending</option>
                                <option value="adopted"         @selected(old('status', $pet->status) === 'adopted')>Adopted</option>
                                <option value="under_treatment" @selected(old('status', $pet->status) === 'under_treatment')>Under Treatment</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                                       value="1" id="isVaccinated"
                                       @checked(old('is_vaccinated', $pet->is_vaccinated))>
                                <label class="form-check-label" for="isVaccinated" style="font-size:.855rem;">Vaccinated</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_neutered"
                                       value="1" id="isNeutered"
                                       @checked(old('is_neutered', $pet->is_neutered))>
                                <label class="form-check-label" for="isNeutered" style="font-size:.855rem;">Neutered/Spayed</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_microchipped"
                                       value="1" id="isMicrochipped"
                                       @checked(old('is_microchipped', $pet->is_microchipped))>
                                <label class="form-check-label" for="isMicrochipped" style="font-size:.855rem;">Microchipped</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Special Needs</label>
                            <input type="text" name="special_needs" class="form-control"
                                   value="{{ old('special_needs', $pet->special_needs) }}"
                                   placeholder="Any special requirements or medical conditions...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description ── --}}
            <div class="card card-desc">
                <div class="section-hdr">
                    <div class="sh-icon" style="background:rgba(45,49,71,.07);color:var(--navy);"><i class="bi bi-card-text"></i></div>
                    Description
                </div>
                <div class="card-body p-4">
                    <label class="form-label">About this pet</label>
                    <textarea name="description" class="form-control" rows="4"
                              placeholder="Tell adopters about this pet's personality, history, and what makes them special...">{{ old('description', $pet->description) }}</textarea>
                </div>
            </div>

            {{-- Submit ── --}}
            <div class="card-submit d-flex align-items-center justify-content-between p-3 bg-white rounded-3 border">
                <span style="font-size:.78rem; color:var(--muted);">
                    <i class="bi bi-clock me-1"></i>Last updated {{ $pet->updated_at->format('M d, Y h:i A') }}
                </span>
                <div class="d-flex gap-2">
                    <a href="{{ route('staff.pets.show', $pet) }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-lg"></i> Save Changes
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Image preview on file select ──
    document.getElementById('photoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (ev) => {
            const img = document.getElementById('previewImg');
            const ph  = document.getElementById('previewPlaceholder');
            img.src = ev.target.result;
            img.classList.remove('d-none');
            if (ph) ph.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });

    // ── Breed filter by category ──
    document.getElementById('categorySelect')?.addEventListener('change', function() {
        const catId = this.value;
        document.querySelectorAll('#breedSelect option').forEach(opt => {
            if (!opt.value) { opt.style.display = ''; return; }
            opt.style.display = (opt.dataset.category === catId) ? '' : 'none';
        });
        document.getElementById('breedSelect').value = '';
    });

    // ── Fee amount toggle ──
    function toggleFee() {
        const type    = document.getElementById('feeType')?.value;
        const wrapper = document.getElementById('feeAmountWrapper');
        if (wrapper) wrapper.style.display = type === 'fixed' ? '' : 'none';
    }
    document.getElementById('feeType')?.addEventListener('change', toggleFee);
    toggleFee();

    // ── Stagger field groups ──
    document.querySelectorAll('.field-group').forEach(el => {
        const delay = 400 + (parseInt(el.dataset.idx) * 60);
        setTimeout(() => el.classList.add('visible'), delay);
    });

});
</script>
@endpush